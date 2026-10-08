<?php
/**
 * Template part for Full Width Widget Area Section
 *
 * @package NewsLunar
 */

$section_data = get_query_var( 'section_data' );
$section_id = ! empty( $section_data['id'] ) ? $section_data['id'] : '';
$layout_type = ! empty( $section_data['layout_type'] ) ? $section_data['layout_type'] : 'default';

if ( $layout_type === 'default' ) {
	// Default layout - single full-width widget area
	// Use section ID instead of index to prevent widget loss on section reorder/delete
	$widget_area_id = 'homepage-section-widget-' . $section_id;
	
	if ( is_active_sidebar( $widget_area_id ) ) :
	?>
	<section class="homepage-section full-width-widget-section">
		<div class="section-inner">
			<div class="united-widget-area widget-area-full-width">
				<?php dynamic_sidebar( $widget_area_id ); ?>
			</div>
		</div>
	</section>
	<?php
	endif;

} else {
	// Layout with main area and sidebar
	// Use section ID instead of index to prevent widget loss on section reorder/delete
	$main_widget_area_id = 'homepage-section-widget-' . $section_id . '-main';
	$sidebar_widget_area_id = 'homepage-section-widget-' . $section_id . '-sidebar';
	
	if ( is_active_sidebar( $main_widget_area_id ) || is_active_sidebar( $sidebar_widget_area_id ) ) :
	?>
	<section class="homepage-section full-width-widget-section layout-<?php echo esc_attr( $layout_type ); ?>">
		<div class="section-inner">
			<div class="united-widget-area widget-area-with-sidebar">
				<?php if ( $layout_type === 'sidebar_main' ) : ?>
					<!-- Sidebar first, then main area -->
					<?php if ( is_active_sidebar( $sidebar_widget_area_id ) ) : ?>
					<div class="widget-sidebar">
						<?php dynamic_sidebar( $sidebar_widget_area_id ); ?>
					</div>
					<?php endif; ?>
					
					<?php if ( is_active_sidebar( $main_widget_area_id ) ) : ?>
					<div class="widget-main-area">
						<?php dynamic_sidebar( $main_widget_area_id ); ?>
					</div>
					<?php endif; ?>
				<?php else : ?>
					<!-- Main area first, then sidebar -->
					<?php if ( is_active_sidebar( $main_widget_area_id ) ) : ?>
					<div class="widget-main-area">
						<?php dynamic_sidebar( $main_widget_area_id ); ?>
					</div>
					<?php endif; ?>
					
					<?php if ( is_active_sidebar( $sidebar_widget_area_id ) ) : ?>
					<div class="widget-sidebar">
						<?php dynamic_sidebar( $sidebar_widget_area_id ); ?>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	endif;
}
?>