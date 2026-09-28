<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db_wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '&`}}djP[HRZ}iun#>w~JSc(0:?!f_!:,sjW>p:>^uk_dMy;gi&Z@4dpNx{r_siaR' );
define( 'SECURE_AUTH_KEY',  'ce{>Q_AIe1FNyWq<lKgD8P%HM(v0Da~RA7l;~aUyL7j`1bdI+9,Jxd4h8]<U`W2 ' );
define( 'LOGGED_IN_KEY',    '|Ohr^#LRkr)zhrJI(MI[R8a95v|0i[jIqEtI.qS#@d+7eZp?p]-_X-w<E-g9X&9$' );
define( 'NONCE_KEY',        'P4Q 7p>HM_B|{dp,s<4l U&~sCW0:=4VKx3zohR.[3q,1$JPq$t*`(Rj*WKl#;,U' );
define( 'AUTH_SALT',        'IJR{?9)HL0&I/ y_=HztN{(QCd%8FXQq^p7gkI_FrxcRTMj&`6Gp[iGaBR0W]}[4' );
define( 'SECURE_AUTH_SALT', 'p1}C!grQ~TsAQ<=9YjLQk0XaF)LSoIcyx]G9*wH#Ty8|$e:BPIrTED4Pb#XTP ZP' );
define( 'LOGGED_IN_SALT',   'C3$wzMwkXj>)LM3^s1|N93A7wW&r3G;e?WFP:MWNTDDZIPG23J!Kvty^_sOJk!Zg' );
define( 'NONCE_SALT',       'a;X:^3@<74fd0#nU!Ywi(+%?eyo~#<.@ZBaEkJszI3&#zktk-q_!tT:q!GWA]Kfr' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
