<?php
/**
 * GoDaddy Reseller Beaver Builder Module HTML.
 *
 * Reseller store product helpers trait.
 *
 * @author   GoDaddy
 * @since    NEXT
 */


/**
 * Module args.
 *
 * @since NEXT
 *
 * @var array
 */
$args = [
	'before_widget' => '',
	'before_title'  => '<h4 class="widget-title">',
	'after_title'   => '</h4>',
	'after_widget'  => '</div>',
	'widget_id'     => $id,
];

/**
 * Module settings.
 *
 * @since NEXT
 *
 * @var array
 */
$atts = get_object_vars($settings);

if ($settings->type === 'rstore-domain-transfer') {
	$args['before_widget'] = '<div class="widget rstore-domain-transfer">';
	$domain = new \Reseller_Store\Widgets\Domain_Transfer();
	$domain->widget($args, $atts);
}

if ($settings->type === 'rstore-domain-search') {
	$args['before_widget'] = '<div class="widget rstore-domain-search">';
	$domain = new \Reseller_Store\Widgets\Domain_Search();
	$domain->widget($args, $atts);
}

if ($settings->type === 'rstore-product') {
	$args['before_widget'] = '<div class="widget rstore-product">';
	$product = new \Reseller_Store\Widgets\Product();
	$product->widget($args, $atts);
}


