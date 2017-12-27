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
define('DB_NAME', 'traveller');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         'F1ZfHanN]>KY1)g{ jJ*UGxq:C!vMr-g*|@}(C7638e9e~S&nNSgw!NnJ`3=R/cT');
define('SECURE_AUTH_KEY',  'cl;FFKcFrDPrt;zzY+}JR3KP6HB.yCa)/USavI<YNb`<3i2i-/I&^{nj1ZTUi(>D');
define('LOGGED_IN_KEY',    'F#TwHxlj?dF;W2cNrHJq,<,7q6]ScSR3?$3%GE^DU|,pQ*ADwN,h*+YAlx}(&F`<');
define('NONCE_KEY',        'L>VdQw`SggV1S3.s^nW2>,}iewUhS[<Re?y6F^Q:Kk5=H#c17GUX4ECA64D82[NR');
define('AUTH_SALT',        'ebTQVK!,Y@$@2IfE(@|UmmY[O&0C9Q~[uLDjd.rL2t+D3%z4eMYJuZ$RL{|i<n}~');
define('SECURE_AUTH_SALT', 'zuaU[d8A8U3 WGTc/mHKMf`0K/TyZ{n}%nwjE#@apr%FMROk20-K){gc G Ft<>N');
define('LOGGED_IN_SALT',   '-{.k((y?AbmOP/5]ttq9i77=x/B1+t@i}*RT9^!}r8wHmYBuex4pj]oSnXNL5:PF');
define('NONCE_SALT',       '?U/tEfDHZS^?~2BFC(]5K<K9CtCY%3H_#-_Q^H6#CiRZ+opqG+HmliRFicj/Y*!.');

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
