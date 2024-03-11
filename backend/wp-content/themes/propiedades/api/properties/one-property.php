<?php

add_action('rest_api_init', 'one_property_endpoint');

function one_property_endpoint() {
  register_rest_route('propiedades', '/properties/property', array(
    'methods' => 'GET',
    'callback' => 'get_one_property',
  ));
}

function get_one_property($request) {
  $data = array();

  // Obtén el parámetro de la URL
  $slug = $request->get_param('slug');

  // Obtén el post con el slug especificado

  $args = array(
    'name' => $slug,
    'post_type' => 'propiedad',
    'post_status' => 'publish',
    'posts_per_page' => 1
  );

  $query = new WP_Query($args);

  if ($query->have_posts()) {
    $post = $query->posts[0];


    // Obtén la custom taxonomy localizacion
    $localizacion = wp_get_post_terms($post->ID, 'localizacion');
    $localizacion_names = array();
    foreach ($localizacion as $localizacion_id) {
      $localizacion = get_term($localizacion_id);
      $localizacion_names[] = $localizacion->name;
    }

    // Agrega los campos personalizados y categoría a los datos de respuesta del post actual
    $post_data = array(
      'title' => $post->post_title,
      'slug' => $post->post_name,
      'image' => get_the_post_thumbnail_url($post->ID, 'medium'),
      'link' => get_permalink($post->ID),
      'categories' => $localizacion_names
    );


    $informacion_general = get_field('informacion_general', $post->ID);
    $constructora = get_field('constructora', $post->ID);
    $sala_de_ventas = get_field('sala_de_ventas', $post->ID);
    $tags = get_field('tags', $post->ID);
    $direccion = get_field('direccion', $post->ID);
    $caracteristicas = get_field('caracteristicas', $post->ID);
    $planos = get_field('planos', $post->ID);
    $video = get_field('video', $post->ID);
    $galeria = get_field('galeria', $post->ID);

    // galeria srcset
    $galeria_srcset = array();
    foreach ($galeria as $image) {
      $thumbnail_srcset = wp_get_attachment_image_srcset($image, 'large');
      $image_url = wp_get_attachment_image_url($image, 'large');
      $image = array(
        'url' => $image_url,
        'srcset' => $thumbnail_srcset,
      );

      $galeria_srcset[] = $image;
    }

    // planos srcset

    $planos_data = array();

    foreach ($planos as $plano) {

      foreach ($plano as $image) {
        $thumbnail_srcset = wp_get_attachment_image_srcset($image, 'large');
        $image_url = wp_get_attachment_image_url($image, 'large');
        $image = array(
          'url' => $image_url,
          'srcset' => $thumbnail_srcset,
        );

        $plano['image'] = $image;
        unset($plano['imagen_plano']);
      }

      $planos_data[] = $plano;
    }


    // get tags names from ids
    $tags_names = array();
    foreach ($tags as $tag_id) {
      $tag = get_term($tag_id);
      $tags_names[] = $tag->name;
    }

    // get sala de ventas name from id
    $sala_de_ventas_array = array();
    if ($sala_de_ventas) {
      $sala_de_ventas_query = new WP_Query(array(
        'post_type' => 'sala-de-venta',
        'post__in' => $sala_de_ventas,
      ));

      if ($sala_de_ventas_query->have_posts()) {
        $sala_de_ventas_query->the_post();
        $sala_de_ventas_array = array(
          'title' => get_the_title(),
          'slug' => get_post_field('post_name', get_post()),
          'image' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
          'link' => get_permalink(get_the_ID()),
        );

        // add acf fields to sala de ventas
        $sala_de_ventas_array['informacion_general'] = get_field('informacion_general',  $sala_de_ventas_query->the_ID());


        wp_reset_postdata();
      }
    }

    // get constructora name from id
    $constructora_array = array();
    if ($constructora) {
      $constructora_query = new WP_Query(array(
        'post_type' => 'constructora',
        'post__in' => $constructora,
      ));

      if ($constructora_query->have_posts()) {
        $constructora_query->the_post();
        $constructora_array = array(
          'title' => get_the_title(),
          'slug' => get_post_field('post_name', get_post()),
          'image' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
          'link' => get_permalink(get_the_ID()),
        );

        // add acf fields to constructora
        $constructora_array['informacion_general'] = get_field('informacion_general',  $constructora_query->the_ID());

        wp_reset_postdata();
      }
    }

    // add custom fields
    $post_data['informacion_general'] = $informacion_general;
    $post_data['tags'] = $tags_names;
    $post_data['sala_de_ventas'] = $sala_de_ventas_array;
    $post_data['constructora'] = $constructora_array;
    $post_data['direccion'] = $direccion;
    $post_data['caracteristicas'] = $caracteristicas;
    $post_data['planos'] = $planos_data;
    $post_data['video'] = $video;
    $post_data['galeria'] = $galeria_srcset;

    // Agrega los datos del post actual al array de datos
    $data = $post_data;
  }

  // Agrega los datos a la respuesta
  $response = new WP_REST_Response($data);
  $response->set_status(200);

  return $response;
}
