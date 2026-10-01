<?php
get_header();

$shapely_layout       = get_theme_mod( 'projects_layout_view', 'mansonry' );
$shapely_layout_class = shapely_get_layout_class();

$shapely_item_classes = 'post-snippet col-md-3 col-sm-6 project';
if ( 'mansonry' === $shapely_layout ) {
	$shapely_item_classes .= ' masonry-item';
}

?>
	<div class="row">
	<?php
	if ( 'sidebar-left' === $shapely_layout_class ) :
		get_sidebar();
	endif;
	?>
	<div id="primary" class="content-area col-md-8 mb-xs-24 <?php echo esc_attr( $shapely_layout_class ); ?>">
		<div class="site-main">
			<h1 class="screen-reader-text"><?php echo esc_html( get_theme_mod( 'portfolio_name' ) ? get_theme_mod( 'portfolio_name' ) : __( 'Portfolio', 'shapely' ) ); ?></h1>

			<?php
			if ( have_posts() ) :
				?>

				<?php if ( 'mansonry' === $shapely_layout ) : ?>
				<div class="masonry-loader fixed-center">
					<div class="col-sm-12 text-center">
						<div class="spinner"></div>
					</div>
				</div>
			<?php endif ?>

			<div class="<?php echo 'mansonry' === $shapely_layout ? 'masonry masonryFlyIn' : ''; ?>">
				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();
					$shapely_projects_args = array(
						'fields' => 'names',
					);
					$shapely_project_types = wp_get_post_terms( get_the_ID(), 'jetpack-portfolio-type', $shapely_projects_args );
					// wp_get_post_terms() returns WP_Error for an unregistered taxonomy,
					// which would fatal in the implode() below.
					if ( is_wp_error( $shapely_project_types ) ) {
						$shapely_project_types = array();
					}

					$shapely_thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
					$shapely_item_style    = '';
					if ( 'mansonry' !== $shapely_layout && $shapely_thumbnail_url ) {
						$shapely_item_style = 'background-image: url(' . esc_url( $shapely_thumbnail_url ) . ')';
					}
					?>

					<?php
					$shapely_portfolio_custom_url = get_post_meta( get_the_ID(), 'shapely_companion_portfolio_link', true );

					if ( ! $shapely_portfolio_custom_url ) {
						$shapely_portfolio_custom_url = get_the_permalink();
					}

					// A project without a featured image used to render as an
					// empty tile with no link to it at all.
					$shapely_tile_class = has_post_thumbnail() ? '' : ' no-thumbnail';
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( $shapely_item_classes ); ?>>
						<div class="image-tile inner-title hover-reveal text-center<?php echo esc_attr( $shapely_tile_class ); ?>" style="<?php echo esc_attr( $shapely_item_style ); ?>">
							<a href="<?php echo esc_url( $shapely_portfolio_custom_url ); ?>" title="<?php the_title_attribute(); ?>">
								<?php
								if ( 'mansonry' === $shapely_layout && has_post_thumbnail() ) {
									the_post_thumbnail( 'medium' );
								}
								?>
								<div class="title">
									<?php
									the_title( '<h5 class="mb0">', '</h5>' );
									if ( ! empty( $shapely_project_types ) ) {
										// Term names are author-editable; escape each before joining.
										echo '<span>' . esc_html( implode( ' / ', $shapely_project_types ) ) . '</span>';
									}
									?>
								</div>
							</a>
						</div>
					</article><!-- #post-## -->
					<?php

				endwhile;
				?>
			</div><!-- .masonry -->
				<?php

				the_posts_navigation();

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>

		</div><!-- .site-main -->
	</div><!-- #primary -->
	<?php
	if ( 'sidebar-right' === $shapely_layout_class ) :
		get_sidebar();
	endif;
	?>
	</div><!-- .row -->
<?php
get_footer();
