<?php
/**
 * @package Software-Hub
 * @version 0.1
 */
/*
Plugin Name: Software Hub
Plugin URI: http://sh.niftiestsoftware.com
Description: Manages software projects
Author: Simon Cadman
Version: 0.1
Author URI: http://www.niftiestsoftware.com/
*/
/*  Copyright 2012 Simon Cadman (src@niftiestsoftware.com)

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function software_hub_get_software_instances ( ) {
    global $wpdb;
    $instances = $wpdb->get_results(
        "SELECT `id`, `name` FROM {$wpdb->prefix}software_hub_software ORDER BY `name` ASC"
    );
    
    return $instances;
}

function software_hub_add_pages() {
    add_submenu_page( 'options-general.php', 'Software Hub', 'Software Hub', 'manage_options', 'software_hub_menu', 'software_hub_options' );
}

function software_hub_options () {
    global $wpdb;
    $errors = array();
    
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['software_hub_backend_page_type'] == 'hub' ) {
        if ( isset( $_POST['software_hub_new'] ) && strlen( $_POST['software_hub_new'] ) > 0 ) {
            $wpdb->insert( $wpdb->prefix . "software_hub_software", array( 'name' => $_POST['software_hub_new'] ) );
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['software_hub_backend_page_type'] == 'delete_software'  && isset( $_POST['software_id'] ) ) {
        $wpdb->delete( $wpdb->prefix . "software_hub_software", array('id' => $_POST['software_id'] ) );
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['software_hub_backend_page_type'] == 'populate-changes'  && isset( $_POST['software_id'] ) ) {
        $unassignedChanges = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_changelog where software_id = %s and software_release_id = 0 order by time desc", $_POST['software_id'] )
        );
        foreach ( $unassignedChanges as $change ) {
            $release = $wpdb->get_row(
                $wpdb->prepare("select id from `{$wpdb->prefix}software_hub_software_release` where software_id = %s and time > %s order by time asc limit 1", $_POST['software_id'], $change->time )
            );
            if ( isset($release->id) ) {
                $wpdb->update( $wpdb->prefix . "software_hub_changelog", array('software_release_id' => $release->id), array( 'id' => $change->id ) );
            }
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['software_hub_backend_page_type'] == 'sync'  && isset( $_POST['software_id'] ) ) {
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Listener/ListenerInterface.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Client/ClientInterface.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Client/AbstractClient.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Client/AbstractCurl.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Client/Curl.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Message/MessageInterface.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Message/AbstractMessage.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Message/Response.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Util/Url.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Message/RequestInterface.php');
        require_once(dirname(__FILE__) . '/lib/buzz/lib/Buzz/Message/Request.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/HttpClient/Listener/AuthListener.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/HttpClient/Listener/ErrorListener.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/Api/ApiInterface.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/Api/AbstractApi.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/Api/Repository/Commits.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/Api/Repo.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/Client.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/HttpClient/Message/Request.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/HttpClient/Message/Response.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/HttpClient/HttpClientInterface.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/HttpClient/HttpClient.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/Exception/RuntimeException.php');
        require_once(dirname(__FILE__) . '/lib/php-github-api/lib/Github/Exception/ApiLimitExceedException.php');
        $githubclient = new Github\Client();
        $commits = array();
        $software = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_software where id = %s limit 1", $_POST['software_id'] )
        );
        try {
            $lastsha = 'master';
            
            $i=0;
            while ( $i < 1000 ) {
                $i++;
                $currentcommits = $githubclient->api('repo')->commits()->all($software->github_user, $software->github_repository, array('sha' => $lastsha, 'per_page' => 100));
                if ( count($currentcommits) > 0 ) {
                    $commits = array_merge($commits, $currentcommits);
                    $lastcommit = array_pop($currentcommits);
                    $lastsha = $lastcommit['sha'];
                    if ( count($currentcommits) == 0 ) {
                        break;
                    }
                } else {
                    break;
                }
            }
        } catch ( RuntimeException $e ) {
            $errors[] = $e->getMessage();
        } catch ( ApiLimitExceedException $e ) {
            $errors[] = $e->getMessage();
        }
        foreach($commits as $commit ) {
            $id = $commit['sha'];
            $commitItem = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_changelog where software_id = %s and commit = %s limit 1", $_POST['software_id'], $id )
            );
            if ( is_null($commitItem) ) {
                $wpdb->insert( $wpdb->prefix . "software_hub_changelog", array( 'commit' => $id,
                                                                                'note' => $commit['commit']['message'],
                                                                                'display_message' => $commit['commit']['message'],
                                                                                'software_id' => $_POST['software_id'],
                                                                                'time' => gmdate('Y-m-d H:i:s', strtotime($commit['commit']['committer']['date']) )) );
            }
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['software_id'] ) ) {
        $newfields = array();
        $doUpdate = true;
        if ( $_POST['software_hub_backend_page_type'] == 'overview' ) {
            if ( isset( $_POST['software_hub_overview_enabled'] ) ) {
                $newfields['overview_enabled'] = $_POST['software_hub_overview_enabled'] === 'on';
            } else {
                $newfields['overview_enabled'] = 0;
            }
            $newfields['overview'] = stripslashes($_POST['software_hub_overview_text']);
            $newfields['github_user'] = $_POST['software_hub_github_user'];
            $newfields['github_repository'] = $_POST['software_hub_github_repository'];
            $newfields['download_url_prefix'] = $_POST['software_hub_download_url_prefix'];
        }else if ( $_POST['software_hub_backend_page_type'] == 'changelog' ) {
            if ( isset( $_POST['software_hub_changelog_enabled'] ) ) {
                $newfields['changelog_enabled'] = $_POST['software_hub_changelog_enabled'] === 'on';
            } else {
                $newfields['changelog_enabled'] = 0;
            }
            $newfields['changelog'] = stripslashes($_POST['software_hub_changelog_text']);
        } else if ( $_POST['software_hub_backend_page_type'] == 'changes' ) {
            $doUpdate = false;
            $changeitems = array();
            foreach ($_POST['software_hub_change_display_message'] as $id => $message) {
                $changeitems[$id]['display_message'] = $message;
                if ( isset($_POST['software_hub_change_live'][$id]  ) ) {
                    $changeitems[$id]['live'] = $_POST['software_hub_change_live'][$id] == 'on';
                } else {
                    $changeitems[$id]['live'] = 0;
                }
            }
            foreach ( $changeitems as $key => $item ) {
                $wpdb->update( $wpdb->prefix . "software_hub_changelog", $item, array('id' => $key ) );
            }
            
        } else if ( $_POST['software_hub_backend_page_type'] == 'installation' ) {
            if ( isset( $_POST['software_hub_installation_enabled'] ) ) {
                $newfields['installation_enabled'] = $_POST['software_hub_installation_enabled'] === 'on';
            } else {
                $newfields['installation_enabled'] = 0;
            }
            $newfields['installation'] = stripslashes($_POST['software_hub_installation_text']);
        } else if ( $_POST['software_hub_backend_page_type'] == 'install' ) {
            $doUpdate = false;
            $live = 0;
            if ( isset($_POST['software_hub_install_live']) && $_POST['software_hub_install_live'] == 'on' ) {
                $live = 1;
            }
            $data = array( 'os_group_id' => $_POST['os_group_id'],
                           'software_id' => $_POST['software_id'],
                           'content' => stripslashes($_POST['software_hub_install']),
                           'live' => $live);
            $foundinstall = $wpdb->get_row(
                $wpdb->prepare("SELECT count(id) as count FROM {$wpdb->prefix}software_hub_install where software_id = %s and os_group_id = %s limit 1", $_POST['software_id'], $_POST['os_group_id'] )
            );
            if ( $foundinstall->count > 0 ) {
                $wpdb->update( $wpdb->prefix . "software_hub_install", $data, array('software_id' => $_POST['software_id'], 'os_group_id' => $_POST['os_group_id']) );
            } else {
                $wpdb->insert( $wpdb->prefix . "software_hub_install", $data );
            }
        } else if ( $_POST['software_hub_backend_page_type'] == 'configuration' ) {
            if ( isset( $_POST['software_hub_configuration_enabled'] ) ) {
                $newfields['configuration_enabled'] = $_POST['software_hub_configuration_enabled'] === 'on';
            } else {
                $newfields['configuration_enabled'] = 0;
            }
            $newfields['configuration'] = stripslashes($_POST['software_hub_configuration_text']);
        } else if ( $_POST['software_hub_backend_page_type'] == 'issues' ) {
            if ( isset( $_POST['software_hub_issues_enabled'] ) ) {
                $newfields['issues_enabled'] = $_POST['software_hub_issues_enabled'] === 'on';
            } else {
                $newfields['issues_enabled'] = 0;
            }
            $newfields['issues'] = stripslashes($_POST['software_hub_issues_text']);
        }
        
        if ( $doUpdate ) {
            $wpdb->update( $wpdb->prefix . "software_hub_software", $newfields, array( 'id' => $_POST['software_id'] ) );
        }
    }
    if ( isset($_GET['tab']) ) {
        $software = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_software where id = %s limit 1", $_GET['tab'] )
        );
            
        if ( isset( $_GET['tab2'] ) && $_GET['tab2'] == 'releases' ) {
            $releases = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_software_release where software_id = %s order by time desc", $_GET['tab'] )
            );
        }
            
        if ( isset( $_GET['tab2'] ) && $_GET['tab2'] == 'changes' ) {
            $changes = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_changelog where software_id = %s order by time desc", $_GET['tab'] )
            );
            
            $githubweb = 'https://github.com/'.$software->github_user.'/'.$software->github_repository;
        }
        
        if ( isset( $_GET['tab2'] ) && $_GET['tab2'] == 'install' ) {
            $osgroups = $wpdb->get_results(
                "SELECT * FROM {$wpdb->prefix}software_hub_os_group order by display_order asc "
            );
            
            $osgroupid = $osgroups[0]->id;
            
            if ( isset($_GET['tab3']) ) {
                $osgroupid = $_GET['tab3'];
            }
                
            $installtext = '';
            if ( isset($osgroupid) ) {
                $install = $wpdb->get_row(
                    $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_install where software_id = %s and os_group_id = %s ", $_GET['tab'], $osgroupid )
                );
                if ( isset($install->content) ) {
                    $installtext = $install->content;
                }
            }
        }
    }
    
    $softwareInstances = software_hub_get_software_instances();
    require_once(dirname(__FILE__) . '/admin-options.php');
}

function software_hub_changes ( $releaseid ) {
    //software_release_id
    global $wpdb;
    return $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_changelog where software_release_id = %s", $releaseid )
    );
}

function software_hub_view ( $params ) {
    if ( isset($params) && is_array($params) && isset( $params['id'] ) ) {
        global $wpdb;
        $software = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_software where id = %s limit 1", $params['id'] )
        );
        
        if ( !is_null($software) ) {
            wp_enqueue_script('software_hub', '/wp-content/plugins/software-hub/js/software-hub.js');
            wp_enqueue_style('software_hub', '/wp-content/plugins/software-hub/css/software-hub.css');
            global $wpdb;
            $osgroups = $wpdb->get_results($wpdb->prepare("SELECT *, 
                                            ( select count(id) from {$wpdb->prefix}software_hub_os_group as childgroup 
                                            where childgroup.parent_id = {$wpdb->prefix}software_hub_os_group.id or childgroup.id = {$wpdb->prefix}software_hub_os_group.id ) 
                                            as child_count 
                                            FROM {$wpdb->prefix}software_hub_os_group 
                                            inner join {$wpdb->prefix}software_hub_install on {$wpdb->prefix}software_hub_os_group.id =  {$wpdb->prefix}software_hub_install.os_group_id 
                                            where parent_id = 0 
                                            and {$wpdb->prefix}software_hub_install.software_id = %s 
                                            order by display_order asc", $params['id'] )
            );
                                            
            foreach ( $osgroups as $osgroup ) {
                $osgroup->oses = $wpdb->get_results( 
                                    $wpdb->prepare("SELECT *, ( select group_concat(' ' , short_name ) from {$wpdb->prefix}software_hub_os where os_group_id = {$wpdb->prefix}software_hub_os_group.id order by display_order asc ) as oslist, ( select count(id) from {$wpdb->prefix}software_hub_os where os_group_id = {$wpdb->prefix}software_hub_os_group.id order by display_order asc ) as oscount FROM {$wpdb->prefix}software_hub_os_group 
                                    inner join {$wpdb->prefix}software_hub_install on {$wpdb->prefix}software_hub_os_group.id =  {$wpdb->prefix}software_hub_install.os_group_id 
where parent_id = %s or {$wpdb->prefix}software_hub_os_group.id = %s 
    and {$wpdb->prefix}software_hub_install.software_id = %s and live = 1 order by display_order asc ", $osgroup->os_group_id, $osgroup->os_group_id, $params['id'] )
                                );
            }
            
            $installitems = $wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}software_hub_install where software_id = %s ", $params['id'] ));
            
            $releases = $wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}software_hub_software_release where software_id = %s order by time desc ", $params['id'] ));
            
            foreach ( $releases as $release ) {
                $changes = $wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}software_hub_changelog where software_release_id = %s and live = 1 order by time desc ", $release->id ));
                $release->changes = $changes;
            }
            
            $githubweb = 'https://github.com/'.$software->github_user.'/'.$software->github_repository;
            
            require_once(dirname(__FILE__) . '/frontend-view-software-hub.php');
        }
    }
}


function software_hub_install ( ) {
   global $wpdb;
   $software_hub_db_version = "0.1";

   $software_table_name = $wpdb->prefix . "software_hub_software";
   $software_release_table_name = $wpdb->prefix . "software_hub_software_release";
   $changelog_table_name = $wpdb->prefix . "software_hub_changelog";
   $install_table_name = $wpdb->prefix . "software_hub_install";
   $os_table_name = $wpdb->prefix . "software_hub_os";
   $os_group_table_name = $wpdb->prefix . "software_hub_os_group";
   $os_group_software_file_table_name = $wpdb->prefix . "software_hub_os_group_software_file";
      
   $software_sql = "CREATE TABLE $software_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar (255) NOT NULL,
  github_user varchar (255) NOT NULL,
  github_repository varchar (255) NOT NULL,
  download_url_prefix varchar (255) NOT NULL,
  overview_enabled tinyint(1) NOT NULL,
  overview longtext NOT NULL,
  changelog_enabled tinyint(1) NOT NULL,
  changelog longtext NOT NULL,
  installation_enabled tinyint(1) NOT NULL,
  installation longtext NOT NULL,
  configuration_enabled tinyint(1) NOT NULL,
  configuration longtext NOT NULL,
  issues_enabled tinyint(1) NOT NULL,
  issues longtext NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   $software_release_sql = "CREATE TABLE $software_release_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar (255) NOT NULL,
  notes longtext NOT NULL,
  software_id mediumint(9) NOT NULL,
  time datetime NOT NULL,
  live tinyint (1) NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   $changelog_sql = "CREATE TABLE $changelog_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  commit varchar (50) NOT NULL,
  note longtext NOT NULL,
  display_message longtext NOT NULL,
  software_release_id mediumint(9) NOT NULL,
  software_id mediumint(9) NOT NULL,
  time datetime NOT NULL,
  live tinyint (1) NOT NULL,
  UNIQUE KEY id (id)
    );";

   $install_sql = "CREATE TABLE $install_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  software_id mediumint(9) NOT NULL,
  content longtext NOT NULL,
  os_group_id mediumint(9) NOT NULL,
  live tinyint (1) NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   $os_sql = "CREATE TABLE $os_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  short_name varchar(255) NOT NULL,
  os_group_id mediumint(9) NOT NULL,
  UNIQUE KEY id (id)
    );";
   
$os_data_sql = "REPLACE INTO $os_table_name (`id`, `name`, `short_name`, `os_group_id`) VALUES
(1, 'Ubuntu Linux', 'Ubuntu', 7),
(2, 'Kubuntu Linux', 'Kubuntu', 7),
(3, 'Xubuntu Linux', 'Xubuntu', 7),
(4, 'Linux Mint', 'Mint', 7),
(5, 'Debian', 'Debian', 2),
(6, 'MEPIS Linux', 'MEPIS', 2),
(7, 'CentOS', 'CentOS', 5),
(8, 'Fedora', 'Fedora', 5),
(9, 'Oracle', 'Oracle', 5),
(10, 'Redhat Enterprise Linux', 'RHEL', 5),
(11, 'Scientific Linux', 'Scientific Linux', 5),
(12, 'OpenSUSE', 'OpenSUSE', 6),
(13, 'SUSE EL', 'SUSE EL', 6),
(14, 'Gentoo', 'Gentoo', 3),
(15, 'Sabayon', 'Sabayon', 3),
(16, 'Funtoo', 'Funtoo', 3),
(17, 'Arch Linux', 'Arch', 4),
(18, 'Chakra', 'Chakra', 4),
(21, 'Other (Source install)', 'Other (Source install)', 10),
(22, 'Mac OS X', 'Mac OS X', 8),
(23, 'Windows', 'Windows', 9);";
   
   $os_group_sql = "CREATE TABLE $os_group_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  short_name varchar(255) NOT NULL,
  parent_id mediumint(9) NOT NULL,
  display_order mediumint(9) NOT NULL,
  UNIQUE KEY id (id)
    );";

$os_group_data_sql = "REPLACE INTO $os_group_table_name (`id`, `name`, `short_name`, `parent_id`, `display_order`) VALUES
(1, '.rpm file based installation', 'RPM Based', 0, 5),
(2, '.deb file based installation', 'Deb Based', 0, 2),
(3, 'Portage Ebuild', 'Ebuild', 0, 7),
(4, 'Arch Pacman', 'Arch', 0, 6),
(5, 'Yum Based', 'Yum', 1, 3),
(6, 'Zypper Based', 'Zypper', 1, 4),
(7, 'PPA Based Installation', 'PPA', 2, 1),
(8, 'Darwin Based', 'Mac', 0, 8),
(9, 'Windows Based', 'Windows', 0, 9),
(10, 'Source Install', 'Source', 0, 10);
   ";
   
   $os_group_software_file_sql = "CREATE TABLE $os_group_software_file_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  os_group_id mediumint(9) NOT NULL,
  software_id mediumint(9) NOT NULL,
  file varchar(2048) NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($software_sql);
   dbDelta($software_release_sql);
   dbDelta($changelog_sql);
   dbDelta($install_sql);
   dbDelta($os_sql);
   dbDelta($os_group_sql);
   dbDelta($os_group_software_file_sql);
   
   $wpdb->query($os_group_data_sql);
   $wpdb->query($os_data_sql);
   
   add_option("software_hub_db_version", $software_hub_db_version);
}

function software_hub_download ( $params ) {
    if ( isset($params) && is_array($params) && isset( $params['id'] ) && isset( $params['os_group_id'] ) ) {
        global $wpdb;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT file FROM {$wpdb->prefix}software_hub_os_group_software_file where software_id = %s and os_group_id = %s limit 1", $params['id'], $params['os_group_id'] )
        );
        if ( isset($item->file) ) {
            $file = $item->file;
            if ( stripos($file, "%v") !== false ) { 
                $lastrelease = $wpdb->get_row(
                    $wpdb->prepare("SELECT name FROM {$wpdb->prefix}software_hub_software_release where software_id = %s and live = 1 order by time desc limit 1", $params['id'] )
                );
                if ( isset($lastrelease->name) ) {
                    $file = str_replace("%v", $lastrelease->name, $file);
                }
            }
            return $file;
        }
    }
}

function software_hub_download_prefix ( $params ) {
    if ( isset($params) && is_array($params) && isset( $params['id'] ) ) {
        global $wpdb;
        $software = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_software where id = %s limit 1", $params['id'] )
        );
        return $software->download_url_prefix;
    }
}

register_activation_hook(__FILE__,'software_hub_install');

add_action('admin_menu', 'software_hub_add_pages');
add_shortcode('software_hub_view', 'software_hub_view');
add_shortcode('software_hub_download', 'software_hub_download');
add_shortcode('software_hub_download_prefix', 'software_hub_download_prefix');
