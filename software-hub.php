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
    
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['software_hub_backend_page_type'] == 'hub' ) {
        if ( isset( $_POST['software_hub_new'] ) && strlen( $_POST['software_hub_new'] ) > 0 ) {
            $wpdb->insert( $wpdb->prefix . "software_hub_software", array( 'name' => $_POST['software_hub_new'] ) );
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['software_hub_backend_page_type'] == 'delete_software'  && isset( $_POST['software_id'] ) ) {
        $wpdb->delete( $wpdb->prefix . "software_hub_software", array('id' => $_POST['software_id'] ) );
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
    }
    
    $softwareInstances = software_hub_get_software_instances();
    require_once(dirname(__FILE__) . '/admin-options.php');
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
  software_id mediumint(9) NOT NULL,
  time datetime NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   $changelog_sql = "CREATE TABLE $changelog_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  software_release_id mediumint(9) NOT NULL,
  UNIQUE KEY id (id)
    );";

   $install_sql = "CREATE TABLE $install_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  software_id mediumint(9) NOT NULL,
  os_id mediumint(9) NOT NULL,
  content longtext NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   $os_sql = "CREATE TABLE $os_table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  UNIQUE KEY id (id)
    );";
   
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($software_sql);
   dbDelta($software_release_sql);
   dbDelta($changelog_sql);
   dbDelta($install_sql);
   dbDelta($os_sql);
 
   add_option("software_hub_db_version", $software_hub_db_version);
}

register_activation_hook(__FILE__,'software_hub_install');

add_action('admin_menu', 'software_hub_add_pages');
add_shortcode('software_hub_view', 'software_hub_view');
