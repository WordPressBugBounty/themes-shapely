<?php
/**
 * Title: Contact details
 * Slug: shapely/contact
 * Categories: shapely, contact
 * Keywords: contact, address, phone, email
 * Viewport Width: 1400
 * Description: A heading and introduction beside your phone number, email address and postal address.
 *
 * @package Shapely
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"96px","bottom":"96px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:96px;padding-bottom:96px"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"64px"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Contact us', 'shapely' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Tell visitors the best way to reach you and how soon they can expect a reply.', 'shapely' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Phone', 'shapely' ); ?></strong><br><?php esc_html_e( '(000) 000-0000', 'shapely' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Email', 'shapely' ); ?></strong><br><?php esc_html_e( 'hello@example.com', 'shapely' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong><?php esc_html_e( 'Address', 'shapely' ); ?></strong><br><?php esc_html_e( '123 Street Name, City', 'shapely' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
