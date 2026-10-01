<?php
/**
 * Title: Three features
 * Slug: shapely/features
 * Categories: shapely, columns, featured
 * Keywords: features, services, columns
 * Viewport Width: 1400
 * Description: A centred heading over three columns, each with a short title and description.
 *
 * @package Shapely
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"96px","bottom":"96px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:96px;padding-bottom:96px"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'What we do', 'shapely' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:separator {"className":"is-style-shapely-short"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-shapely-short"/>
<!-- /wp:separator -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"48px"},"margin":{"top":"48px"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:48px"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Design', 'shapely' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php esc_html_e( 'Describe the first thing you offer in a sentence or two.', 'shapely' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Development', 'shapely' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php esc_html_e( 'Describe the second, keeping the three about the same length.', 'shapely' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Support', 'shapely' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php esc_html_e( 'And the third, so the columns balance on a wide screen.', 'shapely' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
