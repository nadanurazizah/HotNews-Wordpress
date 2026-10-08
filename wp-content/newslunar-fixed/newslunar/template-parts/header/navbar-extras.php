<?php
/**
 * Navbar Extras Template Part
 * Contains: Color Mode Switcher, Random Post Button, CTA Button
 * 
 * @package NewsLunar
 */
?>

<?php
// Color Mode Switcher
if ( get_theme_mod( 'newslunar_enable_color_mode', true ) ) :
    $default_mode = get_theme_mod( 'newslunar_default_color_mode', 'light' );
?>
    <div class="color-mode-switcher" data-default-mode="<?php echo esc_attr( $default_mode ); ?>">
        <button type="button" class="mode-option" data-mode="light" aria-label="<?php esc_attr_e( 'Light mode', 'newslunar' ); ?>">
            <i class="bi bi-sun-fill" aria-hidden="true"></i>
        </button>
        <button type="button" class="mode-option" data-mode="system" aria-label="<?php esc_attr_e( 'System mode', 'newslunar' ); ?>">
            <i class="bi bi-circle-half" aria-hidden="true"></i>
        </button>
        <button type="button" class="mode-option" data-mode="dark" aria-label="<?php esc_attr_e( 'Dark mode', 'newslunar' ); ?>">
            <i class="bi bi-moon-fill" aria-hidden="true"></i>
        </button>
    </div>
<?php endif; ?>

<?php
// Random Post Button
if ( get_theme_mod( 'newslunar_enable_random_post', true ) ) :
    $random_post_url = add_query_arg( 'random', '1', home_url( '/' ) );
?>
    <a href="<?php echo esc_url( $random_post_url ); ?>" class="random-post-button" aria-label="<?php esc_attr_e( 'Random post', 'newslunar' ); ?>" title="<?php esc_attr_e( 'Random post', 'newslunar' ); ?>">
        <i class="bi bi-shuffle" aria-hidden="true"></i>
    </a>
<?php endif; ?>

<?php
// CTA Button
if ( get_theme_mod( 'newslunar_enable_cta_button', true ) ) :
    $cta_label = get_theme_mod( 'newslunar_cta_button_label', __( 'Sign In', 'newslunar' ) );
    $cta_url   = get_theme_mod( 'newslunar_cta_button_url', '#' );

    // Kalau admin belum set URL CTA secara manual (masih default '#' atau kosong),
    // jangan jatuh ke wp-login.php bawaan WordPress. Cari dulu halaman yang
    // pakai template "Login Page (Custom)" dan pakai URL-nya, biar tombol
    // SIGN IN selalu stay di dalam desain tema.
    if ( empty( $cta_url ) || $cta_url === '#' ) {
        if ( is_user_logged_in() ) {
            $cta_url   = admin_url();
            $cta_label = __( 'Dashboard', 'newslunar' );
        } else {
            $custom_login_pages = get_posts( array(
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'meta_key'       => '_wp_page_template',
                'meta_value'     => 'template-login.php',
            ) );

            if ( ! empty( $custom_login_pages ) ) {
                $cta_url = get_permalink( $custom_login_pages[0] );
            } else {
                // Fallback terakhir: halaman login bawaan WordPress.
                $cta_url = wp_login_url();
            }
        }
    }
?>
    <a href="<?php echo esc_url( $cta_url ); ?>" class="navbar-cta-button">
        <?php echo esc_html( $cta_label ); ?>
    </a>
<?php endif; ?>
