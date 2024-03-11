<?php

add_action('rest_api_init', 'register_advertising_endpoint');

function register_advertising_endpoint() {
  register_rest_route('propiedades', '/advertising', array(
    'methods' => 'GET',
    'callback' => 'get_advertising_callback',
  ));
}

function get_advertising_callback() {

  $data = array();

  // get acf field for options page

  $advertising = get_field('publicidad', 'option');

  // Image srcset for each image
  $advertising_array = array();

  foreach ($advertising as $ad) {
    $image_id = $ad['imagen'];
    $image_srcset = wp_get_attachment_image_srcset($image_id, 'medium');
    $image_url = wp_get_attachment_image_url($image_id, 'medium');

    $ad = array(
      'link' => $ad['link'],
      'image' => array(
        'url' => $image_url,
        'srcset' => $image_srcset,
      ),
    );

    $advertising_array[] = $ad;
  }





  // add fields to data array

  $data = $advertising_array;





  // Devuelve la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
