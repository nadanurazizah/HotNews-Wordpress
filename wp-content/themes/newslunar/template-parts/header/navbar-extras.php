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
    $cta_url = get_theme_mod( 'newslunar_cta_button_url', '#' );
?>
    <a href="<?php echo esc_url( $cta_url ); ?>" class="navbar-cta-button">
        <?php echo esc_html( $cta_label ); ?>
    </a>
<?php endif; ?>
