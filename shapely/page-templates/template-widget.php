<?php
/**
 *
 * Template Name: Builder Page
 *
 */
?>
<?php

get_header();

while ( have_posts() ) {
	the_post();
	global $post;
	$shapely_sidebar_id = 'shapely-' . $post->post_name;

	if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( $shapely_sidebar_id ) ) {
		echo '<div class="container p24 wp-caption-text">';
			/* translators: %s: page title */
			echo '<h5>' . sprintf( esc_html__( 'This is the %s sidebar, add some widgets to it to change it.', 'shapely' ), esc_html( get_the_title() ) ) . '</h5>';
			echo '<p>' . esc_html__( 'Go to Appearance → Widgets and add widgets to this area.', 'shapely' ) . '</p>';
		echo '</div>';
	}
}

get_footer();
