<?php
/**
 * Title: Call to action band
 * Slug: shapely/call-to-action-band
 * Categories: shapely, call-to-action
 * Keywords: call to action, cta, banner, button
 * Viewport Width: 1400
 * Description: A full-width band with a line of text and a button, like the theme's footer call to action.
 *
 * @package Shapely
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"light","style":{"spacing":{"padding":{"top":"64px","bottom":"64px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-light-background-color has-background" style="padding-top:64px;padding-bottom:64px"><!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"66.66%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Have a project in mind? Let\'s talk about it.', 'shapely' ); ?></h3>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"33.33%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33.33%"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-shapely-filled"} -->
<div class="wp-block-button is-style-shapely-filled"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Contact us', 'shapely' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
