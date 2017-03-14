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
  $signon = wp_signon( 
    array( 
      'user_login' => $data['email'] , 
      'user_password' => $data['password'], 
      'remember_me' => true ) );
  return  $signon ;
}

function edb_logout( ) {
  return  wp_logout() ;
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
     }
}
//register actions
add_action( 'rest_api_init', 'edb_rest_insert_thumbnail_url' );
add_action( 'rest_api_init', 'edb_rest_register_fields' );

add_action( 'rest_api_init', function() {
    
  remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
  
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
  
  add_filter( 'rest_pre_serve_request', function( $value ) {
    header_remove('Access-Control-Allow-Headers');
    header_remove('Access-Control-Allow-Origin');
    header_remove('Access-Control-Allow-Methods');
    header_remove('Access-Control-Allow-Credentials');
    header_remove('Access-Control-Expose-Headers');
    header('Access-Control-Allow-Headers: Authorization, X-Requested-With, Content-Type, Content-Disposition');
    header('Access-Control-Allow-Methods: HEAD, OPTIONS, GET, PUT, POST, PATCH, DELETE');
    return $value;
  });
}, 15 );
