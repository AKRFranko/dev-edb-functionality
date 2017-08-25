<?php

function edb_custom_post_type(  $capPlural, $capSingular, $plural, $singular){
  
  $labels = array(
    'name'                  => _x( $capPlural, 'Post Type General Name', 'edb' ),
    'singular_name'         => _x( $capPlural, 'Post Type Singular Name', 'edb' ),
    'menu_name'             => __( $capPlural, 'edb' ),
    'name_admin_bar'        => __( $capPlural, 'edb' ),
    'archives'              => __( $capSingular.' Archives', 'edb' ),
    'attributes'            => __( $capSingular.' Attributes', 'edb' ),
    'parent_item_colon'     => __( "Parent $capSingular:", 'edb' ),
    'all_items'             => __( "All $capPlural", 'edb' ),
    'add_new_item'          => __( "Add New $capSingular", 'edb' ),
    'add_new'               => __( 'Add New', 'edb' ),
    'new_item'              => __( "New $capSingular", 'edb' ),
    'edit_item'             => __( "Edit $capSingular", 'edb' ),
    'update_item'           => __( "Update $capSingular", 'edb' ),
    'view_item'             => __( "View $capSingular", 'edb' ),
    'view_items'            => __( "View $capPlural", 'edb' ),
    'search_items'          => __( "Search $capSingular", 'edb' ),
    'not_found'             => __( 'Not found', 'edb' ),
    'not_found_in_trash'    => __( 'Not found in Trash', 'edb' ),
    'featured_image'        => __( 'Featured Image', 'edb' ),
    'set_featured_image'    => __( 'Set featured image', 'edb' ),
    'remove_featured_image' => __( 'Remove featured image', 'edb' ),
    'use_featured_image'    => __( 'Use as featured image', 'edb' ),
    'insert_into_item'      => __( 'Insert into item', 'edb' ),
    'uploaded_to_this_item' => __( 'Uploaded to this item', 'edb' ),
    'items_list'            => __( 'Items list', 'edb' ),
    'items_list_navigation' => __( 'Items list navigation', 'edb' ),
    'filter_items_list'     => __( 'Filter items list', 'edb' ),
  );
  $rewrite = array(
    'slug'                  => $plural,
    'with_front'            => true,
    'pages'                 => true,
    'feeds'                 => true,
  );
  $args = array(
    'label'                 => __( $capPlural, 'edb' ),
    'description'           => __( 'custom edb post type', 'edb' ),
    'labels'                => $labels,
    'supports'              => array( 'title', 'editor', 'thumbnail', ),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 5,
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => false,    
    'exclude_from_search'   => true,
    'publicly_queryable'    => true,
    'rewrite'               => $rewrite,
    'query_var'             => $plural,
    'capability_type'       => 'page',
    'show_in_rest'          => true,
    'rest_base' => $plural,
    'rest_controller_class' => 'WP_REST_Posts_Controller'
  );
  return $args;
  // register_post_type($post_type,$args);
}

function edb_register_materials_post_type(){
  $args = edb_custom_post_type( 'Materials', 'Material', 'materials', 'material');
  register_post_type('edb_materials', $args);
  
}
function edb_register_slides_post_type(){
  $args = edb_custom_post_type( 'Slides', 'Slide', 'slides', 'slide');
  register_post_type('edb_slides', $args);
}
function edb_register_features_post_type(){
  $args = edb_custom_post_type( 'Features', 'Feature', 'features', 'feature');
  register_post_type('edb_features', $args);
  add_post_type_support( 'edb_features', 'wps_subtitle' );
}
function edb_register_lookbooks_post_type(){
  $args = edb_custom_post_type( 'Lookbooks', 'Lookbook', 'lookbooks', 'lookbook');
  register_post_type('edb_lookbooks', $args);
}
function edb_register_inspirations_post_type(){
  $args = edb_custom_post_type( 'Inspirations', 'Inspiration', 'inspirations', 'inspiration');
  register_post_type('edb_inspirations', $args);
}
function edb_register_faqs_post_type(){
  $args = edb_custom_post_type( 'FAQs', 'FAQ', 'faqs', 'faq');
  register_post_type('edb_faqs', $args);
}

add_action( 'init', 'edb_register_materials_post_type', 0 );
add_action( 'init', 'edb_register_slides_post_type', 0 );
add_action( 'init', 'edb_register_features_post_type', 0 );
add_action( 'init', 'edb_register_lookbooks_post_type', 0 );
add_action( 'init', 'edb_register_inspirations_post_type', 0 );
add_action( 'init', 'edb_register_faqs_post_type', 0 );

/* META BOXES */

function edb_register_meta_boxes( $meta_boxes ) {
    $prefix = 'edb_';
    // 1st meta box
    $meta_boxes[] = array(
        'id'         => 'EDB',
        'title'      => __( 'EDB', 'edb' ),
        'post_types' => array( 'edb_slides' ),
        'context'    => 'normal',
        'priority'   => 'high',
        'fields' => array(
            array(
                'name'  => __( 'URL', 'edb' ),
                'id'    => $prefix . 'slide_url',
                'type'  => 'text',
                'class' => 'slide-url'
            ),
            array(
                'name'  => __( 'Sort Order', 'edb' ),
                'id'    => $prefix . 'sort_order',
                'type'  => 'number',
                'class' => 'sort-order'
            ),
        )
    );
    
    $meta_boxes[] = array(
        'id'         => 'EDB',
        'title'      => __( 'EDB', 'edb' ),
        'post_types' => array( 'edb_features' ),
        'context'    => 'normal',
        'priority'   => 'high',
        'fields' => array(
            
            array(
                'name'  => __( 'URL', 'edb' ),
                'id'    => $prefix . 'feature_url',
                'type'  => 'text',
                'class' => 'feature-url'
            ),
            array(
                'name'  => __( 'Sort Order', 'edb' ),
                'id'    => $prefix . 'sort_order',
                'type'  => 'number',
                'class' => 'sort-order'
            ),
            array(
                'name'  => __( 'Card Type', 'edb' ),
                'id'    => $prefix . 'card_type',
                'type'  => 'select',
                'class' => 'card-type',
                'options' => array('standard'=>'standard','square'=>'square','full'=>'full','full square'=>'full square')
            ),
        )
    );
    
    $meta_boxes[] = array(
        'id'         => 'EDB',
        'title'      => __( 'EDB', 'edb' ),
        'post_types' => array( 'edb_materials' ),
        'context'    => 'normal',
        'priority'   => 'high',
        'fields' => array(
            array(
                'name'  => __( 'NUMBER', 'edb' ),
                'id'    => $prefix . 'material_number',
                'type'  => 'text',
                'class' => 'material-number'
            ),
            array(
                'name'  => __( 'material_attribute', 'edb' ),
                'id'    => $prefix . 'material_attribute',
                'type'  => 'text',
                'class' => 'material-attribute'
            ),
        )
    );
    
    $meta_boxes[] = array(
        'id'         => 'faq',
        'title'      => __( 'FAQ', 'edb' ),
        'post_types' => array( 'edb_faqs' ),
        'context'    => 'normal',
        'priority'   => 'high',
        'fields' => array(
            array(
                'name'  => __( 'Question', 'edb' ),
                'id'    => $prefix . 'faq_question',
                'type'  => 'textarea',
                'class' => 'faq-question'
            ),
            array(
                'name'  => __( 'Answer', 'edb' ),
                'id'    => $prefix . 'faq_answer',
                'type'  => 'textarea',
                'class' => 'faq-answer'
            ),
            array(
                'name'  => __( 'Background Color', 'edb' ),
                'id'    => $prefix . 'faq_background_color',
                'type'  => 'color',
                'class' => 'faq-color'
            ),
            array(
                'name'  => __( 'Text Color', 'edb' ),
                'id'    => $prefix . 'faq_text_color',
                'type'  => 'color',
                'class' => 'faq-color'
            ),
            array(
                'name'  => __( 'URL', 'edb' ),
                'id'    => $prefix . 'sort_order',
                'type'  => 'number',
                'class' => 'sort-order'
            ),
        )
    );
    
    $meta_boxes[] = array(
        'id'         => 'inspiration',
        'title'      => __( 'Inspiration', 'edb' ),
        'post_types' =>  'edb_inspirations',
        'context'    => 'normal',
        'priority'   => 'high',
        'fields' => array(
            array(
                'name'  => __( 'Images', 'edb' ),
                'id'    => $prefix . 'inspiration_images',
                'type'  => 'image_advanced',
                'multiple'=>true,
                'class' => 'inspiration-images'
            ),
            array(
                'name'  => __( 'URL', 'edb' ),
                'id'    => $prefix . 'sort_order',
                'type'  => 'number',
                'class' => 'sort-order'
            ),
        )
    );
    
    $meta_boxes[] = array(
        'id'         => 'edb-extra',
        'title'      => __( 'EDB', 'edb' ),
        'post_types' => array( 'product' ),
        'context'    => 'normal',
        'priority'   => 'high',
        'fields' => array(
            array(
                'name'  => __( 'Why we love Title', 'edb' ),
                'id'    => $prefix . 'why_we_love_title',
                'type'  => 'text',
                'class' => 'why-we-love-title'
            ),
            array(
                'name'  => __( 'Why we love content', 'edb' ),
                'id'    => $prefix . 'why_we_love_content',
                'type'  => 'textarea',
                'class' => 'why-we-love-content'
            ),
            array(
                'name'  => __( 'Wireframe', 'edb' ),
                'id'    => $prefix . 'wireframe',
                'type'  => 'image',
                'class' => 'wireframe'
            ),
            array(
                'name'  => __( 'Anatomy EN', 'edb' ),
                'id'    => $prefix . 'anatomy_en',
                'type'  => 'image',
                'class' => 'anatomy-en'
            ),
            array(
                'name'  => __( 'Anatomy FR', 'edb' ),
                'id'    => $prefix . 'anatomy_fr',
                'type'  => 'image',
                'class' => 'anatomy-fr'
            ),
            array(
                'name'  => __( 'Is Bucket', 'edb' ),
                'id'    => $prefix . 'is_bucket',
                'type'  => 'checkbox',
                'class' => 'is-bucket'
            ),
            array(
                'name'  => __( 'Bucket Slug', 'edb' ),
                'id'    => $prefix . 'bucket_slug',
                'type'  => 'text',
                'class' => 'bucket-slug'
            ),
            array(
                'name'  => __( 'Group Ids', 'edb' ),
                'id'    => $prefix . 'group_ids',
                'type'  => 'text',
                'class' => 'bucket-groupids'
            ),
            array(
                'name'  => __( 'Base Price', 'edb' ),
                'id'    => $prefix . 'base_price',
                'type'  => 'number',
                'class' => 'bucket-baseprice'
            )
        )
    );
    

    return $meta_boxes;
}


add_filter( 'rwmb_meta_boxes', 'edb_register_meta_boxes' );