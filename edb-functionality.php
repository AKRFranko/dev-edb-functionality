t<?php
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

if (!function_exists('write_log')) {

    function write_log ( $log )  {

        if ( true === WP_DEBUG ) {

            if ( is_array( $log ) || is_object( $log ) ) {

                error_log( print_r( $log, true ) );

            } else {

                error_log( $log );

            }

        }

    }

}


function debug_to_console( $data ) {
if ( is_array( $data ) )
 $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
 else
 $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
  echo $output;
}

function edb_woocommerce_order_get_total_discount( $discount, $order ){
  debug_to_console($order);  
}

add_filter('woocommerce_order_get_total_discount','edb_woocommerce_order_get_total_discount', 90, 2 );

add_filter('woocommerce_get_price', 'edb_return_custom_price', 10,2); 
add_filter('woocommerce_get_regular_price', 'edb_return_custom_price', 10,2); 
add_filter('woocommerce_get_sale_price', 'edb_return_custom_sale_price', 10,2); 
function edb_return_custom_price(  $price,$product) {
  $parent_id = $product->parent_id;
  if(!empty($parent_id)){
    $base_price = rwmb_meta('edb_base_price', null, $parent_id);  
  }else{
    $base_price = rwmb_meta('edb_base_price', null, $product->id);  
  }
  
  $gids = rwmb_meta('edb_group_ids', null, $product->id);
  if(is_array($gids)){
    $split = array();
    foreach( $gids as $k => $v){
      $split = array_merge($split, explode( ',',trim( $v) ) );
    }
    $gids = $split;
  }else{
    $gids = explode( ',',trim( $gids) );
  }
  if(!empty($gids)){
      $prices = array();
      foreach($gids as $gid){
        $prod = wc_get_product( $gid);
        $prices[] = $prod->get_price();
      }
      return floatval( array_sum( $prices ) );  
  }else{
    if(empty($base_price)){
      return floatval($price);
    }
  }
  
  return floatval(max($price,$base_price)) + $price;
  
}
function edb_return_custom_sale_price(  $price,$product) {
  $parent_id = $product->parent_id;
  if(!empty($parent_id)){
    $base_price = rwmb_meta('edb_base_price', null, $parent_id);  
  }else{
    $base_price = rwmb_meta('edb_base_price', null, $product->id);  
  }
  return is_null($price) ? null : floatval(max($price,$base_price)) + $price;
}


// function edb_return_custom_regular_price(  $price,$product) {
//   $parent_id = $product->parent_id;
//   if(!empty($parent_id)){
//     $base_price = rwmb_meta('edb_base_price', null, $parent_id);  
//   }else{
//     $base_price = rwmb_meta('edb_base_price', null, $product->id);  
//   }

//   return floatval(max($price,$base_price)) + $price;
  
// }

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



// // Load necessary admin files.
require_once ABSPATH . 'wp-admin/includes/template.php';
require_once ABSPATH . 'wp-admin/includes/post.php';
// // Load plugin main class.
require_once dirname( __FILE__ ) . '/edb-mb-rest-api.php';
$mb_rest_api = new MB_Rest_API;
add_action( 'rest_api_init', array( $mb_rest_api, 'init' ) );

include 'edb-post-types.php';

// include 'edb-mb-rest-api.php';

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