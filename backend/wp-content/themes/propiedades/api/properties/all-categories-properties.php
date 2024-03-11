<?php


add_action('rest_api_init', 'all_categories_properties');

function all_categories_properties() {
  register_rest_route('propiedades', '/properties/categories', array(
    'methods' => 'GET',
    'callback' => 'all_categories_properties_callback',
  ));
}

function all_categories_properties_callback($request) {
  $data = array();

  // Obtén el parámetro 'cantidad' de la consulta
  $location_limit = $request->get_param('location_limit');
  $properties_limit = $request->get_param('properties_limit');
  $slug = $request->get_param('slug');

  // Establece un valor predeterminado si el parámetro no está presente o es inválido
  $properties_limit = (int) $properties_limit;
  $location_limit = (int) $location_limit;
  if (!$properties_limit || $properties_limit < 1) {
    $properties_limit = 10; // Valor predeterminado de limit
  }

  // get all categories from properties custom post taxonomy

  $categories = get_terms(array(
    'taxonomy' => 'localizacion',
    'hide_empty' => false,
    'number' => $location_limit,
    'slug' => $slug,
  ));

  foreach ($categories as $category) {
    $category_data = array(
      'localizacion' => $category->name,
      'slug' => $category->slug,
      'propiedades' => array(),
    );

    $args = array(
      'post_type' => 'propiedad',
      'tax_query' => array(
        array(
          'taxonomy' => 'localizacion',
          'field' => 'slug',
          'terms' => $category->slug,
        ),
      ),
      'posts_per_page' => $properties_limit,
    );

    $query = new WP_Query($args);


    if ($query->have_posts()) {
      while ($query->have_posts()) {
        $query->the_post();

        $property = array(
          'title' => get_the_title(),
          'slug' => get_post_field('post_name'),
          'link' => get_permalink(),
        );

        // Agrega los acf de la propiedad
        $informacion_general = get_field('informacion_general');
        $direccion = get_field('direccion');
        $galeria = get_field('galeria');

        // galeria srcset
        $galeria_srcset = array();
        foreach ($galeria as $image) {
          $thumbnail_srcset = wp_get_attachment_image_srcset($image, 'medium');
          $image_url = wp_get_attachment_image_url($image, 'medium');
          $image = array(
            'url' => $image_url,
            'srcset' => $thumbnail_srcset,
          );

          $galeria_srcset[] = $image;
        }

        // Agrega los campos personalizados al arreglo de datos de la propiedad
        $property['informacion_general'] = $informacion_general;
        $property['direccion'] = $direccion;
        $property['galeria'] = $galeria_srcset;

        $category_data['propiedades'][] = $property;
      }
    }

    wp_reset_postdata();

    // Si contiene slug agregar al array de datos
    if (!$slug) {
      $data[] = $category_data;
    } else {
      $data = $category_data;
    }
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);
  return $response;
}
