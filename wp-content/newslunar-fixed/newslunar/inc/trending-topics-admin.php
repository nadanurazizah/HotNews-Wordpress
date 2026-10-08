<?php
/**
 * Trending Topics Category Image Management
 * Adds image upload field to category edit pages
 *
 * @package NewsLunar
 */

// Add image field to category add form
add_action( 'category_add_form_fields', 'newslunar_add_category_image_field' );
function newslunar_add_category_image_field() {
	?>
	<div class="form-field term-image-wrap">
		<label for="category-image"><?php _e( 'Category Image', 'newslunar' ); ?></label>
		<input type="hidden" id="category-image" name="category_image" value="" />
		<button type="button" class="button category-image-upload"><?php _e( 'Upload Image', 'newslunar' ); ?></button>
		<div class="category-image-preview" style="margin-top: 10px;"></div>
		<p class="description"><?php _e( 'Upload an image for this category to display in Trending Topics section.', 'newslunar' ); ?></p>
	</div>
	<?php
}

// Add image field to category edit form
add_action( 'category_edit_form_fields', 'newslunar_edit_category_image_field' );
function newslunar_edit_category_image_field( $term ) {
	$image_url = get_term_meta( $term->term_id, 'newslunar_category_image', true );
	?>
	<tr class="form-field term-image-wrap">
		<th scope="row">
			<label for="category-image"><?php _e( 'Category Image', 'newslunar' ); ?></label>
		</th>
		<td>
			<input type="hidden" id="category-image" name="category_image" value="<?php echo esc_url( $image_url ); ?>" />
			<button type="button" class="button category-image-upload"><?php _e( 'Upload Image', 'newslunar' ); ?></button>
			<?php if ( $image_url ) : ?>
				<button type="button" class="button category-image-remove" style="margin-left: 5px;"><?php _e( 'Remove Image', 'newslunar' ); ?></button>
			<?php endif; ?>
			<div class="category-image-preview" style="margin-top: 10px;">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 200px; height: auto; display: block;" />
				<?php endif; ?>
			</div>
			<p class="description"><?php _e( 'Upload an image for this category to display in Trending Topics section.', 'newslunar' ); ?></p>
		</td>
	</tr>
	<?php
}

// Save category image
add_action( 'created_category', 'newslunar_save_category_image' );
add_action( 'edited_category', 'newslunar_save_category_image' );
function newslunar_save_category_image( $term_id ) {
	if ( isset( $_POST['category_image'] ) ) {
		update_term_meta( $term_id, 'newslunar_category_image', esc_url_raw( $_POST['category_image'] ) );
	}
}

// Enqueue admin scripts for category image upload
add_action( 'admin_enqueue_scripts', 'newslunar_category_image_admin_scripts' );
function newslunar_category_image_admin_scripts( $hook ) {
	// Only load on category edit pages
	if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook ) {
		return;
	}
	
	$screen = get_current_screen();
	if ( ! $screen || $screen->taxonomy !== 'category' ) {
		return;
	}
	
	wp_enqueue_media();
	
	wp_add_inline_script( 'jquery', "
		jQuery(document).ready(function($) {
			var mediaUploader;
			
			// Upload Image
			$(document).on('click', '.category-image-upload', function(e) {
				e.preventDefault();
				
				var button = $(this);
				var inputField = button.siblings('#category-image');
				var preview = button.siblings('.category-image-preview');
				
				// If the media uploader already exists, open it
				if (mediaUploader) {
					mediaUploader.open();
					return;
				}
				
				// Create the media uploader
				mediaUploader = wp.media({
					title: 'Select Category Image',
					button: {
						text: 'Use This Image'
					},
					multiple: false
				});
				
				// When an image is selected
				mediaUploader.on('select', function() {
					var attachment = mediaUploader.state().get('selection').first().toJSON();
					inputField.val(attachment.url);
					preview.html('<img src=\"' + attachment.url + '\" style=\"max-width: 200px; height: auto; display: block;\" />');
					
					// Add remove button if it doesn't exist
					if (!button.siblings('.category-image-remove').length) {
						button.after('<button type=\"button\" class=\"button category-image-remove\" style=\"margin-left: 5px;\">Remove Image</button>');
					}
				});
				
				mediaUploader.open();
			});
			
			// Remove Image
			$(document).on('click', '.category-image-remove', function(e) {
				e.preventDefault();
				$(this).siblings('#category-image').val('');
				$(this).siblings('.category-image-preview').html('');
				$(this).remove();
			});
		});
	");
}

// Add column to categories list table
add_filter( 'manage_edit-category_columns', 'newslunar_add_category_image_column' );
function newslunar_add_category_image_column( $columns ) {
	$columns['category_image'] = __( 'Image', 'newslunar' );
	return $columns;
}

// Display image in categories list table
add_filter( 'manage_category_custom_column', 'newslunar_display_category_image_column', 10, 3 );
function newslunar_display_category_image_column( $content, $column_name, $term_id ) {
	if ( 'category_image' === $column_name ) {
		$image_url = get_term_meta( $term_id, 'newslunar_category_image', true );
		if ( $image_url ) {
			$content = '<img src="' . esc_url( $image_url ) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" />';
		} else {
			$content = '—';
		}
	}
	return $content;
}
