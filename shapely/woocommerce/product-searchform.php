<?php
/**
 * Product Search Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/product-searchform.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * The theme's search form, restricted to products. This used to print the
 * plain site search, so the WooCommerce product search widget returned blog
 * posts and pages as well.
 */
$shapely_form = get_search_form( array( 'echo' => false ) );
echo str_replace( '</form>', '<input type="hidden" name="post_type" value="product" /></form>', $shapely_form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by shapely_search_form().
