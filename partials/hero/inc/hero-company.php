<?php
/**
 * Single Company hero — Figma Hero/Companies/Post Type/Single (435:1631).
 *
 * @var array $d Hero data from `client_get_hero_config()['data']`.
 */
$bg_style = '';
if ( ! empty( $d['image_id'] ) ) {
	$img = wp_get_attachment_image_src( (int) $d['image_id'], 'hero-bg' );
	if ( ! $img ) {
		$img = wp_get_attachment_image_src( (int) $d['image_id'], 'large' );
	}
	if ( $img ) {
		$bg_style = 'background-image: url(' . esc_url( $img[0] ) . ');';
	}
}
if ( $bg_style === '' && ! empty( $d['background_color'] ) ) {
	$bg_style = 'background-color: ' . esc_attr( $d['background_color'] ) . ';';
}

$title         = isset( $d['title'] ) ? (string) $d['title'] : '';
$industry      = isset( $d['industry'] ) ? (string) $d['industry'] : '';
$years_biz     = isset( $d['years_in_business'] ) ? $d['years_in_business'] : '';
$years_stew    = isset( $d['years_in_stewardship'] ) ? $d['years_in_stewardship'] : '';
$total_emp     = isset( $d['total_employees'] ) ? $d['total_employees'] : '';

$client_company_stat_has_value = static function ( $value ) {
	if ( $value === null || $value === '' || $value === false ) {
		return false;
	}
	if ( is_string( $value ) && trim( $value ) === '' ) {
		return false;
	}
	return is_numeric( $value );
};

$stats = array();
$stat_defs = array(
	array(
		'value' => $years_biz,
		'lines' => array(
			__( 'Total Years', 'client_theme' ),
			__( 'in Business', 'client_theme' ),
		),
	),
	array(
		'value' => $years_stew,
		'lines' => array(
			__( 'Years in Our', 'client_theme' ),
			__( 'Stewardship', 'client_theme' ),
		),
	),
	array(
		'value' => $total_emp,
		'lines' => array(
			__( 'Total', 'client_theme' ),
			__( 'Employees', 'client_theme' ),
		),
	),
);

foreach ( $stat_defs as $stat_def ) {
	if ( $client_company_stat_has_value( $stat_def['value'] ) ) {
		$stats[] = $stat_def;
	}
}

$has_stat = ! empty( $stats );
?>
<div class="hero__container hero__container--company">
	<div class="hero__container--company-inner<?php echo ! $has_stat ? ' hero__container--company-inner--no-stats' : ''; ?>">
		<div class="hero__content hero__content--company-bg"<?php echo $bg_style !== '' ? ' style="' . esc_attr( $bg_style ) . '"' : ''; ?>></div>

		<div class="hero__content hero__content--company-overlay" aria-hidden="true"></div>

		<div class="hero__content hero__content--company-text">
			<?php if ( $title !== '' ) : ?>
				<h1 class="hero__title hero__title--company"><?php echo esc_html( $title ); ?></h1>
			<?php endif; ?>
			<?php if ( $industry !== '' ) : ?>
				<p class="hero__industry hero__industry--company"><?php echo esc_html( $industry ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $has_stat ) : ?>
			<div class="hero__stats hero__stats--company">
				<?php foreach ( $stats as $stat ) : ?>
					<div class="hero__stat hero__stat--company">
						<p class="hero__stat-value"><?php echo esc_html( (string) (int) $stat['value'] ); ?></p>
						<?php if ( ! empty( $stat['lines'] ) && is_array( $stat['lines'] ) ) : ?>
							<div class="hero__stat-label">
								<?php foreach ( $stat['lines'] as $line ) : ?>
									<?php if ( (string) $line !== '' ) : ?>
										<p class="hero__stat-label-line"><?php echo esc_html( $line ); ?></p>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
