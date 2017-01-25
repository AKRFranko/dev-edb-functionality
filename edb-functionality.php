<?php
/**
* Plugin Name:       EDB Functionality Plugin
* Plugin URI:        http://elementdebase.com
* Description:       EDB Functionality Plugin
* Version:           1.0.0
* Author:            Element De Base
* Author URI:        http://example.com/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       edb
* 
*/


function edb_woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'post-thumbnails' );
}

add_action( 'after_setup_theme', 'edb_woocommerce_support' );

function edb_remove_menus(){
  // remove_menu_page( 'index.php' );                  //Dashboard
  // remove_menu_page( 'jetpack' );                    //Jetpack* 
  // remove_menu_page( 'edit.php' );                   //Posts
  // remove_menu_page( 'upload.php' );                 //Media
  // remove_menu_page( 'edit.php?post_type=page' );    //Pages
  remove_menu_page( 'edit-comments.php' );          //Comments
  // remove_menu_page( 'themes.php' );                 //Appearance
  // remove_menu_page( 'plugins.php' );                //Plugins
  // remove_menu_page( 'users.php' );                  //Users
  // remove_menu_page( 'tools.php' );                  //Tools
  // remove_menu_page( 'options-general.php' );        //Settings
}
add_action( 'admin_menu', 'edb_remove_menus' );

remove_filter('the_content', 'wpautop');

include 'edb-post-types.php';
include 'edb-mb-rest-api.php';
include 'edb-rest-api.php';
include 'edb-wc-rest-api.php';
include 'edb-user-extras.php';

// include 'mysitename-functionality-extra-rss-feeds.php';
// include 'mysitename-functionality-remove-unwanted-assets.php';