<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'b3m[93m[_rPyo@rE?)K97AEUWqukEvvSgr#BAQDm]D`gcfq~ 4;/)~(cm1BT(H)X' );
define( 'SECURE_AUTH_KEY',   'q[xSbx5M}=dY~#]jpZ%]k$Igsm.9ayrS(6*Dg>{}aIZ#Bs?*_KOG)OW--BMi+Y^A' );
define( 'LOGGED_IN_KEY',     'F%BG=VO*>NS$FtlCz*}Mf6yZ7JKSmOHe)/8ZiU.OX>_.Ez5^5aO*)h*-z?{<;W?X' );
define( 'NONCE_KEY',         'G.SZl!Ff$yY+e=~Kk*kN_E]!#>b%t}UrPwyc[Mc/w.!D+)`? T{t`I&![.{*PE:B' );
define( 'AUTH_SALT',         '6tup*]!w*:X; xMemlmD$i;, RpX`J}6 Q#e4vyDv!)>!_ZQOjgT#uf-09|Gn/0o' );
define( 'SECURE_AUTH_SALT',  '+)?o4Bc&0npu0G6O@Dkd2kWt_hwQf.ef*ifIr;+A&JU1<dYmsCVGnqbo|EE@DO!d' );
define( 'LOGGED_IN_SALT',    '>,HHcn;/c)xS^;Ywv0T?pGQRXBW}ZQhuKw-cMw[cq=:G,8w[v/S+b34D7wG(dfD0' );
define( 'NONCE_SALT',        'vD:JQ(wL {8LgB7mC%~`fgR3&A<aSWElk>9@S6bCmA0DLLl(T!k1?|tjdWsIrbP`' );
define( 'WP_CACHE_KEY_SALT', 'V(|1&eF.%DwqmQ[mj/vf{08A<*7NF1wskT=r~^|w#C7*9BquEJRJp0c-8wyOooR!' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
