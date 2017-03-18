<?php

function edb_woocommerce_rest_insert_product($post, $request){
  $params = $request->get_param('meta_box');
  foreach($params as $param => $value){
    update_post_meta($post->ID, $param, $value );
  }

}

function edb_woocommerce_rest_prepare_product( $response, $post ){
  $wf = rwmb_meta( 'edb_wireframe', 'size=full', $post->ID );
  $ae = rwmb_meta( 'edb_anatomy_en', 'size=full', $post->ID );
  $af = rwmb_meta( 'edb_anatomy_fr', 'size=full', $post->ID );
  $metabox = array(
    'edb_why_we_love_title' =>rwmb_meta( 'edb_why_we_love_title', null, $post->ID ) ,
    'edb_why_we_love_content' =>rwmb_meta( 'edb_why_we_love_content', null, $post->ID ) ,
    'edb_wireframe' => array_shift($wf),
    'edb_anatomy_en' =>array_shift($ae),
    'edb_anatomy_fr'=>array_shift($af)
  );
  $response->data['meta_box'] = $metabox;
  return $response;
}


add_filter( 'woocommerce_rest_prepare_product', 'edb_woocommerce_rest_prepare_product', 90, 2 );
add_filter( 'woocommerce_rest_insert_product', 'edb_woocommerce_rest_insert_product', 90, 2 );
