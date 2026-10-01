<?php
	/**
	 * The template for displaying archive pages.
	 *
	 * @link    https://codex.wordpress.org/Template_Hierarchy
	 *
	 * @package Shapely
	 */
	get_header(); ?>
<?php $shapely_layout_class = shapely_get_layout_class(); ?>
	<div class = "row">
		<?php
		if ( 'sidebar-left' === $shapely_layout_class ) :
			get_sidebar();
			endif;
		?>
		<div id = "primary" class = "col-md-8 mb-xs-24 <?php echo esc_attr( $shapely_layout_class ); ?>">
			<?php
			if ( have_posts() ) :

				// The visible title in the header callout is an h3; this is the page's h1.
				?>
					<header>
						<h1 class="page-title screen-reader-text"><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
					</header>
					<?php

					$shapely_layout_type = get_theme_mod( 'blog_layout_view', 'grid' );
					$shapely_layout_type = str_replace( '_', '-', $shapely_layout_type );

					get_template_part( 'template-parts/layouts/blog', $shapely_layout_type );

					shapely_pagination();
				else :
					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>
		</div><!-- #primary -->
		<?php
		if ( 'sidebar-right' === $shapely_layout_class ) :
			get_sidebar();
			endif;
		?>
	</div>
<?php
	get_footer();
