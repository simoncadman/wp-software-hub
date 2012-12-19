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
    $instances = array();
    foreach ( explode("\n", get_option('software_hub_instances', "")) as $software ) {
        $instances[] = array( 'id' => trim(str_replace(' ', '_', $software)), 'name' => trim($software) );
    }
    return $instances;
}

function software_hub_settings () {
    register_setting( 'software_hub_settings', 'software_hub_instances');
    foreach ( software_hub_get_software_instances() as $software ) {
        // overview
        register_setting( 'software_hub_settings', 'software_hub_overview_enabled_'.$software['id']);
        register_setting( 'software_hub_settings', 'software_hub_overview_text_'.$software['id']);
        
        // changelog
        register_setting( 'software_hub_settings', 'software_hub_changelog_enabled_'.$software['id']);
        register_setting( 'software_hub_settings', 'software_hub_changelog_text_'.$software['id']);
        
        // installation
        register_setting( 'software_hub_settings', 'software_hub_installation_enabled_'.$software['id']);
        register_setting( 'software_hub_settings', 'software_hub_installation_text_'.$software['id']);
        
        // configuration
        register_setting( 'software_hub_settings', 'software_hub_configuration_enabled_'.$software['id']);
        register_setting( 'software_hub_settings', 'software_hub_configuration_text_'.$software['id']);
        
        // issues
        register_setting( 'software_hub_settings', 'software_hub_issues_enabled_'.$software['id']);
        register_setting( 'software_hub_settings', 'software_hub_issues_text_'.$software['id']);
    }
}

function software_hub_add_pages() {
    add_submenu_page( 'options-general.php', 'Software Hub', 'Software Hub', 'manage_options', 'software_hub_menu', 'software_hub_options' );
}

function software_hub_options () {
    require_once(dirname(__FILE__) . '/admin-options.php');
}

function software_hub_view ( $params ) {
    if ( isset($params) && is_array($params) && isset( $params['id'] ) ) {
        $software = null;
        foreach ( software_hub_get_software_instances() as $softwareItem ) {
            if ( $softwareItem['id'] == $params['id'] ) {
                $software = $softwareItem;
                break;
            }
        }
        
        if ( !is_null($software) ) {
            wp_enqueue_script('software_hub', '/wp-content/plugins/software-hub/js/software-hub.js');
            wp_enqueue_style('software_hub', '/wp-content/plugins/software-hub/css/software-hub.css');
            require_once(dirname(__FILE__) . '/frontend-view-software-hub.php');
        }
    }
}

add_action('admin_menu', 'software_hub_settings');
add_action('admin_menu', 'software_hub_add_pages');

foreach ( software_hub_get_software_instances() as $software ) {
    add_shortcode('software_hub_view', 'software_hub_view');
}
