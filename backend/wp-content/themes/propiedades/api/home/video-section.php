<?php


// GET VODEP SECTION ACF

add_action('rest_api_init', 'video_section');

function video_section() {
  register_rest_route('propiedades', '/home/video-section', array(
    'methods' => 'GET',
    'callback' => 'video_section_callback',
  ));
}

function video_section_callback($request) {
  $data = array();

  $lang = $request->get_param('lang');

  if (!$lang) {
    $lang = 'es';
  }

  // Get home page for slug
  $home_page = get_page_by_path('home');

  if ($home_page) {
    // Obtén los campos personalizados utilizando Advanced Custom Fields
    $field1 = get_field('video-section', $home_page->ID, $lang);

    if (is_array($field1)) {
      $thumbnail_id = $field1['preview_video']; // id de la imagen
      $thumbnail_srcset = wp_get_attachment_image_srcset($thumbnail_id, 'medium');
      $image = wp_get_attachment_image_src($thumbnail_id, 'large');

      // Revome the preview_video field
      unset($field1['preview_video']);
      // Agrega el srcset al field
      $field1['image'] = array(
        'url' => $image[0],
        'srcset' => $thumbnail_srcset,
      );
    }

    // Agrega el field a la respuesta
    $data = $field1;
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
