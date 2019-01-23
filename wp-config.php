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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'fire');

/** MySQL database username */
define('DB_USER', 'wwfp_tech02');

/** MySQL database password */
define('DB_PASSWORD', 'T7wgUCOzUKlQyCfT');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'N4a_3|&G*HJmL G=$&IGjv5-h|f$ jcw{>bj*n~|AWP1&GAoEv6fhT!Uq6!d|n=3');
define('SECURE_AUTH_KEY',  '.|Ek%[0>DhSKJ c[PI11+rdx/-W!yENw=D-lQ!:t30GX?nK,;b>okq?A2Snn;Wu8');
define('LOGGED_IN_KEY',    'a !/QD(IIdby249oYMfPq0CZ^nsbO0$,uOA7<h-{ENPcRfCDgIOOV;wWN~T%0=Q+');
define('NONCE_KEY',        'P_gBQQTrk}seZGXNPiLq)8nEqY]s,P(<PeNM]CeQ>(%NZ>/(;B!c|AD<h8I4ZwX~');
define('AUTH_SALT',        'zja42vWsYZw:Ev,es{~RNEUaNRkIn;@F+7%qp)iY?WJa2O8<qQrVJSs/pSOnDA3l');
define('SECURE_AUTH_SALT', 'E((}]duRWoLx;C%d_1b*!,H&`=!X[JCu.q3{F}46RUzX9u+$sR4K+?T+1y7fPm-D');
define('LOGGED_IN_SALT',   'xQNP^^s-fWt6|v!`l_>Hs$#K4,[x#O>w2snHFxD.eI}g<a-`jy<MYU]t2a0~8i(g');
define('NONCE_SALT',       '-!/>rM,2,y))C}<,<yHzc0$/PdDND~Y7WrgQUqR)X~%Bq)Ta >5d@jIM/&zZ*BA#');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
