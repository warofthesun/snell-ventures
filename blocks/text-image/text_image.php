<?php
/**
 * Text + Image template, single row.
 *
 * @param array $block The block settings and attributes.
 */
if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'text-image' ) ) {
	return;
}

$preheader       = get_field( 'preheader' );
$headline        = get_field( 'headline' );
$headline_size   = get_field( 'headline_size' );
$on_dark         = (bool) get_field( 'on_dark_background' );
$headline_parsed = function_exists( 'client_headline_tag_and_class' ) ? client_headline_tag_and_class( $headline_size, '' ) : array( 'tag' => 'h2', 'class' => '' );
$headline_class  = trim( $headline_parsed['class'] . ' h4 c-text-image__heading' );
$body            = get_field( 'body_copy' );
$content_position = get_field( 'content_vertical' );
$image_position  = get_field( 'image_horizontal' );
$media_type      = get_field( 'media_type' ) ?: 'image';
$image           = function_exists( 'client_text_image_get_image' ) ? client_text_image_get_image() : null;
$slideshow_gallery = get_field( 'slideshow_gallery' );
$show_captions   = (bool) get_field( 'show_captions' );
$video_url       = get_field( 'video_url', false, false );
$slideshow_count = is_array( $slideshow_gallery ) ? count( $slideshow_gallery ) : 0;

$has_video     = ( $media_type === 'video' && ! empty( $video_url ) && is_string( $video_url ) );
$has_image     = ( ( $media_type === 'image' || $media_type === 'gallery' ) && ! empty( $image ) );
$has_slideshow = ( $media_type === 'slideshow' && $slideshow_count > 0 );
$has_media     = $has_video || $has_image || $has_slideshow;

if ( $has_video || $has_slideshow || $media_type === 'slideshow' || $has_image ) {
	$text_col  = 'col-xs-12 col-md-6';
	$image_col = 'col-xs-12 col-md-6';
} else {
	$text_col  = 'col-xs-12 col-md-12';
	$image_col = 'd-none';
}

$classes_cg = array( 'c-content-group', 'c-text-image' );
if ( $content_position === 'middle' ) {
	$classes_cg[] = 'c-content-group--middle';
}
if ( $content_position === 'bottom' ) {
	$classes_cg[] = 'c-content-group--bottom';
}
if ( $image_position === 'left' ) {
	$classes_cg[] = 'c-content-group--reverse';
}

$slideshow_items = array();
if ( $has_slideshow && is_array( $slideshow_gallery ) ) {
	foreach ( $slideshow_gallery as $img ) {
		$url     = isset( $img['url'] ) ? $img['url'] : '';
		$title   = isset( $img['title'] ) ? $img['title'] : '';
		$id      = isset( $img['ID'] ) ? (int) $img['ID'] : ( isset( $img['id'] ) ? (int) $img['id'] : 0 );
		$caption = '';
		$author  = '';
		if ( $id ) {
			$caption = isset( $img['caption'] ) && (string) $img['caption'] !== '' ? $img['caption'] : wp_get_attachment_caption( $id );
			$author  = function_exists( 'get_field' ) ? ( get_field( 'caption_author', $id ) ?: '' ) : '';
			$src     = wp_get_attachment_image_url( $id, 'client_slider_square' );
			if ( $src ) {
				$url = $src;
			}
		}
		if ( $url !== '' ) {
			$slideshow_items[] = array(
				'url'     => $url,
				'title'   => $title !== '' ? $title : __( 'Untitled', 'client_theme' ),
				'caption' => is_string( $caption ) ? $caption : '',
				'author'  => is_string( $author ) ? $author : '',
			);
		}
	}
}

$is_editor  = is_admin();
$body_plain = is_string( $body ) ? trim( wp_strip_all_tags( $body ) ) : '';
$is_empty   = empty( $preheader ) && empty( $headline ) && empty( $body_plain ) && ! $has_media;

if ( $is_editor && $is_empty ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;">Text + Image</strong>
		<p style="margin:0;">Add a headline, body copy, and an image or a video.</p>
	</div>
	<?php
	return;
endif;
?>

<div class="c-text-image__content">
	<div class="<?php echo esc_attr( implode( ' ', $classes_cg ) ); ?> row<?php echo $has_video ? ' c-content-group__row--has-video' : ''; ?><?php echo ( $has_slideshow || $media_type === 'slideshow' ) ? ' c-content-group__row--has-slideshow' : ''; ?>">
		<div class="<?php echo esc_attr( $text_col ); ?> c-content-group__content">
			<?php if ( $preheader || $headline ) : ?>
			<div class="c-text-image__titles">
			<?php endif; ?>
			<?php if ( $preheader ) : ?><h6 class="c-headline-group__preheader<?php echo $on_dark ? ' light' : ''; ?>"><?php echo esc_html( $preheader ); ?></h6><?php endif; ?>
			<?php if ( $headline ) : ?><<?php echo esc_attr( $headline_parsed['tag'] ); ?> class="<?php echo esc_attr( trim( $headline_class . ( $on_dark ? ' light' : '' ) ) ); ?>"><?php echo esc_html( $headline ); ?></<?php echo esc_attr( $headline_parsed['tag'] ); ?>><?php endif; ?>
			<?php if ( $preheader || $headline ) : ?>
			</div>
			<?php endif; ?>
			<?php if ( $body ) : ?><?php echo wp_kses_post( $body ); ?><?php endif; ?>
			<?php $partial_path = get_theme_file_path( '/partials/button_pair.php' ); ?>
			<?php include $partial_path; ?>
		</div>
		<div class="<?php echo esc_attr( $image_col ); ?><?php echo $has_video ? ' c-content-group__media-col--video' : ''; ?>">
			<?php
			$ti_shape_mirror = ( $image_position === 'left' );
			$ti_grad_id      = 'ti-grad-' . ( ! empty( $block['id'] ) ? (string) (int) $block['id'] : wp_unique_id() );
			?>
			<?php if ( $has_video ) :
				$video_embed = wp_oembed_get( $video_url );
				if ( $video_embed ) : ?>
				<div class="c-content-group__video">
					<?php echo wp_kses( $video_embed, array(
						'iframe' => array(
							'src'             => true,
							'width'           => true,
							'height'          => true,
							'frameborder'     => true,
							'allow'           => true,
							'allowfullscreen' => true,
							'loading'         => true,
							'title'           => true,
						),
					) ); ?>
				</div>
				<?php endif; ?>
			<?php elseif ( $has_slideshow && ! empty( $slideshow_items ) ) :
				$slideshow_id = isset( $block['id'] ) ? 'text-image-slider-' . $block['id'] : 'text-image-slider-' . wp_rand( 1000, 9999 );
				if ( ! is_admin() ) {
					$block_path = get_template_directory() . '/blocks/slider';
					$block_uri  = get_template_directory_uri() . '/blocks/slider';
					wp_enqueue_script(
						'client-slider-view',
						$block_uri . '/view.js',
						array(),
						file_exists( $block_path . '/view.js' ) ? filemtime( $block_path . '/view.js' ) : null,
						true
					);
				}
				$first = $slideshow_items[0];
				$first_has_caption = $show_captions && ( (string) $first['caption'] !== '' || (string) $first['author'] !== '' );
				?>
				<div class="c-content-group__visual<?php echo $ti_shape_mirror ? ' c-content-group__visual--mirror' : ''; ?>">
					<div class="c-content-group__visual-shape" aria-hidden="true">
						<div class="c-content-group__visual-shape-inner">
							<svg class="c-content-group__visual-shape-svg" viewBox="0 0 629 449" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
								<defs>
									<linearGradient id="<?php echo esc_attr( $ti_grad_id ); ?>" x1="401.422" y1="-78.1841" x2="401.422" y2="449" gradientUnits="userSpaceOnUse">
										<stop offset="0" stop-color="#12273D"/>
										<stop offset="1" stop-color="#507A73"/>
									</linearGradient>
								</defs>
								<path fill="url(#<?php echo esc_attr( $ti_grad_id ); ?>)" d="M0 256.037L629 0V449H0V256.037Z"/>
							</svg>
						</div>
					</div>
					<div class="c-content-group__visual-frame">
						<div class="c-content-group__slideshow c-content-group__slideshow--square">
							<div class="c-slider c-slider--slideshow"
								id="<?php echo esc_attr( $slideshow_id ); ?>"
								data-slider-type="slideshow"
								data-items="<?php echo esc_attr( wp_json_encode( $slideshow_items ) ); ?>"
								data-autoplay="1"
								data-show-captions="<?php echo $show_captions ? '1' : '0'; ?>"
								role="region"
								aria-label="<?php esc_attr_e( 'Image slideshow', 'client_theme' ); ?>">
								<div class="c-slider__panel">
									<div class="c-slider__image-wrap c-slider__image-wrap--square">
										<div class="c-slider__slide c-slider__slide--current" data-slider-slide>
											<img src="<?php echo esc_url( $first['url'] ); ?>"
												alt="<?php echo esc_attr( $first['title'] ); ?>"
												class="c-slider__image c-slider__image--cover"
												data-slider-image>
										</div>
										<div class="c-slider__slide c-slider__slide--next" data-slider-slide>
											<img src="<?php echo esc_url( $first['url'] ); ?>"
												alt=""
												class="c-slider__image c-slider__image--cover"
												data-slider-image>
										</div>
										<?php if ( $show_captions ) : ?>
										<div class="c-slider__caption<?php echo $first_has_caption ? ' c-slider__caption--visible' : ''; ?>" data-slider-caption aria-live="polite">
											<?php if ( $first_has_caption ) : ?>
												<?php if ( (string) $first['caption'] !== '' ) : ?>
													<p class="c-slider__caption-text"><?php echo esc_html( $first['caption'] ); ?></p>
												<?php endif; ?>
												<?php if ( (string) $first['author'] !== '' ) : ?>
													<p class="c-slider__caption-author"><?php echo esc_html( $first['author'] ); ?></p>
												<?php endif; ?>
											<?php endif; ?>
										</div>
										<?php endif; ?>
										<button type="button" class="c-slider__arrow c-slider__arrow--prev" data-slider-prev aria-label="<?php esc_attr_e( 'Previous slide', 'client_theme' ); ?>"></button>
										<button type="button" class="c-slider__arrow c-slider__arrow--next" data-slider-next aria-label="<?php esc_attr_e( 'Next slide', 'client_theme' ); ?>"></button>
										<nav class="c-slider__dots" aria-label="<?php esc_attr_e( 'Slide navigation', 'client_theme' ); ?>">
											<?php foreach ( $slideshow_items as $i => $item ) : ?>
												<button type="button" class="c-slider__dot<?php echo $i === 0 ? ' c-slider__dot--active' : ''; ?>" data-index="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'client_theme' ), $i + 1 ) ); ?>" aria-current="<?php echo $i === 0 ? 'true' : 'false'; ?>"></button>
											<?php endforeach; ?>
										</nav>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php elseif ( $media_type === 'slideshow' ) : ?>
				<div class="c-content-group__visual<?php echo $ti_shape_mirror ? ' c-content-group__visual--mirror' : ''; ?>">
					<div class="c-content-group__visual-shape" aria-hidden="true">
						<div class="c-content-group__visual-shape-inner">
							<svg class="c-content-group__visual-shape-svg" viewBox="0 0 629 449" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
								<defs>
									<linearGradient id="<?php echo esc_attr( $ti_grad_id ); ?>" x1="401.422" y1="-78.1841" x2="401.422" y2="449" gradientUnits="userSpaceOnUse">
										<stop offset="0" stop-color="#12273D"/>
										<stop offset="1" stop-color="#507A73"/>
									</linearGradient>
								</defs>
								<path fill="url(#<?php echo esc_attr( $ti_grad_id ); ?>)" d="M0 256.037L629 0V449H0V256.037Z"/>
							</svg>
						</div>
					</div>
					<div class="c-content-group__visual-frame">
						<div class="c-content-group__slideshow c-content-group__slideshow--square">
							<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 2rem; border-radius: .5rem; background: rgba(255,255,255,.8); min-height: 200px; display: flex; align-items: center; justify-content: center;">
								<p style="margin: 0; color: #666;">Add images to the slideshow gallery above.</p>
							</div>
						</div>
					</div>
				</div>
			<?php elseif ( $has_image ) :
				$url = client_acf_image_src_text_image( $image );
				$alt = is_array( $image ) ? ( $image['alt'] ?? '' ) : '';
				?>
				<div class="c-content-group__visual<?php echo $ti_shape_mirror ? ' c-content-group__visual--mirror' : ''; ?>">
					<div class="c-content-group__visual-shape" aria-hidden="true">
						<div class="c-content-group__visual-shape-inner">
							<svg class="c-content-group__visual-shape-svg" viewBox="0 0 629 449" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
								<defs>
									<linearGradient id="<?php echo esc_attr( $ti_grad_id ); ?>" x1="401.422" y1="-78.1841" x2="401.422" y2="449" gradientUnits="userSpaceOnUse">
										<stop offset="0" stop-color="#12273D"/>
										<stop offset="1" stop-color="#507A73"/>
									</linearGradient>
								</defs>
								<path fill="url(#<?php echo esc_attr( $ti_grad_id ); ?>)" d="M0 256.037L629 0V449H0V256.037Z"/>
							</svg>
						</div>
					</div>
					<div class="c-content-group__visual-frame">
						<ul class="c-image-grid c-image-grid--one">
							<li class="c-image-grid__item c-image-grid__item--1">
								<img class="c-image-grid__img" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
							</li>
						</ul>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
