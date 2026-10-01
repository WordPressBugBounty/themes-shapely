<?php
/**
 * Template Name: Blocks (full width, no title)
 * Template Post Type: page
 *
 * A canvas for pages built from blocks and the theme's patterns: the header
 * and footer, and the page content at full width with no title band. Wide and
 * full-width blocks break out to the container and the screen edge; everything
 * else is held to the content width.
 *
 * @package Shapely
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'shapely-blocks' ); ?>>
		<?php // The page's own title is not shown here, so give it to screen readers. ?>
		<h1 class="screen-reader-text"><?php the_title(); ?></h1>
		<div class="entry-content">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shapely' ),
					'after'  => '</div>',
				)
			);
			?>
		</div><!-- .entry-content -->
	</article>
	<?php
endwhile;

get_footer();
