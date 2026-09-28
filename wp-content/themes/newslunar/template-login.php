<?php
/**
 * Template Name: Login Page (Custom)
 * Description: Halaman login yang menyatu dengan header, footer, dan desain tema NewsLunar.
 *
 * @package NewsLunar
 */

// Kalau user sudah login, langsung arahkan ke homepage.
if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

$login_error = '';
if ( isset( $_GET['login'] ) && $_GET['login'] === 'failed' ) {
	$login_error = __( 'Username atau password salah. Silakan coba lagi.', 'newslunar' );
}

get_header();
?>

<div class="wrapper section-inner group">

	<div class="content newslunar-login-page">

		<h1 class="page-title"><?php esc_html_e( 'Sign In', 'newslunar' ); ?></h1>

		<?php if ( $login_error ) : ?>
			<div class="newslunar-login-error"><?php echo esc_html( $login_error ); ?></div>
		<?php endif; ?>

		<div class="newslunar-login-box">
			<?php
			wp_login_form( array(
				'echo'           => true,
				'redirect'       => home_url( '/' ),
				'form_id'        => 'newslunar-loginform',
				'label_username' => __( 'Username atau Email', 'newslunar' ),
				'label_password' => __( 'Password', 'newslunar' ),
				'label_remember' => __( 'Ingat saya', 'newslunar' ),
				'label_log_in'   => __( 'Sign In', 'newslunar' ),
				'remember'       => true,
			) );
			?>

			<div class="newslunar-login-links">
				<a href="<?php echo esc_url( wp_lostpassword_url( home_url( '/' ) ) ); ?>">
					<?php esc_html_e( 'Lupa password?', 'newslunar' ); ?>
				</a>
				<?php if ( get_option( 'users_can_register' ) ) : ?>
					<a href="<?php echo esc_url( wp_registration_url() ); ?>">
						<?php esc_html_e( 'Belum punya akun? Daftar', 'newslunar' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

	</div><!-- .content -->

	<?php get_sidebar(); ?>

</div><!-- .wrapper -->

<?php get_footer(); ?>
