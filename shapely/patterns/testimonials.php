<?php
/**
 * Title: Testimonials
 * Slug: shapely/testimonials
 * Categories: shapely, testimonials
 * Keywords: testimonials, quotes, reviews
 * Viewport Width: 1400
 * Description: Three short quotes from customers on a light full-width band.
 *
 * @package Shapely
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"light","style":{"spacing":{"padding":{"top":"96px","bottom":"96px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-light-background-color has-background" style="padding-top:96px;padding-bottom:96px"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'What our clients say', 'shapely' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"48px"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p><?php esc_html_e( 'A sentence about working with you, in the client\'s own words.', 'shapely' ); ?></p>
<!-- /wp:paragraph --><cite><?php esc_html_e( 'Client name, Company', 'shapely' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p><?php esc_html_e( 'A second quote, ideally about a different part of what you do.', 'shapely' ); ?></p>
<!-- /wp:paragraph --><cite><?php esc_html_e( 'Client name, Company', 'shapely' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p><?php esc_html_e( 'A third, kept about as long as the other two.', 'shapely' ); ?></p>
<!-- /wp:paragraph --><cite><?php esc_html_e( 'Client name, Company', 'shapely' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
