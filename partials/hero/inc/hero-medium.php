<?php
/**
 * Hero Medium (and Hero Small on pages). Events archive uses same markup at full height.
 *
 * @var array $d Hero data from `client_get_hero_config()['data']`.
 */
$medium_bg_style = '';
if ( isset( $d['background_type'] ) && $d['background_type'] === 'color' && ! empty( $d['background_color'] ) ) {
	$medium_bg_style = 'background-color: ' . esc_attr( $d['background_color'] ) . ';';
} elseif ( ! empty( $d['background_image'] ) ) {
	$img = wp_get_attachment_image_src( (int) $d['background_image'], 'hero-bg' );
	if ( $img ) {
		$medium_bg_style = 'background-image: url(' . esc_url( $img[0] ) . ');';
	}
}

$is_small            = isset( $d['size'] ) && $d['size'] === 'small';
$is_events_archive   = is_post_type_archive( 'tribe_events' ) && ! is_singular( 'tribe_events' );
$use_medium_text_classes = ! $is_small || $is_events_archive;
$text_mode               = isset( $d['text_color'] ) ? (string) $d['text_color'] : '';
$text_dark               = $text_mode === 'dark';

$preheader_text = isset( $d['preheader_text'] ) ? trim( (string) $d['preheader_text'] ) : '';
$subheader_text = isset( $d['subheader_text'] ) ? trim( (string) $d['subheader_text'] ) : '';

$headline_text = isset( $d['headline_text'] ) ? $d['headline_text'] : '';
if ( is_array( $headline_text ) ) {
	$headline_text = isset( $headline_text['field_68225159eee20'] ) ? $headline_text['field_68225159eee20'] : ( isset( $headline_text['hero_headline'] ) ? $headline_text['hero_headline'] : '' );
}
$headline_text = is_string( $headline_text ) ? trim( $headline_text ) : '';
$headline_html = $headline_text !== '' ? wp_kses_post( $headline_text ) : '';
?>
<div
	class="<?php echo $is_small ? 'hero__container hero__container--small' : 'hero__container hero__container--medium'; ?>">
	<div class="hero__container--inner">
		<?php if ( $medium_bg_style ) : ?>
			<div class="hero__content hero__content--bg col-xs-12" style="<?php echo $medium_bg_style; ?>"></div>
		<?php endif; ?>

		<?php if ( ! $is_small ) : ?>
			<div class="hero__content hero__content--text hero__content--medium col-xs-12">
				<div class="hero__mediumPanel">
					<div class="hero__mediumPanelShape" aria-hidden="true">
						<svg class="hero__mediumPanelShapeSvg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 668 606" preserveAspectRatio="none" focusable="false">
							<path d="M0 0H668V606H334L0 0Z" fill="currentColor"/>
						</svg>
					</div>
					<div class="hero__mediumCopy">
						<?php if ( $preheader_text !== '' ) : ?>
							<p class="hero__mediumPreheader"><?php echo esc_html( $preheader_text ); ?></p>
						<?php endif; ?>

						<?php if ( $headline_html !== '' ) : ?>
							<h1 class="hero__mediumHeadline"><?php echo $headline_html; ?></h1>
						<?php endif; ?>

						<?php if ( $subheader_text !== '' ) : ?>
							<p class="hero__mediumSubheader"><?php echo esc_html( $subheader_text ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="hero__content hero__content--text hero__content--small col-xs-12">
				<div class="hero__headline <?php echo $use_medium_text_classes ? 'hero__headline--medium' : 'hero__headline--small'; ?>">
					<?php if ( $headline_html !== '' ) : ?>
						<h1 class="hero__title <?php echo $use_medium_text_classes ? 'hero__title--medium' : 'hero__title--small'; ?><?php echo $text_dark ? ' dark' : ''; ?>"><?php echo $headline_html; ?></h1>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
