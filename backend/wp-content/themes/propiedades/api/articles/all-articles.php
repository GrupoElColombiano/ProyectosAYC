<?php

add_action('rest_api_init', 'articles_endpoint');

function articles_endpoint() {
  register_rest_route('propiedades', '/articles', array(
    'methods' => 'GET',
    'callback' => 'custom_home_endpoint_callback',
  ));
}

function custom_home_endpoint_callback($request) {
  $data = array();

  $limit = $request->get_param('limit');
  $search_term = $request->get_param('s');

  $limit = (int) $limit;
  if (!$limit || $limit < 1) {
    $limit = 10; // Valor predeterminado de limit
  }

  // Obtén los posts
  $args = array(
    'post_type' => 'post',
    'posts_per_page' => $limit,
    'orderby' => 'date',
    'order' => 'DESC',
    's' => $search_term,
  );

  $query = new WP_Query($args);

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();


      // Obtén la categoría del post
      $categories = wp_get_post_categories(get_the_ID());
      $category_names = array();
      foreach ($categories as $category_id) {
        $category = get_category($category_id);
        $category_names[] = $category->name;
      }

      // Image srcset
      $thumbnail_id = get_post_thumbnail_id(get_the_ID());
      $thumbnail_srcset = wp_get_attachment_image_srcset($thumbnail_id, 'medium');
      $image = array(
        'url' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
        'srcset' => $thumbnail_srcset,
      );

      // Crea un array con los datos del post actual
      $post_data = array(
        'title' => get_the_title(),
        'slug' => get_post_field('post_name'),
        'link' => get_permalink(),
        'categories' => $category_names,
        'image' => $image,
      );

      // Agrega los datos del post actual al array de datos
      $data[] = $post_data;
    }
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
