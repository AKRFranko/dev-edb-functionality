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
add_filter('woocommerce_get_price', 'edb_return_custom_price', 2); 
function edb_return_custom_price( $product, $price) {
  $base_price = rwmb_meta('edb_base_price', null, $post_id);
  return $base_price;
}
//     // Grab the product id
//     $post_id = $product->id; 
//     // Get user's ip location and correspond it to the custom field key
//     // $base_price = rwmb_meta('edb_base_price', null, $post_id);
//     // return is_null($price) ? $base_price : $
//     return $price;
// }   


add_action( 'after_setup_theme', 'edb_woocommerce_support' );

function edb_add_svg_to_upload_mimes( $upload_mimes ) {
  $upload_mimes['svg'] = 'image/svg+xml';
  $upload_mimes['svgz'] = 'image/svg+xml';
  return $upload_mimes;
}
add_filter( 'upload_mimes', 'edb_add_svg_to_upload_mimes', 10, 1 );


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


// add_filter('manage_edb_materials_posts_columns','filter_cpt_columns'  );

// function filter_cpt_columns( $columns ) {
//     // this will add the column to the end of the array
//     $columns['material'] = 'Material';
//     //add more columns as needed

//     // as with all filters, we need to return the passed content/variable
//     return $columns;
// }

// add_action( 'manage_posts_custom_column','action_custom_columns_content' , 10, 2 );
// function action_custom_columns_content ( $column_id, $post_id ) {
//     //run a switch statement for all of the custom columns created
//     switch( $column_id ) { 
//         case 'material':
//             echo rwmb_meta('edb_material', null, $post_id);
//         break;

//         //add more items here as needed, just make sure to use the column_id in the filter for each new item.

//   }
// }
// include 'mysitename-functionality-extra-rss-feeds.php';
// include 'mysitename-functionality-remove-unwanted-assets.php';