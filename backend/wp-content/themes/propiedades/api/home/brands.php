<?php


// GET BRANDS HERO ACF

add_action('rest_api_init', 'brands_home');

function brands_home() {
  register_rest_route('propiedades', '/home/brands', array(
    'methods' => 'GET',
    'callback' => 'brands_home_callback',
  ));
}

function brands_home_callback($request) {
  $data = array();

  $lang = $request->get_param('lang');

  if (!$lang) {
    $lang = 'es';
  }


  // Get home page for slug
  $home_page = get_page_by_path('home');

  if ($home_page) {
    // Obtén los campos personalizados utilizando Advanced Custom Fields
    $field1 = get_field('brands', $home_page->ID, $lang);

    // Agrega los campos personalizados a los datos de respuesta
    $data['brands'] = $field1;
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
