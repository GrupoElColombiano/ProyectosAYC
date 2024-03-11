<?php


// METADATA HOME

add_action('rest_api_init', 'register_home_metadata_endpoint');

function register_home_metadata_endpoint() {
  register_rest_route('propiedades', '/home/metadata', array(
    'methods' => 'GET',
    'callback' => 'get_home_metadata_callback',
  ));
}


function get_home_metadata_callback($request) {


  if (function_exists('wpseo_init')) {

    $lang = $request->get_param('lang');
    add_filter('wpseo_locale', function () use ($lang) {
      return $lang;
    });



    // Obtén los metadatos de la página de inicio utilizando la función get_post_meta() de Yoast SEO
    $title = get_post_meta(get_option('page_on_front'), '_yoast_wpseo_opengraph-title', true);
    $description = get_post_meta(get_option('page_on_front'), '_yoast_wpseo_metadesc', true);

    // get og_locale

    $og_locale = get_post_meta(get_option('page_on_front'), '_yoast_wpseo_opengraph-locale', true);
    $schema = get_post_meta(get_option('page_on_front'), '_yoast_wpseo_json-schema', true);
    $robots = get_post_meta(get_option('page_on_front'), '_yoast_wpseo_meta-robots-adv', true);




    // Devuelve los metadatos
    $response_data = array(
      'title' => $title,
      'description' => $description,
      'og_locale' => $og_locale,
      'schema' => $schema,
      'robots' => $robots,
    );

    // Devuelve la respuesta
    $response = new WP_REST_Response($response_data);
    $response->set_status(200);

    return $response;
  } else {
    return new WP_Error('yoast_not_active', 'Yoast SEO no está activo.', array('status' => 404));
  }




  // // Agrega los datos a la respuesta
  // $response = new WP_REST_Response($data);
  // $response->set_status(200);

  // return $response;
}
