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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'citymody_talents' );

/** Database username */
define( 'DB_USER', 'citymody_talents' );

/** Database password */
define( 'DB_PASSWORD', '242e!^tABa' );

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
define( 'AUTH_KEY',         'F}doDL){80.,BuCBYFF|wc6R:$<&`^B5MsUe.po;Il+@)HrRl5(/j%.^p3{,[,#I' );
define( 'SECURE_AUTH_KEY',  'eyq02L ,[U/{wKq*mNxG-m=KF GnJU8 RJcW3BdpVTCaR)~L5dMpz[$^mh)b#2e<' );
define( 'LOGGED_IN_KEY',    '4^~p*/$79F0XLCG^/43c;pa(<%^Fu7n+-hGG?c6xy1{NJ$Ae6+D2@svRi y,LPBC' );
define( 'NONCE_KEY',        '4rbiyg~S[a`F)t=.rO@xSU7H^XUa?[~b|;7Qh kiKTnv-v*|AR=mN;RzRvbi&48Q' );
define( 'AUTH_SALT',        'EW7fr*.&*v`#mJ:l3sk&Q>v8Tt#eWQ9_r|MkH8X5{=Vn)n g;)CEg3)>lrjS:F h' );
define( 'SECURE_AUTH_SALT', 'cvfxE4~OpGq[l*0r32g4VWlV0A+)NzzA?[!,P_]lj yZ_e|q>fHbS*|S%A71k.JY' );
define( 'LOGGED_IN_SALT',   'g[l.:]4zXw.#?)%J%<oZ?{8!pd<VHpV0:C@IumoCZ:vo`l[$dS7!*IP?_)YWTH*>' );
define( 'NONCE_SALT',       'Py}s2@DWN^(AKEiX0G9^WwZxIL.-n09{@T/;$zW-8@dE,Cqv>uBBkPWqwKuy-as[' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
