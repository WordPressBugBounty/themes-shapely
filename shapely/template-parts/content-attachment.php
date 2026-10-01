<?php
/**
 * Template part for displaying attachment page
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Shapely
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if ( has_post_thumbnail() ) {
			?>
			<a class="text-center" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php
				$shapely_thumbnail_args = array(
					'class' => 'mb24',
				);
				the_post_thumbnail( 'shapely-featured', $shapely_thumbnail_args );
				?>
			</a>
		<?php } ?>
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		$shapely_image = wp_get_attachment_image( get_the_ID(), 'full' );

		// shapely_image_allowed_html() keeps srcset/sizes, which wp_kses_post()
		// strips -- every visitor was sent the full-size original.
		echo wp_kses( $shapely_image, shapely_image_allowed_html() );
		// Not the_content(): core prepends the attachment itself to it, which
		// would show the image twice.
		echo wp_kses_post( get_the_content() );

		$shapely_link_pages_args = array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shapely' ),
			'after'  => '</div>',
		);
		wp_link_pages( $shapely_link_pages_args );
		?>
	</div><!-- .entry-content -->
	<?php
	if ( is_single() ) :
		$shapely_prev = get_previous_post_link();
		$shapely_prev = str_replace( '&laquo;', '<div class="wrapper"><span class="fa-solid fa-angle-left"></span>', $shapely_prev );
		$shapely_prev = str_replace( '</a>', '</a></div>', $shapely_prev );
		$shapely_next = get_next_post_link();
		$shapely_next = str_replace( '&raquo;', '<span class="fa-solid fa-angle-right"></span></div>', $shapely_next );
		$shapely_next = str_replace( '<a', '<div class="wrapper"><a', $shapely_next );
		?>
		<hr/>
		<div class="shapely-next-prev row">
			<div class="col-md-6 text-left">
				<?php echo wp_kses_post( $shapely_prev ); ?>
			</div>
			<div class="col-md-6 text-right">
				<?php echo wp_kses_post( $shapely_next ); ?>
			</div>
		</div>
	<?php endif; ?>
</article><!-- #post-## -->
