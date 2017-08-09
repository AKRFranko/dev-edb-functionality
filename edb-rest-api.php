<?php

function edb_rest_get_thumbnail_url($post){
    if(has_post_thumbnail($post['id'])){
        $imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $post['id'] ), 'full' ); // replace 'full' with 'thumbnail' to get a thumbnail
        $imgURL = $imgArray[0];
        return $imgURL;
    } else {
        return false;
    }
}

function edb_rest_get_thumbnail_colors($post){
    if(has_post_thumbnail($post['id'])){
        $colors = get_post_meta($post['id'],'color_palette_hex');
        return $colors;
    } else {
        return array();
    }
}


function edb_rest_get_designer_meta($user){
  if($user){
    $edb_user_is_designer = get_the_author_meta( 'edb_user_is_designer', $user['id'], true );
    
    $edb_user_designer_level = get_the_author_meta( 'edb_user_designer_level', $user['id'], true );
    
    return array(
      'is_designer' => $edb_user_is_designer,
      'designer_level' => $edb_user_designer_level );  
  }
};

function edb_rest_update_designer_meta( $v , $user){
  $edb_user_designer_level = $v['designer_level'];
  if($edb_user_designer_level == 'none'){
    $edb_user_is_designer=false;
  }else{
    $edb_user_is_designer=true;
  }
  update_usermeta($user->ID, 'edb_user_designer_level',  $edb_user_designer_level );
  update_usermeta($user->ID, 'edb_user_is_designer',$edb_user_is_designer );
  
};

function edb_rest_get_customer_meta($user){
  if($user){
    $billing = array('first_name','last_name','company','address_1','address_2','city','postcode','country','state','email','phone');
    $shipping = array('first_name','last_name','company','address_1','address_2','city','postcode','country','state');
    $meta = array();
    foreach($billing as $field ){
      $meta['billing_'.$field]=get_user_meta( $user['id'], 'billing_'.$field, true);
    }
    foreach($shipping as $field ){
      $meta['shipping_'.$field]=get_user_meta( $user['id'], 'shipping_'.$field, true);
    }
    return $meta;
  }
};

function edb_rest_update_customer_meta( $meta , $user){
  foreach( $meta as $k => $v){
    update_user_meta($user->ID, $k, $v );
  }
};

function edb_rest_register_fields(){
  register_rest_field( 'user',
   'designer_meta',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_designer_meta',
      'update_callback' => 'edb_rest_update_designer_meta',
      'schema'          => null,
      )
  );
  register_rest_field( 'user',
   'customer_meta',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_customer_meta',
      'update_callback' => 'edb_rest_update_customer_meta',
      'schema'          => null,
      )
  );
}


function edb_get_auth_user( $data ) {
  if ( !(is_user_logged_in()) ){
    $current_user = null;
  }else{
    $current_user = wp_get_current_user(); 
  }
  return $current_user;
}


function edb_login( $data ) {
  
    $signon = wp_signon( array( 'user_login' => $data['username'], 'user_password' => $data['password'], 'remember_me' => true ) );
    if( is_wp_error( $signon ) ) {
      return $signon;
    }else{
      wp_set_auth_cookie( $signon->ID, true );
      return $signon;
    }
}


function edb_logout( ) {
  return  wp_logout() ;
}


function edb_reset(){
  wc_update_product_stock( 199, 10, 'set');
  wc_update_product_stock( 234, 5, 'set');
  wc_update_product_stock( 233, 2, 'set');
  wc_update_product_stock( 232, 3, 'set');
  wc_update_product_stock( 231, 2, 'set');
  wc_update_product_stock( 463, 4, 'set');
  wc_update_product_stock( 472, 0, 'set');
  wc_update_product_stock( 471, 2, 'set');
  wc_update_product_stock( 470, 4, 'set');
  wc_update_product_stock( 469, 1, 'set');
  return true;
}


function edb_register($data){
  
  
  $id = wp_insert_user( array(
    'user_login'  => $data['username'],
    'user_email'  => $data['username'],
    'user_pass'   => $data['password']
  ));
  
  if (is_wp_error($id)){
    return $id;
  }
  return edb_login( $data );
}

//integrate with WP-REST-API
function edb_rest_insert_thumbnail_url() {
     $postTypes = array(
       'post',
       'page',
       'edb_materials',
       'edb_slides',
       'edb_lookbooks',
       'edb_inspirations',
       'edb_faqs',
       'edb_materials',
       'edb_features'
     );
     $cb = array( 'get_callback'    => 'edb_rest_get_thumbnail_url', 'update_callback' => null, 'schema'=> null );
     foreach($postTypes as $type){
       register_rest_field( $type ,'featured_image',  $cb );
       register_rest_field( $type ,'featured_colors', 'edb_rest_get_thumbnail_colors' );
     }
}
function edb_populate_jwt( $data, $user ){
  $data['user_id'] = $user->id;
  return $data;
}

// function edb_get_catalog_entry( $product ){
//   $copy = clone $product;
 
//   $name = $product->post->post_title;
//   $description = $product->post->post_content;
  
//   $product->post = $product->id;
//   $product->name = $name;
//   $product->description = $description;
//   return $product;
// }

// $magic_properties = array(
// // 'width',
// // 'length',
// // 'height',
// // 'weight',
// 'price',
// 'regular_price',
// 'sale_price',
// 'product_image_gallery',
// // 'sku',
// 'stock',
// // 'downloadable',
// // 'virtual',
// // 'sold_individually',
// 'tax_status',
// 'tax_class',
// // 'manage_stock',
// // 'stock_status',
// // 'backorders',
// // 'featured',
// // 'visibility',
// 'variation_id');
// function expand_magic_properties( $product ){
//   global $magic_properties;
//   foreach($magic_properties as $prop ){
//     $product->{$prop} = $product->{$prop};
//   }
//   return $product;
// }


// function expand_metabox_properties( $product ){
//   $meta_boxes = RWMB_Core::get_meta_boxes();
//   $product->meta_box = array();
//   foreach ($meta_boxes as $meta_box) {
//       $meta_box = RW_Meta_Box::normalize($meta_box);
      
//       if (!in_array($product->post->post_type, $meta_box['post_types'])) {
//           continue;
//       }
//       foreach ($meta_box['fields'] as $field) {
//         if (!empty($field['id'])) {
//           $product->meta_box[ $field['id'] ] = rwmb_meta(  $field['id'], $field, $product->post->ID );
//         }
//       }
//   }
//   return $product;
// }

// function edb_catalog_item( $data ){
//   return $data;
//   global $magic_properties;
//   $full = (array) $data;
//   $variation =(array) $full['variation'];
//   $product = (array) $full['product'];
//   $post = (array) $full['product']->post;
//   $display =  array(
//     'wc_variation_id'=> $variation['variation_id'],
//     'wc_product_id'=> $product['id'],
//     'wc_product_name'=>$post['post_title'],
//     'wc_product_description'=>$post['post_content']
//   );
//   foreach($magic_properties as $prop){
//     if(isset($product[$prop])){
//       $display["wc_".$prop] = $product[$prop];
//     }
//     if(isset($variation[$prop])){
//       $display["wc_".$prop] = $variation[$prop];
//     }
//   }
//   foreach($product['meta_box'] as $prop => $val ){
//     $display[$prop]=$val;
//   }
  
//   return $display;
  
// }
// function edb_load_catalog( $data ){
  
  
 
//   $get_buckets  = array(
//     'post_type'=> array('product'),
//     'meta_query' => array( array(
//         'key' => 'edb_is_bucket',
//         'value' => '1', // date to compare to, before this one
//         'compare' => '='
//         )
//     ),
//     'fields' => 'ids'
//   );
//   $get_products = array(
//     'post_type'=> array('product'),
//     'meta_query' => array( array(
//         'key' => 'edb_is_bucket',
//         'value' => '1', // date to compare to, before this one
//         'compare' => '!='
//         )
//     ),
//     'fields' => 'ids'
//   );

  
  
//   $bucket_ids = get_posts( $get_buckets );
//   $product_ids = get_posts( $get_products );
  
  
  
//   # references
//   $all_buckets = array();
//   $all_products = array();
//   $all_variations = array();
//   # result
//   $catalog = array();
  
  
//   # load bucket refs
//   foreach( $bucket_ids as $bucket_id){
//     $p = new WC_Product_Variable( $bucket_id );
//     expand_magic_properties( $p );
//     expand_metabox_properties( $p );
//     $p->get_attributes();
//     $p->variations = $p->get_available_variations();
//     $all_buckets['pa_'.$p->meta_box['edb_bucket_slug']]=$p;
    
//   }
  
//   # load product refs
//   foreach( $product_ids as $product_id){
//     $p = new WC_Product_Variable( $product_id );
//     expand_magic_properties( $p );
//     expand_metabox_properties( $p );
//     $p->get_attributes();
//     // $p->meta = get_post_meta($product_id);
//     $all_products[$product_id]=$p;
//   }
  
  
//   # duplicate entries based on attributes
//   foreach( $all_products as $product_id => $product ){
//     $product_variations = array();
//     foreach( $product->get_available_variations() as $available_variation ){
      
//       $variation = new WC_Product_Variable( $available_variation['variation_id'] );
//       $variation->get_attributes();
//       $variation->product_attributes = $product->product_attributes;
//       // expand_magic_properties( $variation );
//       // expand_metabox_properties( $variation );
//       $variation->variation = $available_variation;
//       $variation->product = $product;
//       $product_variations[] = $variation;
//     }
//     foreach($product_variations as $item ){
//       $product_attrs = array_keys($product->product_attributes);
//       $bucket_vars = array();
//       foreach($product_attrs as $attr){
        
//         if(array_key_exists($attr, $all_buckets )){
//           // $catalog[]=$item;    
//           $bucket_vars[]=$all_buckets[$attr]->variations;
//         }
//       }
//       if(empty($bucket_vars)){
//         $catalog[]=edb_catalog_item($item);     
//       }else{
//           foreach($bucket_vars as $bucketitems){
//             foreach($bucketitems as $bucketvar){
//               foreach($product_attrs as $attr){
//                 if(!empty($bucketvar['attributes']["attribute_$attr"])){
//                   // var_dump($bucketvar['attributes']["attribute_$attr"]);
//                   $newItem = clone $item;
//                   $newItem->variation['attributes']["attribute_$attr"] = $bucketvar['attributes']["attribute_$attr"];
//                   $catalog[]=edb_catalog_item($newItem);    
//                 }  
//               }
              
//             }
//           }
//       }

      
//     } 

      
//   }
  
  
//   return array( 'count' => count($catalog), 'catalog' => (array) $catalog);//$catalog;//array('variations'=> $all_variations ,'catalog' => $catalog, 'buckets'=> $all_buckets,'products'=> $all_products);
// }

//register actions
add_action( 'rest_api_init', 'edb_rest_insert_thumbnail_url' );
add_action( 'rest_api_init', 'edb_rest_register_fields' );
// $_SERVER['PHP_AUTH_USER']="ck_171751666f42c473b1746edc1eaa0a4392ac2e4a";
// $_SERVER['PHP_AUTH_PW']="cs_dd0dfe3cfd245660bf27f5fc25d8f98dd3dda14c";

add_filter( 'rest_authentication_errors', '__return_true' );


add_action( 'rest_api_init', function() {
  
  
  remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
  add_filter('jwt_auth_token_before_dispatch', 'edb_populate_jwt', 10, 2);
  register_rest_route( 'wp/v2', '/authenticated', array(
    'methods' => 'GET',
    'callback' => 'edb_get_auth_user',
  ));
  register_rest_route( 'wp/v2', '/login', array(
    'methods' => 'POST',
    'callback' => 'edb_login',
  ));
  register_rest_route( 'wp/v2', '/logout', array(
    'methods' => 'POST',
    'callback' => 'edb_logout',
  ));
  register_rest_route( 'wp/v2', '/register', array(
    'methods' => 'POST',
    'callback' => 'edb_register',
  ));
  
  register_rest_route( 'wp/v2', '/reset', array(
    'methods' => 'POST',
    'callback' => 'edb_reset',
  ));
  
  // register_rest_route( 'wp/v2', '/catalog', array(
  //   'methods' => 'GET',
  //   'callback' => 'edb_load_catalog',
  // ));
  
  add_filter( 'rest_pre_serve_request', function( $value ) {
    header_remove('Access-Control-Allow-Headers');
    header_remove('Access-Control-Allow-Origin');
    header_remove('Access-Control-Allow-Methods');
    header_remove('Access-Control-Allow-Credentials');
    header_remove('Access-Control-Expose-Headers');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Authorization, X-Requested-With, Content-Type, Content-Disposition');
    header('Access-Control-Allow-Methods: HEAD, OPTIONS, GET, PUT, POST, PATCH, DELETE');
    $aheaders = apache_request_headers();
    $name = $aheaders['Origin'];
    
    if($name == 'http://edb.akr.club' || $name == 'http://edb.akr.club:3000'){
      header("Access-Control-Allow-Origin: $name");  
    }else{
      header_remove('Access-Control-Allow-Origin');
    }
    
    
    // 
    // var_dump($_SERVER);
    return $value;
  });
}, 15 );
