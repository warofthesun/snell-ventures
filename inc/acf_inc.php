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

/**
 * Site-wide form plugin for the Contact Form block.
 *
 * @return string 'contact_form_7' or 'gravity_forms'
 */
function client_get_form_plugin() {
	if ( ! function_exists( 'get_field' ) ) {
		return 'contact_form_7';
	}

	$site_settings = get_field( 'site_settings', 'site-settings' );
	if ( ! is_array( $site_settings ) ) {
		$site_settings = get_field( 'site_settings', 'option' );
	}

	$plugin = ( is_array( $site_settings ) && ! empty( $site_settings['form_plugin'] ) )
		? (string) $site_settings['form_plugin']
		: 'contact_form_7';

	return in_array( $plugin, array( 'contact_form_7', 'gravity_forms' ), true )
		? $plugin
		: 'contact_form_7';
}

/**
 * Whether the active form plugin is Gravity Forms.
 *
 * @return bool
 */
function client_uses_gravity_forms() {
	return client_get_form_plugin() === 'gravity_forms';
}

add_filter( 'acf/load_field/name=gf_form', function ( $field ) {
	$field['choices'] = array();

	if ( class_exists( 'GFAPI' ) ) {
		$forms = GFAPI::get_forms( true );
		if ( is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				if ( empty( $form['id'] ) ) {
					continue;
				}
				$field['choices'][ (string) $form['id'] ] = ! empty( $form['title'] )
					? (string) $form['title']
					: sprintf( __( 'Form %d', 'client_theme' ), (int) $form['id'] );
			}
		}
		return $field;
	}

	if ( class_exists( 'RGFormsModel' ) ) {
		$forms = RGFormsModel::get_forms( null, 'title' );
		if ( is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				$id = isset( $form->id ) ? (int) $form->id : 0;
				if ( $id <= 0 ) {
					continue;
				}
				$field['choices'][ (string) $id ] = ! empty( $form->title )
					? (string) $form->title
					: sprintf( __( 'Form %d', 'client_theme' ), $id );
			}
		}
		return $field;
	}

	$field['instructions'] = __( 'Install and activate Gravity Forms to select a form.', 'client_theme' );
	return $field;
} );

add_filter( 'acf/prepare_field/name=cf7_form', function ( $field ) {
	return client_uses_gravity_forms() ? false : $field;
} );

add_filter( 'acf/prepare_field/key=field_snell_contact_cf7_reference', function ( $field ) {
	return client_uses_gravity_forms() ? false : $field;
} );

add_filter( 'acf/prepare_field/name=gf_form', function ( $field ) {
	return client_uses_gravity_forms() ? $field : false;
} );
