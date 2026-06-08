<?php 
// ACF Helpers and filters

/**
 * Keep Hero Options field group aligned with acf-json when the DB copy is stale.
 * Fixes incorrect dropdown labels and seamless clone fields that ignore conditional logic.
 */
function client_fix_hero_options_field_group( $group ) {
	if ( ! is_array( $group ) || ( $group['key'] ?? '' ) !== 'group_68260ada5ea86' ) {
		return $group;
	}

	$json_path = get_template_directory() . '/acf-json/group_68260ada5ea86.json';
	if ( ! file_exists( $json_path ) ) {
		return $group;
	}

	$json = json_decode( (string) file_get_contents( $json_path ), true );
	if ( ! is_array( $json ) || empty( $json['fields'] ) ) {
		return $group;
	}

	$json_mtime = (int) filemtime( $json_path );
	$db_post    = function_exists( 'acf_get_field_group_post' ) ? acf_get_field_group_post( 'group_68260ada5ea86' ) : null;
	$db_mtime   = is_object( $db_post ) ? (int) strtotime( (string) $db_post->post_modified_gmt . ' GMT' ) : 0;

	if ( $json_mtime <= $db_mtime ) {
		return $group;
	}

	$group['fields']   = $json['fields'];
	$group['modified'] = $json['modified'] ?? $json_mtime;

	return $group;
}
add_filter( 'acf/load_field_group/key=group_68260ada5ea86', 'client_fix_hero_options_field_group', 30 );

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
