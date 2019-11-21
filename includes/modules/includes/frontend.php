<?php
/**
 * GoDaddy Reseller Beaver Builder Module HTML.
 *
 * Reseller store product helpers trait.
 *
 * @author   GoDaddy
 * @since    1.6.0
 */

/**
 * Module args.
 *
 * @since 1.6.0
 *
 * @var array
 */
$args = array(
	'before_widget' => '',
	'before_title'  => '<h4 class="widget-title">',
	'after_title'   => '</h4>',
	'after_widget'  => '</div>',
	'widget_id'     => $id,
);

/**
 * Module settings.
 *
 * @since 1.6.0
 *
 * @var array
 */
$atts = get_object_vars( $settings );

if ( 'rstore-fl-domain-transfer' === $settings->type ) {
	$args['before_widget'] = '<div class="widget rstore-domain-transfer">';
	$domain                = new \Reseller_Store\Widgets\Domain_Transfer();
	$domain->widget( $args, $atts );
}

if ( 'rstore-fl-domain-search' === $settings->type ) {
	$args['before_widget'] = '<div class="widget rstore-domain">';
	$domain                = new \Reseller_Store\Widgets\Domain_Search();
	$domain->widget( $args, $atts );
}

if ( 'rstore-fl-domain-simple' === $settings->type ) {
	$args['before_widget'] = '<div class="widget rstore-domain">';
	$domain                = new \Reseller_Store\Widgets\Domain_Simple();
	$domain->widget( $args, $atts );
}

if ( 'rstore-fl-product' === $settings->type ) {
	$args['before_widget'] = '<div class="widget rstore-product">';
	$product               = new \Reseller_Store\Widgets\Product();
	$product->widget( $args, $atts );
}

if ( 'rstore-fl-login' === $settings->type ) {
	$args['before_widget'] = '<div class="widget rstore-login">';
	$login                 = new \Reseller_Store\Widgets\login();
	$login->widget( $args, $atts );
}

if ( 'rstore-fl-cart' === $settings->type ) {
	$args['before_widget'] = '<div class="widget rstore-cart">';
	$login                 = new \Reseller_Store\Widgets\cart();
	$login->widget( $args, $atts );
}


