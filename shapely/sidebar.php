<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Shapely
 */ ?>

<?php
if ( ! is_active_sidebar( 'sidebar-1' ) || ! shapely_show_sidebar() ) {
	return;
}
?>

<aside id="secondary" class="widget-area col-md-4" aria-label="<?php esc_attr_e( 'Sidebar', 'shapely' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
