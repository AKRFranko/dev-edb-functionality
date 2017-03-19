<?php

function edb_woocommerce_rest_insert_product($post, $request){
  $params = $request->get_param('meta_box');
  foreach($params as $param => $value){
    update_post_meta($post->ID, $param, $value );
  }

}
function expand_metabox_properties( $product ){
  $meta_boxes = RWMB_Core::get_meta_boxes();
  $product->meta_box = array();
  foreach ($meta_boxes as $meta_box) {
      $meta_box = RW_Meta_Box::normalize($meta_box);
      
      if (!in_array($product->post->post_type, $meta_box['post_types'])) {
          continue;
      }
      foreach ($meta_box['fields'] as $field) {
        if (!empty($field['id'])) {
          $product->meta_box[ $field['id'] ] = rwmb_meta(  $field['id'], $field, $product->post->ID );
        }
      }
  }
  return $product;
}
function edb_woocommerce_rest_prepare_product( $response, $post ){
  // $wf = rwmb_meta( 'edb_wireframe', 'size=full', $post->ID );
  // $ae = rwmb_meta( 'edb_anatomy_en', 'size=full', $post->ID );
  // $af = rwmb_meta( 'edb_anatomy_fr', 'size=full', $post->ID );
  // $metabox = array(
  //   'edb_why_we_love_title' =>rwmb_meta( 'edb_why_we_love_title', null, $post->ID ) ,
  //   'edb_why_we_love_content' =>rwmb_meta( 'edb_why_we_love_content', null, $post->ID ) ,
  //   'edb_wireframe' => array_shift($wf),
  //   'edb_anatomy_en' =>array_shift($ae),
  //   'edb_anatomy_fr'=>array_shift($af)
  // );
  
  $response->data['meta_box'] = array();
  $meta_boxes = RWMB_Core::get_meta_boxes();
  foreach ($meta_boxes as $meta_box) {
      $meta_box = RW_Meta_Box::normalize($meta_box);
      if (!in_array('product', $meta_box['post_types'])) {
          continue;
      }
      foreach ($meta_box['fields'] as $field) {
        if (!empty($field['id'])) {
          if( in_array( $field['id'] , array('edb_wireframe','edb_anatomy_en','edb_anatomy_fr') )){
            $opts = 'size=full&single=true';
          }else{
            $opts = $field;
          }
          $response->data['meta_box'][ $field['id'] ] = rwmb_meta(  $field['id'], $opts, $post->ID  );
        }
      }
  }
  
  return $response;
}

function edb_woocommerce_rest_check_permissions( $permission, $context, $object_id, $post_type  ){
  // var_dump($_SERVER);
  return true;
}


add_filter( 'woocommerce_rest_prepare_product', 'edb_woocommerce_rest_prepare_product', 90, 2 );
add_filter( 'woocommerce_rest_insert_product', 'edb_woocommerce_rest_insert_product', 90, 2 );
add_filter( 'woocommerce_rest_check_permissions', 'edb_woocommerce_rest_check_permissions', 90, 4 );
