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
?>
<div
	class="<?php echo $is_small ? 'hero__container hero__container--small' : 'hero__container hero__container--medium'; ?>">
	<div class="hero__container--inner">
		<?php if ( $medium_bg_style ) : ?>
			<div class="hero__content hero__content--bg col-xs-12" style="<?php echo $medium_bg_style; ?>"></div>
		<?php endif; ?>

		<div class="hero__content hero__content--text<?php echo $is_small ? ' hero__content--small' : ' hero__content--medium'; ?> col-xs-12">
			<div class="hero__headline <?php echo $use_medium_text_classes ? 'hero__headline--medium' : 'hero__headline--small'; ?>">
				<?php if ( ! empty( $d['headline_text'] ) ) : ?>
					<h1 class="hero__title <?php echo $use_medium_text_classes ? 'hero__title--medium' : 'hero__title--small'; ?><?php echo $text_dark ? ' dark' : ''; ?>"><?php echo esc_html( $d['headline_text'] ); ?></h1>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
