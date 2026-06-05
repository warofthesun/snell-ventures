<?php 
// ACF Helpers and filters
add_filter('acf/load_field/name=post_type', function ($field) {
  $field['choices'] = [];

  $pts = get_post_types(['public' => true], 'objects');

  foreach ($pts as $pt) {
    if (in_array($pt->name, ['attachment'], true)) continue;
    $field['choices'][$pt->name] = $pt->labels->singular_name;
  }

  return $field;
});

add_filter( 'acf/load_field/name=cf7_form', function ( $field ) {
  $field['choices'] = array();

  if ( ! post_type_exists( 'wpcf7_contact_form' ) ) {
    $field['instructions'] = __( 'Install and activate Contact Form 7 to select a form.', 'client_theme' );
    return $field;
  }

  $forms = get_posts(
    array(
      'post_type'      => 'wpcf7_contact_form',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
    )
  );

  foreach ( $forms as $form ) {
    $field['choices'][ (string) $form->ID ] = $form->post_title;
  }

  return $field;
} );