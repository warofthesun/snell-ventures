<?php
/**
 * Headline group template: header, subheader, body copy, CTAs
 *
 * @param array $block The block settings and attributes.
 */

if ( function_exists( 'client_block_inserter_preview' ) && client_block_inserter_preview( $block, 'headline-group' ) ) {
	return;
}

$preheader       = get_field( 'preheader' );
$headline        = get_field( 'headline' );
$headline_size   = get_field( 'headline_size' );
$on_dark         = (bool) get_field( 'on_dark_background' );
$headline_parsed = function_exists( 'client_headline_tag_and_class' )
	? client_headline_tag_and_class( $headline_size, 'c-headline-group__header h1 medium' )
	: array(
		'tag'   => 'h1',
		'class' => 'c-headline-group__header h1 medium',
	);
$body = get_field( 'body_copy' );

$is_editor  = is_admin();
$body_plain = is_string( $body ) ? trim( wp_strip_all_tags( $body ) ) : '';
$is_empty   = empty( $preheader ) && empty( $headline ) && empty( $body_plain ) && ! have_rows( 'buttons' );

if ( $is_editor && $is_empty ) :
	?>
	<div class="block-placeholder" style="border: 1px dashed #cfd3d7; padding: 1rem; border-radius: .5rem; background: rgba(255,255,255,.8);">
		<strong style="display:block; margin-bottom:.25rem;">Headline group</strong>
		<p style="margin:0;">Add a header, subheader, body copy, or buttons.</p>
	</div>
	<?php
	return;
endif;

$wrapper_classes = array( 'c-headline-group' );
if ( $on_dark ) {
	$wrapper_classes[] = 'c-headline-group--on-dark';
}

$header_class    = trim( $headline_parsed['class'] . ( $on_dark ? ' light' : '' ) );
$subheader_class = trim( 'c-headline-group__subheader h4 bold' . ( $on_dark ? ' light' : '' ) );
?>

<div class="row">
	<div class="col-xs-12 <?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
		<?php if ( $headline || $preheader ) : ?>
		<div class="c-headline-group__titles">
			<?php if ( $headline ) : ?>
				<<?php echo esc_attr( $headline_parsed['tag'] ); ?> class="<?php echo esc_attr( $header_class ); ?>"><?php echo esc_html( $headline ); ?></<?php echo esc_attr( $headline_parsed['tag'] ); ?>>
			<?php endif; ?>
			<?php if ( $preheader ) : ?>
				<p class="<?php echo esc_attr( $subheader_class ); ?>"><?php echo esc_html( $preheader ); ?></p>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php if ( $body ) : ?>
			<div class="c-headline-group__body"><?php echo wp_kses_post( $body ); ?></div>
		<?php endif; ?>
		<?php
		$partial_path = get_theme_file_path( '/partials/button_pair.php' );
		include $partial_path;
		?>
	</div>
</div>
