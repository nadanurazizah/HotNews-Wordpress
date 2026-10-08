<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">
		
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		 
		<?php wp_head(); ?>
	
	</head>
	
	<body <?php body_class(); ?>>

		<?php 
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open(); 
		}
		?>

		<?php
		// Preloader
		if ( get_theme_mod( 'newslunar_enable_preloader', true ) ) :
			$preloader_style = get_theme_mod( 'newslunar_preloader_style', 'spinner' );
		?>
		<div id="newslunar-preloader" class="newslunar-preloader">
			<div class="preloader-content">
				<div class="preloader-<?php echo esc_attr( $preloader_style ); ?>">
					<?php if ( $preloader_style === 'spinner' ) : ?>
						<div class="spinner"></div>
					<?php elseif ( $preloader_style === 'dots' ) : ?>
						<div class="dot"></div>
						<div class="dot"></div>
						<div class="dot"></div>
					<?php elseif ( $preloader_style === 'pulse' ) : ?>
						<div class="pulse"></div>
					<?php elseif ( $preloader_style === 'bars' ) : ?>
						<div class="bar"></div>
						<div class="bar"></div>
						<div class="bar"></div>
						<div class="bar"></div>
					<?php elseif ( $preloader_style === 'circle' ) : ?>
						<svg class="circular" viewBox="25 25 50 50">
							<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"/>
						</svg>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<a class="skip-link button" href="#site-content"><?php _e( 'Skip to the content', 'newslunar' ); ?></a>
		
		<?php if ( get_theme_mod( 'newslunar_show_topbar', false ) ) : ?>
		
			<div class="united-topbar">
				
				<div class="section-inner group">

                    <div class="topbar-left">
                        <?php if ( get_theme_mod( 'newslunar_show_date', false ) ) : ?>
                            <div class="united-today">
                                <?php
                                $date_format = get_theme_mod( 'newslunar_date_format', '' );
                                if ( empty( $date_format ) ) {
                                    $date_format = 'l, F j, Y';
                                }
                                echo esc_html( wp_date( $date_format ) );
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( has_nav_menu( 'secondary' ) ) : ?>

                            <ul class="secondary-menu dropdown-menu reset-list-style">
                                <?php
                                wp_nav_menu( array(
                                    'container' 		=> '',
                                    'items_wrap' 		=> '%3$s',
                                    'theme_location' 	=> 'secondary'
                                ) );
                                ?>
                            </ul><!-- .secondary-menu -->

                        <?php endif; ?>
                    </div>


					<?php if ( get_theme_mod( 'newslunar_show_topbar_social', false ) && has_nav_menu( 'social' ) ) : ?>
				
						<ul class="social-menu reset-list-style">
							<?php 
							wp_nav_menu( array(
								'theme_location'	=>	'social',
								'container'			=>	'',
								'container_class'	=>	'menu-social',
								'items_wrap'		=>	'%3$s',
								'menu_id'			=>	'menu-social-items',
								'menu_class'		=>	'menu-items',
								'depth'				=>	1,
								'link_before'		=>	'<span class="screen-reader-text">',
								'link_after'		=>	'</span>',
								'fallback_cb'		=>	'',
							) );
							echo '<li id="menu-item-151" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-151"><a class="search-toggle" href="?s"><span class="screen-reader-text">Search</span></a></li>';
							?>
						</ul><!-- .social-menu -->

					<?php endif; ?>
				
				</div><!-- .section-inner -->
				
			</div><!-- .united-topbar -->
			
		<?php endif; ?>
		
		<div class="search-container">
			
			<div class="section-inner">
			
				<?php get_search_form(); ?>
			
			</div><!-- .section-inner -->
			
		</div><!-- .search-container -->
		
		<header class="site-header">
		
			<div class="header">
					
				<div class="section-inner">
				
					<?php 

					$custom_logo_id      = get_theme_mod( 'custom_logo' );
					$legacy_logo_url     = get_theme_mod( 'newslunar_logo' );
					$blog_title_elem     = ( ( is_front_page() || is_home() ) && ! is_page() ) ? 'h1' : 'div';
					$blog_title_class    = $custom_logo_id ? 'blog-logo' : 'blog-title';
					$display_header_text = get_theme_mod( 'display_header_text', true );

					$blog_title          = get_bloginfo( 'title' );
					$blog_description    = get_bloginfo( 'description' );

					if ( $custom_logo_id || $legacy_logo_url ) : 

						$custom_logo_url = $legacy_logo_url ? $legacy_logo_url : wp_get_attachment_image_url( $custom_logo_id, 'full' );
					
						?>

						<<?php echo $blog_title_elem; ?> class="<?php echo esc_attr( $blog_title_class ); ?>">
							<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<img src="<?php echo esc_url( $custom_logo_url ); ?>">
								<span class="screen-reader-text"><?php echo esc_html( $blog_title ); ?></span>
							</a>
						</<?php echo $blog_title_elem; ?>>

						<?php if ( $blog_title || $blog_description ) : ?>
							<div class="blog-title">
								<a href="<?php echo esc_url( home_url() ); ?>" rel="home"><?php echo esc_html( $blog_title ); ?></a>
							</div>
							<?php if ( $blog_description ) : ?>
								<div class="blog-description"><?php echo wpautop( esc_html( $blog_description ) ); ?></div>
							<?php endif; ?>
						<?php endif; ?>
			
					<?php elseif ( $blog_title || $blog_description ) : ?>

						<<?php echo $blog_title_elem; ?> class="<?php echo esc_attr( $blog_title_class ); ?>">
							<a href="<?php echo esc_url( home_url() ); ?>" rel="home"><?php echo esc_html( $blog_title ); ?></a>
						</<?php echo $blog_title_elem; ?>>
					
						<?php if ( $blog_description ) : ?>
							<div class="blog-description"><?php echo wpautop( esc_html( $blog_description ) ); ?></div>
						<?php endif; ?>
					
					<?php endif; ?>
					
					<button class="nav-toggle" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e( 'Open mobile navigation', 'newslunar' ); ?>">
						
						<span class="screen-reader-text"><?php _e( 'Menu', 'newslunar' ); ?></span>
						
						<div class="bars" aria-hidden="true">
							<div class="bar"></div>
							<div class="bar"></div>
							<div class="bar"></div>
						</div>
						
					</button><!-- .nav-toggle -->
				
				</div><!-- .section-inner -->
				
			</div><!-- .header -->
			
			<div class="navigation">
				
				<div class="section-inner">
					
					<ul class="primary-menu reset-list-style dropdown-menu">
						
						<?php if ( has_nav_menu( 'primary' ) ) {

							$nav_args = array( 
								'container' => '', 
								'items_wrap' => '%3$s',
								'theme_location' => 'primary'
							);
																		
							wp_nav_menu( $nav_args ); 
						
						} else {

							$list_pages_args = array(
								'container' => '',
								'title_li' 	=> ''
							);

							wp_list_pages( $list_pages_args );
							
						} ?>
															
					</ul>
					
					<div class="navbar-extras">
						<?php get_template_part( 'template-parts/header/navbar', 'extras' ); ?>
					</div>
					
				</div><!-- .section-inner -->
				
			</div><!-- .navigation -->
			
			<!-- Off-Canvas Mobile Menu -->
			<div id="mobile-menu-overlay" class="mobile-menu-overlay" aria-hidden="true"></div>
			<nav id="mobile-menu" class="mobile-menu-offcanvas" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'newslunar' ); ?>" aria-hidden="true">
				
				<div class="mobile-menu-header">
					<span class="mobile-menu-title"><?php esc_html_e( 'Menu', 'newslunar' ); ?></span>
					<button class="mobile-menu-close" aria-label="<?php esc_attr_e( 'Close mobile navigation', 'newslunar' ); ?>">
						<i class="bi bi-x-lg" aria-hidden="true"></i>
					</button>
				</div>

				<div class="mobile-menu-search">
					<?php get_search_form(); ?>
				</div>
				
				<div class="mobile-menu-content">
					<ul class="mobile-menu-list reset-list-style">
						<?php 
						if ( has_nav_menu( 'primary' ) ) {
							wp_nav_menu( array(
								'container'      => '',
								'items_wrap'     => '%3$s',
								'theme_location' => 'primary'
							) ); 
						} else {
							wp_list_pages( array(
								'container' => '',
								'title_li'  => ''
							) );
						}
						?>
					</ul>
					
					<div class="mobile-menu-extras">
						<div class="mobile-extras-label"><?php esc_html_e( 'Quick Actions', 'newslunar' ); ?></div>
                        <div class="navbar-extras">
						    <?php get_template_part( 'template-parts/header/navbar', 'extras' ); ?>
                        </div>
					</div>
				</div>
				
			</nav><!-- .mobile-menu-offcanvas -->
				
		</header><!-- .site-header -->

		<main id="site-content">