<?php
/*
Plugin Name: Redirectify
Plugin URI: http://drewhardy.com/redirectify
Description: Custom Redirect Options
Version: 1.0.0
Author: Drew Hardy
Author URI: http://drewhardy.com
License: A "Slug" license name e.g. GPL2
*/

/*  Copyright 2013  Drew Hardy  (email : me@drewhardy.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Make sure WP runs install upon activation
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
register_activation_hook(__FILE__,'redirectify_install');
register_activation_hook(__FILE__,'redirectify_install_data');
add_action( 'plugins_loaded', 'redirectify_perform_redirect' );
register_uninstall_hook(__FILE__,'uninstall');
$wpdb->show_errors();


// Setup DB Version Control
global $redirectify_db_version;
$redirectify_db_version = "1.0.0";
global $table_name;
$table_name = $wpdb->prefix . 'redirectify_config';

// Create Install Function
function redirectify_install(){
	global $wpdb, $redirectify_db_version, $table_name;
		
	$sql = "CREATE TABLE $table_name (
		id int(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		url VARCHAR(55) DEFAULT '' NOT NULL,
		UNIQUE KEY id (id)
	);";
	
	dbDelta($sql);
	
	add_option("redirectify_db_version", $redirectify_db_version);
}

// Check if an update is required

function redirectify_update_db_check() {
    global $redirectify_db_version;
    if (get_site_option( 'galleryify_db_version' ) != $redirectify_db_version) {
        redirectify_install();
    }
}
add_action( 'plugins_loaded', 'redirectify_update_db_check' );


// Setup Admin Menu
add_action( 'admin_menu', 'register_redirectify_menu' );
function register_redirectify_menu(){
   add_menu_page( 'Redirectify', 'Redirectify', 'manage_options', 'redirectify/redirectify_admin.php', '',plugins_url( 'redirectify/images/icon.png' ) ); 
    //call register settings function
	add_action( 'admin_init', 'register_redirectify_settings' );
}

function register_redirectify_settings() {
	//register our settings
	register_setting( 'redirectify-settings-group', 'redirect_name' );
	register_setting( 'redirectify-settings-group', 'redirect_url' );
	register_setting( 'redirectify-settings-group', 'redirect_case' );
}

function redirectify_save_settings() {
	global $wpdb, $table_name;
	switch($_POST['action']){
		case create:
			$name = $_POST['redirect_name'];
			$url = $_POST['redirect_url'];
			$wpdb->insert(
		   		$table_name,
		   		array( 
					'id' => NULL, 
					'name' => $name,
					'url' => $url 
				), 
				array( 
					'%d', 
					'%s',
					'%s', 
				) 
	   		);
	   		break;
	   	case delete:
	   		$wpdb->delete(
	   			$table_name, 
	   			array( 'ID' => $_POST['id'] ),  
	   			array( '%d' )
	   		);
	   		break;

	}
}

function redirectify_perform_redirect(){
	global $wpdb, $table_name;
	if(is_user_logged_in() == false){
		$redirect_url = $wpdb->get_row("SELECT * FROM  $table_name WHERE id = 1");
		if(!is_admin() && $_SERVER['REQUEST_URI'] != $redirect_url->url && $_SERVER['REQUEST_URI'] != '/wp-login.php' ){
			wp_redirect( $redirect_url->url , 307 );
		}
	};
}


