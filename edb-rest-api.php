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
       $colors = get_post_meta(get_post_thumbnail_id( $post['id'] ),'color_palette_hex');
       return array_shift($colors);
    } else {
        return false;
    }

}

function edb_rest_get_metabox($post){
  
  $meta_boxes = RWMB_Core::get_meta_boxes();
  
  $mb = array();
  foreach ($meta_boxes as $meta_box) {
      $meta_box = RW_Meta_Box::normalize($meta_box);
      if (!in_array($post['type'], $meta_box['post_types'])) {
        continue;
      }
      foreach ($meta_box['fields'] as $field) {
        if (!empty($field['id'])) {
          $field_value = rwmb_meta(  $field['id'], $field, $post['id'] );
          if ( is_array( $field_value ) && in_array( $field['type'], array( 'media', 'file', 'file_upload', 'file_advanced', 'image', 'image_upload', 'image_advanced', 'plupload_image', 'thickbox_image' ), true ) ) {
            $field_value = array_values( $field_value );
            foreach( $field_value as $k => $value ){
              $id = $value['ID'];
              unset($value['ID']);
              $value['id']=$id;
              if(preg_match('/image/',$field['type'])){
                $colors = get_post_meta($id, 'color_palette_hex',false);
                if(is_array($colors) && is_array($colors[0])){
                  $colors = $colors[0];
                }
                $value = array(
                  'id' =>$id,
                  'src' =>$value['full_url'],
                  'colors'=>$colors
                );
                
              }
              $field_value[$k]=$value;
            }
          }else{
            if(preg_match('/image/',$field['type'])){
              if(!empty($field_value['ID'])){
                $id = $field_value['ID'];
              }else if(!empty($field_value['id'])){
                $id = $field_value['ID'];
              }
              $colors = get_post_meta($id, 'color_palette_hex',false);
              if(is_array($colors) && is_array($colors[0])){
                $colors = $colors[0];
              }
              $field_value = array(
                'id' =>$id,
                'src' =>$field_value['full_url'],
                'colors'=>get_post_meta($id, 'color_palette_hex',false)
              );
            }
          }
          $mb[ $field['id'] ] = $field_value;
        }
      }  
      
  }
  
  return $mb;
}

function edb_rest_get_product_metabox($post){
    $meta = array();
    if($post['meta_data']){
      foreach($post['meta_data'] as $k  => $v){
        
        if($v->key == 'edb_wireframe'){
          $image = wp_get_attachment_image_src($v->value);
          if($image){
            $meta[$v->key] = array("src"=>$image[0] );
          }
        }else if($v->key == 'edb_anatomy_en' || $v->key == 'edb_anatomy_fr'){
          $image = wp_get_attachment_image_src($v->value);
          if($image){
            $meta[$v->key] = array("src"=>$image[0] );
          }
        }else{
          $meta[$v->key]  =  $v->value;
        }
      }
    }
    return $meta;
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


function edb_rest_get_subtitle($post){
  if($post){
    
    return get_post_meta($post['id'], 'wps_subtitle', true );
  }
};
function edb_rest_update_subtitle($data, $post){
  if($post){
    return update_post_meta($post['id'], 'wps_subtitle', $data );
  }
};

function edb_rest_update_customer_meta( $meta , $user){
  foreach( $meta as $k => $v){
    update_user_meta($user->ID, $k, $v );
  }
};

function edb_rest_get_product_variations($post){
  if($post){
    $factory = new WC_Product_Factory();
    $product = $factory->get_product( $post['id'] );
    if($product->get_type() === 'variable'){
      $variations = $product->get_available_variations();  
      foreach($variations as $k => $v){
        $variation_obj = new WC_Product_variation( $v['variation_id'] );
        $stock = $variation_obj->get_stock_quantity();
        $variations[$k]['parent_product_id'] = $post['id'];
        $variations[$k]['stock'] = $stock;
      }
      return $variations;
    }
  }
}

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
  register_rest_field( 'product',
   'meta_box',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_product_metabox',
      'schema'          => null,
      )
  );
  register_rest_field( 'product',
   'variation_data',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_product_variations',
      'schema'          => null,
      )
  );
  
  // public function get_available_variations() {
  //     $available_variations = array();

  //     foreach ( $this->get_children() as $child_id ) {
  //         $variation = wc_get_product( $child_id );

  //         // Hide out of stock variations if 'Hide out of stock items from the catalog' is checked
  //         if ( ! $variation->exists() || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) ) {
  //             continue;
  //         }

  //         // Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price)
  //         if ( apply_filters( 'woocommerce_hide_invisible_variations', false, $this->get_id(), $variation ) && ! $variation->variation_is_visible() ) {
  //             continue;
  //         }

  //         $available_variations[] = $this->get_available_variation( $variation );
  //     }

  //     return $available_variations;
  // }
  register_rest_field( 'product_variation',
   'meta_box',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_product_metabox',
      'schema'          => null,
      )
  );
  
  register_rest_field( 'edb_features',
  'subtitle',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_subtitle',
      'update_callback' => 'edb_rest_update_subtitle',
      'schema'          => null,
      )
  );
  register_rest_field( 'edb_features',
  'meta_box',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_metabox',
      
      )
  );
  register_rest_field( 'edb_inspirations',
  'meta_box',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_metabox',
      
      )
  );
  
  register_rest_field( 'edb_materials',
  'meta_box',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_metabox',
      
      )
  );
  register_rest_field( 'edb_slides',
  'meta_box',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_metabox',
      
      )
  );
  register_rest_field( 'post',
  'meta_box',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_metabox',
      
      )
  );
  register_rest_field( 'page',
  'meta_box',  //key-name in json response
    array(
      'get_callback'    => 'edb_rest_get_metabox',
      
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
function is_a_valid_email($email) {
    return !!filter_var($email, FILTER_VALIDATE_EMAIL);
}

function edb_register($data){
  
  if(!is_a_valid_email($data['email'])){
    return new WP_Error( 'invalid_user_registration', "Email: missing or invalid.", array('status'=>401));
  }
  if($data['password'] && $data['password_confirm'] && $data['password'] != $data['password_confirm']){
    return new WP_Error( 'invalid_user_registration', "Password: passwords do not match.", array('status'=>401));
  }
  $id = wp_insert_user( array(
    'user_login'  => $data['username'],
    'user_email'  => $data['email'],
    'user_pass'   => $data['password']
  ));
  if (is_wp_error($id)){
    return new WP_Error( $id->get_error_code(), $id->get_error_message(), array( 'status' => 401));
  }
  return edb_login( $data );
}

function edb_unregister($data){
  $id = edb_login( $data );
  if (is_wp_error($id)){
    return new WP_Error( $id->get_error_code(), $id->get_error_message(), array( 'status' => 401));
  }
  return wp_logout();
  
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
       register_rest_field( $type ,'featured_colors', array( 'get_callback'    => 'edb_rest_get_thumbnail_colors', 'update_callback' => null, 'schema'=> null ) );
     }
}

function edb_populate_jwt( $data, $user ){
  $data['user_id'] = $user->id;
  return $data;
}


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
  
  register_rest_route( 'wp/v2', '/unregister', array(
    'methods' => 'POST',
    'callback' => 'edb_unregister',
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
    header('Access-Control-Expose-Headers: X-WP-Total, X-WP-TotalPages');
    $aheaders = apache_request_headers();
    $name = @$aheaders['Origin'];
    if($name == 'https://edb.akr.club' || $name == 'https://edb.akr.club:3000'){
      header("Access-Control-Allow-Origin: $name");  
    }else{
      header_remove('Access-Control-Allow-Origin');
    }
    if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
      return true;
      die();
    }
    
    // 
    
    return $value;
  });
}, 15 );
