<?php
/**
 * Template part for displaying posts.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Shapely
 */

$shapely_dropcaps      = get_theme_mod( 'project_first_letter_caps', true );
$shapely_enable_tags   = get_theme_mod( 'project_tags_post_meta', true );
$shapely_post_author   = get_theme_mod( 'project_author_area', true );
$shapely_left_side     = get_theme_mod( 'project_author_left_side', false );
$shapely_post_title    = get_theme_mod( 'title_above_project', true );
$shapely_post_category = get_theme_mod( 'project_category', true );

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-content post-grid-wide' ); ?>>
	<header class="entry-header nolist">
		<?php
		$shapely_category = get_the_terms( get_the_ID(), 'jetpack-portfolio-type' );
		if ( has_post_thumbnail() ) {
			$shapely_layout = shapely_get_layout_class();
			$shapely_size   = 'shapely-featured';

			if ( 'full-width' === $shapely_layout ) {
				$shapely_size = 'shapely-full';
			}
			$shapely_image = get_the_post_thumbnail( get_the_ID(), $shapely_size );

			?>
		<a href="<?php echo esc_url( get_the_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
			<?php echo wp_kses( $shapely_image, shapely_image_allowed_html() ); ?>
		</a>

			<?php
			// get_the_terms() returns false or a WP_Error when there is nothing to
			// show; project types are not categories, so get_category_link() gave
			// an empty href.
			if ( is_array( $shapely_category ) && isset( $shapely_category[0] ) && $shapely_post_category ) :
				$shapely_category_link = get_term_link( $shapely_category[0] );
				?>
			<span class="shapely-category">
				<a href="<?php echo esc_url( is_wp_error( $shapely_category_link ) ? '' : $shapely_category_link ); ?>">
					<?php echo esc_html( $shapely_category[0]->name ); ?>
				</a>
			</span>
			<?php endif; ?>
			<?php
		}
		?>
	</header><!-- .entry-header -->
	<div class="entry-content">
		<?php if ( $shapely_post_title ) : ?>
			<h2 class="post-title">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( wp_trim_words( get_the_title(), 9 ) ); ?></a>
			</h2>
		<?php endif ?>

		<div class="entry-meta">
			<?php
			shapely_posted_on_no_cat();
			?>
			<!-- post-meta -->
		</div>

		<?php if ( $shapely_post_author && $shapely_left_side ) : ?>
			<div class="row">
				<div class="col-md-3 col-xs-12 author-bio-left-side">
					<?php
					shapely_author_bio();
					?>
				</div>
				<div class="col-md-9 col-xs-12 shapely-content <?php echo $shapely_dropcaps ? 'dropcaps-content' : ''; ?>">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shapely' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>
			</div>
		<?php else : ?>
			<div class="shapely-content <?php echo $shapely_dropcaps ? 'dropcaps-content' : ''; ?>">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shapely' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div><!-- .entry-content -->

	<?php
	$shapely_prev = get_previous_post_link();
	$shapely_prev = str_replace( '&laquo;', '<div class="wrapper"><span class="fa-solid fa-angle-left"></span>', $shapely_prev );
	$shapely_prev = str_replace( '</a>', '</a></div>', $shapely_prev );
	$shapely_next = get_next_post_link();
	$shapely_next = str_replace( '&raquo;', '<span class="fa-solid fa-angle-right"></span></div>', $shapely_next );
	$shapely_next = str_replace( '<a', '<div class="wrapper"><a', $shapely_next );
	?>
	<div class="shapely-next-prev row">
		<div class="col-md-6 text-left">
			<?php echo wp_kses_post( $shapely_prev ); ?>
		</div>
		<div class="col-md-6 text-right">
			<?php echo wp_kses_post( $shapely_next ); ?>
		</div>
	</div>

	<?php
	if ( $shapely_post_author && ! $shapely_left_side ) :
		shapely_author_bio();
	endif;

	if ( $shapely_enable_tags ) :
		$shapely_tags_list = get_the_term_list( get_the_ID(), 'jetpack-portfolio-tag', '', ' ' );
		echo ! empty( $shapely_tags_list ) ? '<div class="shapely-tags"><span class="fa-solid fa-tags"></span>' . wp_kses_post( $shapely_tags_list ) . '</div>' : '';
	endif;
	?>

	<?php do_action( 'shapely_single_after_article' ); ?>
</article>
