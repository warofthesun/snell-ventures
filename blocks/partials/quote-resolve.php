<?php
/**
 * Shared helpers for Quote blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'snell_quote_resolve_post_id' ) ) {
	/**
	 * @param mixed $post_field Relationship value.
	 * @return int
	 */
	function snell_quote_resolve_post_id( $post_field ) {
		if ( is_object( $post_field ) && isset( $post_field->ID ) ) {
			return (int) $post_field->ID;
		}
		if ( is_array( $post_field ) && ! empty( $post_field ) ) {
			$first = reset( $post_field );
			if ( is_object( $first ) && isset( $first->ID ) ) {
				return (int) $first->ID;
			}
			if ( is_numeric( $first ) ) {
				return (int) $first;
			}
		}
		if ( is_numeric( $post_field ) ) {
			return (int) $post_field;
		}
		return 0;
	}
}

if ( ! function_exists( 'snell_quote_resolve_slides' ) ) {
	/**
	 * Normalize repeater rows into slideshow slides.
	 *
	 * @param array|null $quote_items ACF repeater rows.
	 * @return array<int, array{text:string}>
	 */
	function snell_quote_resolve_slides( $quote_items ) {
		$slides = array();
		if ( ! is_array( $quote_items ) ) {
			return $slides;
		}

		foreach ( $quote_items as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$source = isset( $row['source'] ) ? (string) $row['source'] : 'custom';
			$text   = '';

			if ( $source === 'cpt' ) {
				$post_id = snell_quote_resolve_post_id( $row['quote_post'] ?? null );
				if ( $post_id > 0 ) {
					$post = get_post( $post_id );
					if ( $post && $post->post_type === 'quotes' ) {
						$text = apply_filters( 'the_content', $post->post_content );
					}
				}
			} else {
				$text = isset( $row['custom_text'] ) ? (string) $row['custom_text'] : '';
			}

			$text = trim( wp_kses_post( $text ) );
			if ( $text === '' ) {
				continue;
			}

			$slides[] = array(
				'text' => $text,
			);
		}

		return $slides;
	}
}

if ( ! function_exists( 'snell_quote_enqueue_view_script' ) ) {
	function snell_quote_enqueue_view_script() {
		static $enqueued = false;
		if ( $enqueued || is_admin() ) {
			return;
		}
		$enqueued   = true;
		$block_path = get_template_directory() . '/blocks/quote';
		$block_uri  = get_template_directory_uri() . '/blocks/quote';
		wp_enqueue_script(
			'client-quote-view',
			$block_uri . '/view.js',
			array(),
			file_exists( $block_path . '/view.js' ) ? filemtime( $block_path . '/view.js' ) : null,
			true
		);
	}
}
