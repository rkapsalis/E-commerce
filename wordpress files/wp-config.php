<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'e-commerce' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '-&U3=Q7tyl|}9y/QRF{8j<UP(6]]BY~TjB5P~S=,$Z6B6I:+D?{ZGJD=IT%.k~&X' );
define( 'SECURE_AUTH_KEY',  'czyjMR]{sjo^QwO7AX[%oP#$S|Zc6s)DL^Woq^-tg9uFT%k`&SPinL[zL.C/t^cI' );
define( 'LOGGED_IN_KEY',    'X:Ta%o/:$[u{JzmSvOd.%5Rc[NPS0O0<b*HQB<<;uQql<Qa8nRXc81HDKs(ki)s0' );
define( 'NONCE_KEY',        '``OaWhjvGj`@<CMwI/*(Hlk(e>TYJ5gRL@.8E K?o)oBFmJU-[9S8#fd0VY$*d@<' );
define( 'AUTH_SALT',        'UtUp/PSW}@;E&+ZNO]x~N^s#|8u`@ZubBE&0}i7fs#Y{fdUSibXQN[0]$a3uHqJ*' );
define( 'SECURE_AUTH_SALT', 'APlwhkM>k4K&ii_hpD.DwP-4Ds/A7e5B0zZxSi~41Ba8I`!?MF/~:8v)$95$w)#|' );
define( 'LOGGED_IN_SALT',   '!+;l zOhDW}0GDV=)]}ea4q0vy4J:5vLmfjbxBb5*#~^`6.?EEeB{f;rEY2:XZI=' );
define( 'NONCE_SALT',       'EHA[0+X@eY2G;hTo}1M5.1lp;Cze1&1~0k2&aR+<@{NrQ^E3O(PyOnESxolN(Q6v' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
