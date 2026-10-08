<?php
require get_template_directory() . '/inc/classes/class-wptt-webfont-loader.php';
/* ---------------------------------------------------------------------------------------------
   FIX PAGINATION FOR STATIC FRONT PAGE
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_fix_front_page_pagination' ) ) :
	/**
	 * Enable pagination on static front page with page template
	 * WordPress normally doesn't allow /page/2/ on static pages
	 */
	function newslunar_fix_front_page_pagination( $query ) {
		if ( ! is_admin() && $query->is_main_query() && is_page() ) {
			// Check if this page uses the Homepage with Sections template
			$page_template = get_post_meta( get_queried_object_id(), '_wp_page_template', true );
			if ( 'homepage-sections.php' === $page_template ) {
				// Allow pagination on this page
				$query->set( 'posts_per_page', get_option( 'posts_per_page' ) );
			}
		}
	}
	add_action( 'pre_get_posts', 'newslunar_fix_front_page_pagination' );
endif;
/* ---------------------------------------------------------------------------------------------
   WEBFONTS (LOCAL GOOGLE FONTS VIA WPTT WEBFONT LOADER)
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_get_webfonts_url' ) ) :
	/**
	 * Build and return the Google Fonts URL for Inter and Merriweather.
	 * The WPTT_WebFont_Loader will download the fonts locally on first load.
	 *
	 * @return string Local stylesheet URL (falls back to the Google Fonts URL).
	 */
	function newslunar_get_webfonts_url() {
		$fonts_url = add_query_arg(
			array(
				'family' => implode( '&family=', array(
					// Inter: variable, all weights, italic axis
					'PT+Serif:ital,wght@0,400;0,700;1,400;1,700',
					'Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900',
				) ),
				'display' => 'swap',
			),
			'https://fonts.googleapis.com/css2'
		);
		return ( new WPTT_WebFont_Loader( $fonts_url ) )->get_url();
	}
endif;
/* ---------------------------------------------------------------------------------------------
   THEME SETUP
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_setup' ) ) :
	function newslunar_setup() {
		
		// Automatic feed
		add_theme_support( 'automatic-feed-links' );
		
		// Title tag
		add_theme_support( 'title-tag' );
		
		// Set content-width
		global $content_width;
		if ( ! isset( $content_width ) ) $content_width = 616;
		
		// Post thumbnails
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size ( 88, 88, true );
		

		// Add nav menus
		register_nav_menu( 'primary', __( 'Primary Menu', 'newslunar' ) );
		register_nav_menu( 'secondary', __( 'Secondary Menu', 'newslunar' ) );
		register_nav_menu( 'social', __( 'Social Menu', 'newslunar' ) );
		// Custom logo
		add_theme_support( 'custom-logo', array(
			'height'      => 240,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'blog-title', 'blog-description' ),
		) );
        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));
         add_theme_support('responsive-embeds');
         add_theme_support('wp-block-styles');
		
		// Make the theme translation ready
		load_theme_textdomain( 'newslunar', get_template_directory() . '/languages' );
		
	}
	add_action( 'after_setup_theme', 'newslunar_setup' );
endif;
/* ---------------------------------------------------------------------------------------------
   ENQUEUE JAVASCRIPT
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_load_javascript_files' ) ) :
	function newslunar_load_javascript_files() {
		$theme_version = wp_get_theme( 'newslunar' )->get( 'Version' );
		wp_enqueue_script( 'newslunar_global', get_template_directory_uri() . '/assets/js/global.js', array( 'jquery' ), $theme_version, true );
		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
	}
	add_action( 'wp_enqueue_scripts', 'newslunar_load_javascript_files' );
endif;
/* ---------------------------------------------------------------------------------------------
   ENQUEUE STYLES
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_load_style' ) ) :
	function newslunar_load_style() {
		if ( is_admin() ) return;
		$theme_version = wp_get_theme( 'newslunar' )->get( 'Version' );
		$dependencies = array();
		$webfonts_url = newslunar_get_webfonts_url();
		if ( $webfonts_url ) {
			wp_register_style( 'newslunar_webfonts', $webfonts_url );
			$dependencies[] = 'newslunar_webfonts';
		}
        wp_register_style( 'newslunar_bootstrap_icons', get_template_directory_uri() . '/assets/css/bootstrap-icons.min.css', array(), $theme_version );
		$dependencies[] = 'newslunar_bootstrap_icons';
		wp_enqueue_style( 'newslunar_style', get_stylesheet_uri(), $dependencies, $theme_version );
	    wp_style_add_data('newslunar_style', 'rtl', 'replace');
		
		// Enqueue homepage sections styles
		if ( is_front_page() || is_page_template( 'homepage-sections.php' ) ) {
			wp_enqueue_style( 'newslunar_homepage_sections', get_template_directory_uri() . '/assets/css/main.css', array( 'newslunar_style' ), $theme_version );
            wp_style_add_data('newslunar_homepage_sections', 'rtl', 'replace');
		}

		// Enqueue tabbed posts styles and scripts
		wp_enqueue_style( 'newslunar-tabbed-posts', get_template_directory_uri() . '/assets/css/tabbed-posts.css', array( 'newslunar_style' ), $theme_version );
		wp_enqueue_script( 'newslunar-tabbed-posts', get_template_directory_uri() . '/assets/js/tabbed-posts.js', array( 'jquery' ), $theme_version, true );
		
		// Enqueue slider banner styles and scripts
		wp_enqueue_style( 'newslunar-slider-banner', get_template_directory_uri() . '/assets/css/slider-banner.css', array( 'newslunar_style' ), $theme_version );
        wp_style_add_data('newslunar-slider-banner', 'rtl', 'replace');
		wp_enqueue_script( 'newslunar-slider-banner', get_template_directory_uri() . '/assets/js/slider-banner.js', array( 'jquery' ), $theme_version, true );
		
		// Localize script for AJAX
		wp_localize_script( 'newslunar-tabbed-posts', 'newslunar_tabbed_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'newslunar_tabbed_nonce' ),
		) );
	}
	add_action( 'wp_print_styles', 'newslunar_load_style' );
endif;
/* ---------------------------------------------------------------------------------------------
   ADD EDITOR STYLES
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_add_editor_styles' ) ) :
	function newslunar_add_editor_styles() {
		add_editor_style( array( 'assets/css/newslunar-classic-editor-styles.css', newslunar_get_webfonts_url() ) );
	}
	add_action( 'init', 'newslunar_add_editor_styles' );
endif;
/* ---------------------------------------------------------------------------------------------
   ADD WIDGET AREAS
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_sidebar_registration' ) ) :
	function newslunar_sidebar_registration() {
		register_sidebar( array(
			'name' 			=> __( 'Sidebar', 'newslunar' ),
			'id' 			=> 'sidebar',
			'description' 	=> __( 'Widgets in this area will be shown in the sidebar.', 'newslunar' ),
			'before_title' 	=> '<h3 class="widget-title">',
			'after_title' 	=> '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div></div>'
		) );
		// Register Footer Widget areas dynamically based on customizer setting.
		$footer_widgets = get_theme_mod( 'newslunar_footer_widgets', 4 );
		for ( $i = 1; $i <= $footer_widgets; $i++ ) {
			register_sidebar( array(
				'name'          => sprintf( __( 'Footer %d', 'newslunar' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( 'Widgets in this area will be shown in the footer.', 'newslunar' ),
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
				'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>'
			) );
		}
		
		register_sidebar( array(
			'name'          => __( 'Homepage Before Post', 'newslunar' ),
			'id'            => 'homepage-before-post',
			'description'   => __( 'Widgets in this area will be shown before posts on the homepage.', 'newslunar' ),
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
			'after_widget'  => '</div></div>'
		) );

		register_sidebar( array(
			'name'          => __( 'Homepage After Post', 'newslunar' ),
			'id'            => 'homepage-after-post',
			'description'   => __( 'Widgets in this area will be shown after posts on the homepage.', 'newslunar' ),
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
			'after_widget'  => '</div></div>'
		) );
	}
	add_action( 'widgets_init', 'newslunar_sidebar_registration' ); 
endif;
/* ---------------------------------------------------------------------------------------------
   INCLUDE REQUIRED FILES
   --------------------------------------------------------------------------------------------- */
// Theme Customizer options.
require get_template_directory() . '/inc/classes/class-newslunar-customizer.php';
// Homepage Sections Manager
require get_template_directory() . '/inc/classes/class-newslunar-homepage-sections.php';
// Homepage Sections Helpers
require get_template_directory() . '/inc/homepage-sections-helpers.php';
// Trending Topics Category Image Management
require get_template_directory() . '/inc/trending-topics-admin.php';
// Recent Comments widget
require get_template_directory() . '/inc/widgets/recent-comments.php';
// Recent Posts widget
require get_template_directory() . '/inc/widgets/recent-posts.php';

// Tabbed Posts widget
require get_template_directory() . '/inc/widgets/tabbed-posts.php';

// Slider Banner widget
require get_template_directory() . '/inc/widgets/interactive-banner.php';

// Tabbed Posts AJAX handler
require get_template_directory() . '/inc/tabbed-posts-ajax.php';
/* ---------------------------------------------------------------------------------------------
   MODIFY WIDGETS
   --------------------------------------------------------------------------------------------- */
 
if ( ! function_exists( 'newslunar_unregister_default_widgets' ) ) :
	function newslunar_unregister_default_widgets() {
		// Register custom widgets
		register_widget( 'NewsLunar_Recent_Comments' );
		register_widget( 'NewsLunar_Recent_Posts' );
		register_widget( 'NewsLunar_Tabbed_Posts' );
		register_widget( 'NewsLunar_Interactive_Banner' );
		// Unregister replaced widgets
		unregister_widget( 'WP_Widget_Recent_Comments' );
		unregister_widget( 'WP_Widget_Recent_Posts' );
	}
	add_action( 'widgets_init', 'newslunar_unregister_default_widgets', 11 );
endif;
/* ---------------------------------------------------------------------------------------------
   CHECK FOR JAVASCRIPT
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_html_js_class' ) ) {
	function newslunar_html_js_class () {
		echo '<script>document.documentElement.className = document.documentElement.className.replace("no-js","js");</script>'. "\n";
	}
	add_action( 'wp_head', 'newslunar_html_js_class', 1 );
}
/* ---------------------------------------------------------------------------------------------
   RELATED POSTS FUNCTION
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_related_posts' ) ) :
	function newslunar_related_posts( $number_of_posts = 3 ) { 
		?>
		
		<div class="related-posts">
			
			<p class="related-posts-title"><?php _e( 'Read Next', 'newslunar' ); ?> &rarr;</p>
			
			<div class="row">
							
				<?php
				global $post;
				// Base args, used for both the term query and random query
				$base_args = array(
					'ignore_sticky_posts'	=>	true,
					'meta_key'				=>	'_thumbnail_id',
					'posts_per_page'		=>	$number_of_posts,
					'post_status'			=>	'publish',
					'post__not_in'			=>	array( $post->ID ),	
				);
				// Create a query for posts in the same category as the ones for the current post
				$cat_ids = array();
				$categories = get_the_category();
				foreach( $categories as $category ) {
					$cat_ids[] = $category->cat_ID;
				}
				$term_posts_args = array_merge( $base_args, array( 'category__in' => $cat_ids ) );
				
				$related_posts = get_posts( $term_posts_args );
				// No results for the categories? Get random posts instead
				if ( ! $related_posts ) :
					$random_posts_args = array_merge( $base_args, array( 'orderby' => 'rand' ) );
					$related_posts = get_posts( $random_posts_args );
				endif;
				// If either the category query or random query hit pay dirt, output the posts
				if ( $related_posts ) :
					
					foreach( $related_posts as $related_post ) : ?>
				
						<a class="related-post" href="<?php echo get_the_permalink( $related_post->ID ); ?>">
							
							<?php if ( has_post_thumbnail( $related_post->ID ) ) : ?>
								
								<?php echo get_the_post_thumbnail( $related_post->ID, 'medium' ) ?>
								
							<?php endif; ?>
							
							<p class="category">
								<?php 
								$category = get_the_category( $related_post->ID ); 
								echo $category[0]->cat_name;
								?>
							</p>
					
							<h3 class="title"><?php echo get_the_title( $related_post->ID ); ?></h3>
								
						</a>
					
						<?php 
					endforeach;
				
				endif;
				
				?>
			
			</div><!-- .row -->
		</div><!-- .related-posts -->
		
		<?php
	}
endif;
/* ---------------------------------------------------------------------------------------------
   ARCHIVE NAVIGATION
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_archive_navigation' ) ) :
	function newslunar_archive_navigation() {
		get_template_part( 'pagination' );
	}
endif;
/* ---------------------------------------------------------------------------------------------
   CUSTOM READ MORE TEXT
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_modify_read_more_link' ) ) :
	function newslunar_modify_read_more_link() {
		return '<p><a class="more-link" href="' . get_permalink() . '">' . __( 'Read More', 'newslunar' ) . '</a></p>';
	}
	add_filter( 'the_content_more_link', 'newslunar_modify_read_more_link' );
endif;
/* ---------------------------------------------------------------------------------------------
   BODY CLASSES
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_body_classes' ) ) :
	function newslunar_body_classes( $classes ) {
	
		// If has post thumbnail
		if ( is_single() && has_post_thumbnail() ){
			$classes[] = 'has-featured-image';
		}
		
		return $classes;
	}
	add_filter( 'body_class', 'newslunar_body_classes' );
endif;
/* ---------------------------------------------------------------------------------------------
   GET COMMENT EXCERPT LENGTH
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_get_comment_excerpt' ) ) :
	function newslunar_get_comment_excerpt( $comment_ID = 0, $num_words = 20 ) {
		$comment = get_comment( $comment_ID );
		$comment_text = strip_tags( $comment->comment_content );
		$blah = explode( ' ', $comment_text );
		if ( count( $blah ) > $num_words ) {
			$k = $num_words;
			$use_dotdotdot = 1;
		} else {
			$k = count( $blah );
			$use_dotdotdot = 0;
		}
		$excerpt = '';
		for ( $i = 0; $i < $k; $i++ ) {
			$excerpt .= $blah[$i] . ' ';
		}
		$excerpt .= ( $use_dotdotdot ) ? '...' : '';
		return apply_filters( 'get_comment_excerpt', $excerpt );
	}
endif;
/* ---------------------------------------------------------------------------------------------
   COMMENT FUNCTION
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_comment' ) ) :
	function newslunar_comment( $comment, $args, $depth ) {
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		
			<?php __( 'Pingback:', 'newslunar' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'newslunar' ), '<span class="edit-link">', '</span>' ); ?>
			
		</li>
		<?php
				break;
			default :
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		
			<div id="comment-<?php comment_ID(); ?>" class="comment">
				
				<?php echo get_avatar( $comment, 160 ); ?>
				
				<?php if ( $comment->user_id === $post->post_author ) : ?>
						
					<a class="comment-author-icon" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
						<div class="bi bi-person-fill"></div>
						<span class="screen-reader-text"><?php _e( 'Comment by post author', 'newslunar' ); ?></span>
					</a>
				
				<?php endif; ?>
				
				<div class="comment-inner">
				
					<div class="comment-header">
												
						<h4><?php echo get_comment_author_link(); ?></h4>
					
					</div><!-- .comment-header -->
					
					<div class="comment-content post-content entry-content">
				
						<?php comment_text(); ?>
						
					</div><!-- .comment-content -->
					
					<div class="comment-meta group">
						
						<div class="fleft">
							<i class="bi bi-calendar4"></i><a class="comment-date-link" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php echo get_comment_date( get_option( 'date_format' ) ); ?></a>
							<?php edit_comment_link( __( 'Edit', 'newslunar' ), '<i class="bi bi-pencil"></i>', '' ); ?>
						</div>
						
						<?php if ( '0' == $comment->comment_approved ) : ?>
					
							<div class="comment-awaiting-moderation fright">
								<i class="bi bi-exclamation-circle-fill"></i><?php _e( 'Awaiting moderation', 'newslunar' ); ?>
							</div>
							
						<?php else :
							comment_reply_link( array( 
								'reply_text' 	=> __( 'Reply', 'newslunar' ),
								'depth'			=> $depth, 
								'max_depth' 	=> $args['max_depth'],
								'before'		=> '<div class="fright"><i class="bi bi-reply-fill"></i>',
								'after'			=> '</div>'
							) ); 
							
						endif; ?>
						
					</div><!-- .comment-meta -->
									
				</div><!-- .comment-inner -->
											
			</div><!-- .comment-## -->
					
		<?php
			break;
		endswitch;
	}
endif;
/* ---------------------------------------------------------------------------------------------
   SPECIFY BLOCK EDITOR SUPPORT
------------------------------------------------------------------------------------------------ */
if ( ! function_exists( 'newslunar_add_block_editor_features' ) ) :
	function newslunar_add_block_editor_features() {
		/* Block Editor Features ------------- */
		add_theme_support( 'align-wide' );
		/* Block Editor Palette -------------- */
		$accent_color = get_theme_mod( 'accent_color', '#ce242c' );
		add_theme_support( 'editor-color-palette', array(
			array(
				'name' 	=> _x( 'Accent', 'Name of the accent color in the Block Editor palette', 'newslunar' ),
				'slug' 	=> 'accent',
				'color' => $accent_color,
			),
			array(
				'name' 	=> _x( 'Black', 'Name of the black color in the Block Editor palette', 'newslunar' ),
				'slug' 	=> 'black',
				'color' => '#111',
			),
			array(
				'name' 	=> _x( 'Dark Gray', 'Name of the dark gray color in the Block Editor palette', 'newslunar' ),
				'slug' 	=> 'dark-gray',
				'color' => '#333',
			),
			array(
				'name' 	=> _x( 'Medium Gray', 'Name of the medium gray color in the Block Editor palette', 'newslunar' ),
				'slug' 	=> 'medium-gray',
				'color' => '#555',
			),
			array(
				'name' 	=> _x( 'Light Gray', 'Name of the light gray color in the Block Editor palette', 'newslunar' ),
				'slug' 	=> 'light-gray',
				'color' => '#777',
			),
			array(
				'name' 	=> _x( 'White', 'Name of the white color in the Block Editor palette', 'newslunar' ),
				'slug' 	=> 'white',
				'color' => '#fff',
			),
		) );
		/* Block Editor Font Sizes ----------- */
		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' 		=> _x( 'Small', 'Name of the small font size in Block Editor', 'newslunar' ),
				'shortName' => _x( 'S', 'Short name of the small font size in the Block Editor.', 'newslunar' ),
				'size' 		=> 15,
				'slug' 		=> 'small',
			),
			array(
				'name' 		=> _x( 'Normal', 'Name of the normal font size in Block Editor', 'newslunar' ),
				'shortName' => _x( 'N', 'Short name of the normal font size in the Block Editor.', 'newslunar' ),
				'size' 		=> 17,
				'slug' 		=> 'normal',
			),
			array(
				'name' 		=> _x( 'Large', 'Name of the large font size in Block Editor', 'newslunar' ),
				'shortName' => _x( 'L', 'Short name of the large font size in the Block Editor.', 'newslunar' ),
				'size' 		=> 24,
				'slug' 		=> 'large',
			),
			array(
				'name' 		=> _x( 'Larger', 'Name of the larger font size in Block Editor', 'newslunar' ),
				'shortName' => _x( 'XL', 'Short name of the larger font size in the Block Editor.', 'newslunar' ),
				'size' 		=> 28,
				'slug' 		=> 'larger',
			),
		) );
	}
	add_action( 'after_setup_theme', 'newslunar_add_block_editor_features' );
endif;
/* ---------------------------------------------------------------------------------------------
   BLOCK EDITOR EDITOR STYLES
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_block_editor_styles' ) ) :
	function newslunar_block_editor_styles() {
		$theme_version = wp_get_theme( 'newslunar' )->get( 'Version' );
		
		wp_register_style( 'newslunar-block-editor-styles-font', newslunar_get_webfonts_url() );
		wp_enqueue_style( 'newslunar-block-editor-styles', get_theme_file_uri( '/assets/css/newslunar-block-editor-styles.css' ), array( 'newslunar-block-editor-styles-font' ), $theme_version, 'all' );
	}
	add_action( 'enqueue_block_editor_assets', 'newslunar_block_editor_styles', 1 );
endif;


/* ---------------------------------------------------------------------------------------------
   GET FALLBACK IMAGE
   --------------------------------------------------------------------------------------------- */

if (!function_exists('newslunar_get_fallback_image_url')) :
    function newslunar_get_fallback_image_url()
    {

        $disable_fallback_image = get_theme_mod('newslunar_disable_fallback_image');

        if ($disable_fallback_image) {
            return '';
        }

        $fallback_image_id = get_theme_mod('newslunar_fallback_image');

        if ($fallback_image_id) {
            $fallback_image = wp_get_attachment_image_src($fallback_image_id, 'full');
        }

        $fallback_image_url = isset($fallback_image) ? esc_url($fallback_image[0]) : get_template_directory_uri() . '/assets/images/default-fallback-image.png';

        return $fallback_image_url;

    }
endif;


/* ---------------------------------------------------------------------------------------------
   OUTPUT FALLBACK IMAGE
   --------------------------------------------------------------------------------------------- */

if (!function_exists('newslunar_the_fallback_image')) :
    function newslunar_the_fallback_image()
    {

        $fallback_image_url = newslunar_get_fallback_image_url();

        if (!$fallback_image_url) {
            return;
        }

        echo '<img class="fallback-image" src="' . $fallback_image_url . '" alt="' . __('Fallback image', 'newslunar') . '" />';

    }
endif;

/* ---------------------------------------------------------------------------------------------
   RANDOM POST REDIRECT
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_random_post_redirect' ) ) :
	function newslunar_random_post_redirect() {
		if ( isset( $_GET['random'] ) && $_GET['random'] == '1' ) {
			$args = array(
				'post_type'      => 'post',
				'posts_per_page' => 1,
				'orderby'        => 'rand',
				'post_status'    => 'publish',
			);
			$random_post = get_posts( $args );
			if ( $random_post ) {
				wp_redirect( get_permalink( $random_post[0]->ID ) );
				exit;
			}
		}
	}
	add_action( 'template_redirect', 'newslunar_random_post_redirect' );
endif;

/* ---------------------------------------------------------------------------------------------
   CUSTOM EXCERPT FUNCTION
   --------------------------------------------------------------------------------------------- */
if ( ! function_exists( 'newslunar_custom_excerpt' ) ) :
	/**
	 * Get custom excerpt with fallback to content
	 * 
	 * @param int    $post_id     Post ID (optional, uses current post if not provided)
	 * @param int    $word_count  Maximum number of words (default: 20)
	 * @param string $more        Text to append at the end (default: '...')
	 * @return string The excerpt text
	 */
	function newslunar_custom_excerpt( $post_id = null, $word_count = 20, $more = '...' ) {
		// Get the post
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}
		
		$the_post = get_post( $post_id );
		
		if ( ! $the_post ) {
			return '';
		}
		
		// Check if post has a manual excerpt
		if ( has_excerpt( $post_id ) ) {
			$excerpt = get_the_excerpt( $post_id );
		} else {
			// Use post content as fallback
			$excerpt = $the_post->post_content;
			
			// Strip shortcodes and HTML tags
			$excerpt = strip_shortcodes( $excerpt );
			$excerpt = wp_strip_all_tags( $excerpt );
		}
		
		// Remove extra whitespace
		$excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
		
		// Limit to word count
		$excerpt_words = explode( ' ', $excerpt );
		
		if ( count( $excerpt_words ) > $word_count ) {
			$excerpt_words = array_slice( $excerpt_words, 0, $word_count );
			$excerpt = implode( ' ', $excerpt_words ) . $more;
		} else {
			$excerpt = implode( ' ', $excerpt_words );
		}
		
		return $excerpt;
	}
endif;

if ( ! function_exists( 'newslunar_the_custom_excerpt' ) ) :
	/**
	 * Echo custom excerpt
	 * 
	 * @param int    $post_id     Post ID (optional, uses current post if not provided)
	 * @param int    $word_count  Maximum number of words (default: 20)
	 * @param string $more        Text to append at the end (default: '...')
	 */
	function newslunar_the_custom_excerpt( $post_id = null, $word_count = 20, $more = '...' ) {
		echo esc_html( newslunar_custom_excerpt( $post_id, $word_count, $more ) );
	}
endif;
