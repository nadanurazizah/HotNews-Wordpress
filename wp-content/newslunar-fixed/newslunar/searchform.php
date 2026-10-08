<?php 

$search_form_id = uniqid( 'search-form-' );
$search_field_id = uniqid( 'search-form-' );

?>

<form method="get" class="search-form" id="<?php echo esc_attr( $search_form_id ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" class="search-field" placeholder="<?php _e( 'What are you looking for?', 'newslunar' ); ?>" name="s" id="<?php echo esc_attr( $search_field_id ); ?>" />
	<button type="submit" class="search-button"><i class="bi bi-search"></i><span class="screen-reader-text"><?php _e( 'Search', 'newslunar' ); ?></span></button>
</form>