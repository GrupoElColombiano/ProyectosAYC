<?php


// GET EPAPER ACF

add_action('rest_api_init', 'epaper_home');

function epaper_home() {
  register_rest_route('propiedades', '/home/epaper', array(
    'methods' => 'GET',
    'callback' => 'epaper_home_callback',
  ));
}

function epaper_home_callback($request) {
  $data = array();
  $lang = $request->get_param('lang');

  if (!$lang) {
    $lang = 'es';
  }
  // Get home page for slug
  $home_page = get_page_by_path('home');

  if ($home_page) {
    // Obtén los campos personalizados utilizando Advanced Custom Fields
    $field1 = get_field('e-paper', $home_page->ID, $lang);

    if (is_array($field1)) {
      $image_paper_id = $field1['image_paper'];
      $image_paper = wp_get_attachment_image_src($image_paper_id, 'small');
      $image_paper_srcset = wp_get_attachment_image_srcset($image_paper_id, 'medium');

      $image_paper2_id = $field1['image_paper_secundary'];
      $image_paper2 = wp_get_attachment_image_src($image_paper2_id, 'small');
      $image_paper2_srcset = wp_get_attachment_image_srcset($image_paper2_id, 'medium');


      $field1['image_paper'] = array(
        'url' => $image_paper[0],
        'srcset' => $image_paper_srcset,
      );

      $field1['image_paper_secundary'] = array(
        'url' => $image_paper2[0],
        'srcset' => $image_paper2_srcset,
      );
    }
    // Agrega los campos personalizados a los datos de respuesta
    $data = $field1;
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
