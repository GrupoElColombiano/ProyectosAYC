<?php


// GET SLIDER HERO ACF

add_action('rest_api_init', 'home_last_posts');

function home_last_posts() {
  register_rest_route('propiedades', '/home/last-posts', array(
    'methods' => 'GET',
    'callback' => 'home_last_posts_callback',
  ));
}

function home_last_posts_callback($request) {
  $data = array();

  $lang = $request->get_param('lang');

  if (!$lang) {
    $lang = 'es';
  }

  // Get home page for slug
  $home_page = get_page_by_path('home');

  if ($home_page) {
    // Obtén los campos personalizados utilizando Advanced Custom Fields
    $field1 = get_field('last_posts', $home_page->ID, $lang);

    // Agrega los campos personalizados a los datos de respuesta
    $data = $field1;
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
