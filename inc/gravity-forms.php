<?php
/**
 * Gravity Forms theme integration (Orbital + site tokens).
 *
 * @see https://docs.gravityforms.com/gform_default_styles/
 * @see https://docs.gravityforms.com/form-themes-and-style-settings/
 * @see https://docs.gravityforms.com/adding-a-form-to-the-theme-file/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Orbital style settings keyed to theme color tokens.
 *
 * @param string $context 'light' (default embeds) or 'dark' (contact-form block).
 * @return array<string, string>
 */
function client_get_gf_style_settings( $context = 'light' ) {
	$is_dark = ( $context === 'dark' );

	return array(
		'theme'                        => 'orbital',
		'inputSize'                    => 'md',
		'inputBorderRadius'            => '8',
		'inputBorderColor'             => $is_dark ? '#ffffff' : '#c6cacf', // $white / $grey-200
		'inputBackgroundColor'         => '#ffffff',
		'inputColor'                   => '#12273d', // $primary-700
		'inputPrimaryColor'            => '#b86f52', // $accent-500
		'labelFontSize'                => '19',
		'labelColor'                   => $is_dark ? '#ffffff' : '#12273d', // $white / $primary-700
		'descriptionFontSize'          => '14',
		'descriptionColor'             => $is_dark ? '#a2adb9' : '#50657b', // $primary-200 / $primary-300
		'buttonPrimaryBackgroundColor' => '#b86f52', // $accent-500
		'buttonPrimaryColor'           => '#ffffff',
	);
}

/**
 * JSON-encoded Orbital styles for gravity_form() / shortcode.
 *
 * @param string $context 'light' or 'dark'.
 * @return string
 */
function client_get_gf_style_settings_json( $context = 'light' ) {
	$json = wp_json_encode( client_get_gf_style_settings( $context ) );
	return is_string( $json ) ? $json : '{}';
}

/**
 * Site-wide Orbital defaults: labels/headers assume a light background.
 *
 * Forms in the Contact Form block pass dark styles explicitly and override this.
 *
 * @param array|string|false $styles Existing styles.
 * @return array<string, string>
 */
function client_gform_default_styles( $styles ) {
	$defaults = client_get_gf_style_settings( 'light' );

	if ( is_string( $styles ) && $styles !== '' ) {
		$decoded = json_decode( $styles, true );
		if ( is_array( $decoded ) ) {
			return array_merge( $defaults, $decoded );
		}
	}

	if ( is_array( $styles ) ) {
		return array_merge( $defaults, $styles );
	}

	return $defaults;
}
add_filter( 'gform_default_styles', 'client_gform_default_styles' );

/**
 * Render a Gravity Form with Orbital theme and context styles.
 *
 * @param int    $form_id Form ID.
 * @param string $context 'light' or 'dark'.
 * @return void
 */
function client_render_gravity_form( $form_id, $context = 'light' ) {
	$form_id = absint( $form_id );
	if ( $form_id <= 0 ) {
		return;
	}

	$styles_json = client_get_gf_style_settings_json( $context );

	if ( function_exists( 'gravity_form_enqueue_scripts' ) ) {
		gravity_form_enqueue_scripts( $form_id, true );
	}

	if ( function_exists( 'gravity_form' ) ) {
		gravity_form( $form_id, false, false, false, null, true, 0, true, 'orbital', $styles_json );
		return;
	}

	if ( shortcode_exists( 'gravityform' ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode markup; styles JSON is theme-generated.
		echo do_shortcode(
			sprintf(
				'[gravityform id="%d" title="false" description="false" ajax="true" theme="orbital" styles=\'%s\']',
				$form_id,
				$styles_json
			)
		);
	}
}
