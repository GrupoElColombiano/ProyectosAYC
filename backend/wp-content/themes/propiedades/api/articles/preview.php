<?php

add_action('rest_api_init', 'article_endpoint_preview');

function article_endpoint_preview() {
  register_rest_route('propiedades', '/articles/article/preview', array(
    'methods' => 'GET',
    'callback' => 'get_one_article_preview',
  ));
}

function get_one_article_preview($request) {
  $data = array();

  // Obtén el parámetro de la URL
  $id = $request->get_param('id');

  // Obtén el post con el slug especificado

  // preview post status

  $args = array(
    'post_type' => 'post',
    'post_status' => 'draft',
    'posts_per_page' => 1,
    'preview' => true,
    'p' => $id
  );

  $query = new WP_Query($args);

  if ($query->have_posts()) {
    $post = $query->posts[0];


    // Obtén la categoría del post
    $categories = wp_get_post_categories($post->ID);
    $category_names = array();
    foreach ($categories as $category_id) {
      $category = get_category($category_id);
      $category_names[] = $category->name;
    }

    // Agrega los campos personalizados y categoría a los datos de respuesta del post actual
    // get image caption
    $image_id = get_post_thumbnail_id($post->ID);
    $image_caption = wp_get_attachment_caption($image_id);
    $thumbnail_srcset = wp_get_attachment_image_srcset($image_id, 'medium');

    $image = array(
      'url' => get_the_post_thumbnail_url($post->ID, 'medium'),
      'srcset' => $thumbnail_srcset,
      'caption' => $image_caption
    );


    $post_data = array(
      'title' => $post->post_title,
      'slug' => $post->post_name,
      'link' => get_permalink($post->ID),
      'content' => $post->post_content,
      'image' => $image,
      'categories' => $category_names
    );

    // agrega los campos personalizados
    $post_data['intro'] = get_field('intro', $post->ID);


    // Agrega los datos del post actual al array de datos
    $data = $post_data;
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
