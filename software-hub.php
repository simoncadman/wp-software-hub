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
        try {
            $lastsha = 'master';
            
            $i=0;
            while ( $i < 1000 ) {
                $i++;
                $currentcommits = $githubclient->api('repo')->commits()->all('simoncadman', 'cups-cloud-print', array('sha' => $lastsha, 'per_page' => 100));
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
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software_hub_changelog where commit = %s limit 1", $id )
            );
            if ( is_null($commitItem) ) {
                $wpdb->insert( $wpdb->prefix . "software_hub_changelog", array( 'commit' => $id,
                                                                                'note' => $commit['commit']['message'],
                                                                                'software_id' => $_POST['software_id'],
                                                                                'time' => gmdate('Y-m-d H:i:s', strtotime($commit['commit']['committer']['date']) )) );
            }
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['software_id'] ) ) {
        $newfields = array();
        if ( $_POST['software_hub_backend_page_type'] == 'overview' ) {
            if ( isset( $_POST['software_hub_overview_enabled'] ) ) {
                $newfields['overview_enabled'] = $_POST['software_hub_overview_enabled'] === 'on';
            } else {
                $newfields['overview_enabled'] = 0;
            }
            $newfields['overview'] = stripslashes($_POST['software_hub_overview_text']);
        } else if ( $_POST['software_hub_backend_page_type'] == 'changelog' ) {
            if ( isset( $_POST['software_hub_changelog_enabled'] ) ) {
                $newfields['changelog_enabled'] = $_POST['software_hub_changelog_enabled'] === 'on';
            } else {
                $newfields['changelog_enabled'] = 0;
            }
            $newfields['changelog'] = stripslashes($_POST['software_hub_changelog_text']);
        } else if ( $_POST['software_hub_backend_page_type'] == 'installation' ) {
            if ( isset( $_POST['software_hub_installation_enabled'] ) ) {
                $newfields['installation_enabled'] = $_POST['software_hub_installation_enabled'] === 'on';
            } else {
                $newfields['installation_enabled'] = 0;
            }
            $newfields['installation'] = stripslashes($_POST['software_hub_installation_text']);
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
        
        $wpdb->update( $wpdb->prefix . "software_hub_software", $newfields, array( 'id' => $_POST['software_id'] ) );
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
    and {$wpdb->prefix}software_hub_install.software_id = %s  order by display_order asc ", $osgroup->os_group_id, $osgroup->os_group_id, $params['id'] )
                                );
            }
            
            $installitems = $wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}software_hub_install where software_id = %s ", $params['id'] ));
                                            
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
  UNIQUE KEY id (id)
    );";
   
   $changelog_sql = "CREATE TABLE $changelog_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  commit varchar (50) NOT NULL,
  note longtext NOT NULL,
  software_release_id mediumint(9) NOT NULL,
  software_id mediumint(9) NOT NULL,
  time datetime NOT NULL,
  UNIQUE KEY id (id)
    );";

   $install_sql = "CREATE TABLE $install_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  software_id mediumint(9) NOT NULL,
  content longtext NOT NULL,
  os_group_id mediumint(9) NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   $os_sql = "CREATE TABLE $os_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  short_name varchar(255) NOT NULL,
  os_group_id mediumint(9) NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   $os_group_sql = "CREATE TABLE $os_group_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  short_name varchar(255) NOT NULL,
  parent_id mediumint(9) NOT NULL,
  display_order mediumint(9) NOT NULL,
  UNIQUE KEY id (id)
    );";
   
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
                    $wpdb->prepare("SELECT name FROM {$wpdb->prefix}software_hub_software_release where software_id = %s order by time desc limit 1", $params['id'] )
                );
                $file = str_replace("%v", $lastrelease->name, $file);
            }
            return $file;
        }
    }
}

register_activation_hook(__FILE__,'software_hub_install');

add_action('admin_menu', 'software_hub_add_pages');
add_shortcode('software_hub_view', 'software_hub_view');
add_shortcode('software_hub_download', 'software_hub_download');
