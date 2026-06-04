<?php
/**
 * Gallery block — Figma Gallery (715:891).
 * Rows of up to 3 images; layout avoids a final row of one (except a single-image gallery).
 *
 * @param array $block The block settings and attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'snell_gallery_row_sizes' ) ) {
	/**
	 * Items per row for n images. Prefers rows of 3; last row is never a lone image when n > 1.
	 *
	 * @param int $count Image count.
	 * @return int[] e.g. [3, 2, 2] for 7 images.
	 */
	function snell_gallery_row_sizes( $count ) {
		$count = (int) $count;
		if ( $count <= 0 ) {
			return array();
		}
		if ( $count === 1 ) {
			return array( 1 );
		}
		if ( $count === 2 ) {
			return array( 2 );
		}

		$remainder = $count % 3;
		if ( $remainder === 0 ) {
			$rows = (int) ( $count / 3 );
			return array_fill( 0, $rows, 3 );
		}
		if ( $remainder === 2 ) {
			$rows_of_3 = (int) ( ( $count - 2 ) / 3 );
			$sizes     = array_fill( 0, $rows_of_3, 3 );
			$sizes[]   = 2;
			return $sizes;
		}

		// Remainder 1 (4, 7, 10, …): …3, 2, 2 instead of …3, 1.
		$rows_of_3 = (int) ( ( $count - 4 ) / 3 );
		$sizes     = array_fill( 0, $rows_of_3, 3 );
		$sizes[]   = 2;
		$sizes[]   = 2;
		return $sizes;
	}
}

$block_id = ! empty( $block['anchor'] ) ? $block['anchor'] : 'gallery-' . $block['id'];
$classes  = array( 'c-gallery' );

if ( ! empty( $block['className'] ) ) {
	$classes[] = $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$classes[] = 'align' . $block['align'];
}

$images = function_exists( 'get_field' ) ? get_field( 'gallery_images' ) : array();
if ( ! is_array( $images ) ) {
	$images = array();
}

$items = array();
foreach ( $images as $img ) {
	if ( ! is_array( $img ) ) {
		continue;
	}
	$id = isset( $img['ID'] ) ? (int) $img['ID'] : ( isset( $img['id'] ) ? (int) $img['id'] : 0 );
	if ( $id <= 0 && empty( $img['url'] ) ) {
		continue;
	}
	$url = '';
	if ( $id > 0 ) {
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( ! $url ) {
			$url = wp_get_attachment_url( $id );
		}
	}
	if ( ! $url && ! empty( $img['url'] ) ) {
		$url = $img['url'];
	}
	if ( ! $url ) {
		continue;
	}
	$alt = '';
	if ( $id > 0 ) {
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
	}
	if ( $alt === '' && ! empty( $img['alt'] ) ) {
		$alt = $img['alt'];
	}
	if ( $alt === '' && ! empty( $img['title'] ) ) {
		$alt = $img['title'];
	}
	$items[] = array(
		'id'  => $id,
		'url' => $url,
		'alt' => is_string( $alt ) ? $alt : '',
	);
}

$count = count( $items );

if ( is_admin() && $count === 0 ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;"><?php esc_html_e( 'Gallery', 'client_theme' ); ?></strong>
		<p style="margin:0;"><?php esc_html_e( 'Add images to the gallery.', 'client_theme' ); ?></p>
	</div>
	<?php
	return;
endif;

if ( $count === 0 ) {
	return;
}

$classes[]  = 'c-gallery--count-' . $count;
$row_sizes  = snell_gallery_row_sizes( $count );
$slice_at   = 0;
$item_num   = 0;
?>

<section id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="c-gallery__bg" aria-hidden="true"></div>
	<div class="c-gallery__inner wrap">
		<?php foreach ( $row_sizes as $row_index => $row_size ) : ?>
			<?php
			$row_items = array_slice( $items, $slice_at, $row_size );
			$slice_at += $row_size;
			if ( empty( $row_items ) ) {
				continue;
			}
			$row_mod = $row_index === 0 ? 'top' : 'bottom';
			?>
			<ul class="c-gallery__row c-gallery__row--<?php echo esc_attr( $row_mod ); ?> c-gallery__row--count-<?php echo (int) count( $row_items ); ?>">
				<?php foreach ( $row_items as $item ) : ?>
					<?php $item_num++; ?>
					<li class="c-gallery__item c-gallery__item--<?php echo (int) $item_num; ?>">
						<img
							class="c-gallery__img"
							src="<?php echo esc_url( $item['url'] ); ?>"
							alt="<?php echo esc_attr( $item['alt'] ); ?>"
							loading="lazy"
							decoding="async"
						>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	</div>
</section>
