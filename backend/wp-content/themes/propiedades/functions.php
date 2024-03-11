<?php


// IMPORT API SERVICES
require_once 'api/index.php';




// Admin CSS styles
add_action('admin_enqueue_scripts', 'my_admin_style');
function my_admin_style() {
  wp_enqueue_style('admin-theme', get_stylesheet_directory_uri() . '/assets/css/admin-theme.css');
}



// hide admin bar
function hide_admin_bar() {
  return false;
}
add_filter('show_admin_bar', 'hide_admin_bar');

// ACF Settings

function disable_plugin_updates($value) {
  unset($value->response['advanced-custom-fields-pro/acf.php']);
  return $value;
}
add_filter('site_transient_update_plugins', 'disable_plugin_updates');

// ACF Options Page

if (function_exists('acf_add_options_page')) {
  acf_add_options_page(array(
    'page_title' => 'Opciones Generales',
    'menu_title' => 'Opciones Generales',
    'menu_slug' => 'opciones-generales',
    'capability' => 'edit_posts',
    'redirect' => false,
  ));
}

// custom logo wp admin
function my_login_logo() { ?>
  <style type="text/css">
    #login h1,
    .login h1 {
      content: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo.svg);
      width: 320px;
      margin: 0 auto;
    }
  </style>
<?php }
add_action('login_enqueue_scripts', 'my_login_logo');



// hiden admin bar logo
add_action('admin_bar_menu', 'remove_wp_logo', 999);
function remove_wp_logo($wp_admin_bar) {
  $wp_admin_bar->remove_node('wp-logo');
}



// add menu
function register_my_menu() {
  register_nav_menu('menupp', __('Navegación'));
}
add_action('init', 'register_my_menu');



// Add image sizes

add_theme_support('post-thumbnails');
add_image_size('thumbnail', 300, 300, true);
add_image_size('small', 320, 0, true);
add_image_size('medium', 600, 0, true);
add_image_size('large', 1024, 0, true);
add_image_size('xlarge', 1440, 0, true);
add_image_size('xxlarge', 1920, 0, true);


// remove ...
function new_excerpt_more($more) {
  return '..';
}
add_filter('excerpt_more', 'new_excerpt_more');


// active svg
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');



// Rest url support Gutenberg
add_filter('rest_url', function ($url) {
  $url = str_replace(home_url(), site_url(), $url);
  return $url;
});


// custom url for acf custom post type
add_filter('post_type_link', 'my_rest_acfpost_link', 10, 2);
function my_rest_acfpost_link($post_link, $post) {

  if (is_object($post) && $post->post_type == 'propiedad') {
    $terms = wp_get_post_terms($post->ID, 'localizacion');

    $post_link = str_replace(home_url(), '', $post_link);
    $post_link = str_replace('propiedad', 'proyectos/' . $terms[0]->slug, $post_link);

    if ($terms) {
      return  home_url() . $post_link;
    }
  }
  return $post_link;
}



// custom url for post
add_filter('post_link', 'my_rest_post_link', 10, 2);
function my_rest_post_link($post_link, $post) {
  if (is_object($post) && $post->post_type == 'post') {

    $post_link = str_replace(home_url(), '', $post_link);
    return  home_url() . '/articulos' . $post_link;
  }
  return $post_link;
}


// custom taxonomies url localizacion
add_filter('term_link', 'my_rest_term_link', 10, 3);

function my_rest_term_link($termlink, $term, $taxonomy) {
  if ($taxonomy == 'localizacion') {
    $termlink = str_replace(home_url(), '', $termlink);
    $termlink = str_replace('localizacion', 'proyectos', $termlink);
    return home_url() . $termlink;
  }
  return $termlink;
}


// custom prewview url for acf custom post type
add_filter('post_type_link', 'my_rest_acfpost_link_draf', 10, 2);
function my_rest_acfpost_link_draf($post_link, $post) {

  if (is_object($post) && $post->post_type == 'propiedad' && $post->post_status === 'draft') {
    $terms = wp_get_post_terms($post->ID, 'localizacion');

    // $post_link = str_replace(home_url() . '/?post_type=proyectos', '', $post->ID);
    // $post_link = str_replace('propiedad', '/proyectos/preview/' . $terms[0]->slug, $post_link);

    if ($terms) {
      return  home_url() . '/proyectos/' . $terms[0]->slug . '/preview/' . $post->ID;
    }
  }
  return $post_link;
}

// custom preview url for post guetnberg

add_filter('post_link', 'my_rest_post_link_draf', 10, 2);
function my_rest_post_link_draf($post_link, $post) {
  if (is_object($post) && $post->post_status === 'draft') {

    return  home_url() . '/articulos/preview/' . $post->ID;
  }
  return $post_link;
}


?>
