<?php

add_action('rest_api_init', 'register_custom_menu_endpoint');

function register_custom_menu_endpoint() {
  register_rest_route('propiedades', '/menu', array(
    'methods' => 'GET',
    'callback' => 'get_custom_menu',
  ));
}

function get_custom_menu($request) {
  // Obtén el nombre del menú desde los parámetros de consulta si se proporciona
  $slug = $request->get_param('slug');

  if (!$slug) {
    $slug = 'menu-principal';
  }

  // Obtén el objeto de menú por su nombre
  $menu = wp_get_nav_menu_object($slug);

  // Verifica si se encontró el menú
  if (!$menu) {
    return new WP_Error('menu_not_found', 'El menú no fue encontrado.', array('status' => 404));
  }

  // Obtén los elementos del menú
  $menu_items = wp_get_nav_menu_items($menu->term_id);

  // Procesa los elementos del menú para construir la respuesta deseada
  $response_data = array();
  foreach ($menu_items as $item) {
    $response_data[] = array(
      'title' => $item->title,
      'url' => $item->url,
    );
  }

  // Devuelve la respuesta
  return new WP_REST_Response($response_data);
}
