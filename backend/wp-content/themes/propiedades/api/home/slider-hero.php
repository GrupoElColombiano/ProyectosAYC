<?php


// GET SLIDER HERO ACF

add_action('rest_api_init', 'hero_slider_home');

function hero_slider_home() {
  register_rest_route('propiedades', '/home/slider-hero', array(
    'methods' => 'GET',
    'callback' => 'hero_slider_home_callback',
  ));
}

function hero_slider_home_callback($request) {
  $data = array();

  $lang = $request->get_param('lang');

  if (!$lang) {
    $lang = 'es';
  }

  // Get home page for slug
  $home_page = get_page_by_path('home');


  if ($home_page) {
    // Obtén los campos personalizados utilizando Advanced Custom Fields y lang

    $field1 = get_field('slider_hero', $home_page->ID, $lang);



    // Agrega los campos personalizados a los datos de respuesta
    if (is_array($field1)) {
      foreach ($field1 as $item) {
        $thumbnail_id = $item['image']; // id de la imagen
        $thumbnail_srcset = wp_get_attachment_image_srcset($thumbnail_id, 'medium');
        $image = wp_get_attachment_image_src($thumbnail_id, 'xxlarge');

        // Agrega el srcset al item de datos
        $item['image'] = array(
          'url' => $image[0],
          'srcset' => $thumbnail_srcset,
        );

        $data[] = $item;
      }
    }
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
