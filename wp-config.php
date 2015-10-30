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
define('DB_NAME', 'wp-takitani');

/** MySQL database username */
define('DB_USER', 'samiezkay');

/** MySQL database password */
define('DB_PASSWORD', 'samiezkay0727096116');

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
define('AUTH_KEY',         'Q+k/T74wLY-%BSwv[#%DcPWIW!Y!+c%o`<7=Sh*X+YprDtFbAZ8O*#P<Y-#M#]#!');
define('SECURE_AUTH_KEY',  'Z 55eaRjNMJ?-_f>6I$|1c(r!T&Swauy/J|p*=Q<$;/3Jd|/PrV&_bDu+m|1(.jQ');
define('LOGGED_IN_KEY',    'iQUwT@m>2L:d~%:TECE<}3Z++7f|`%`b-VDq#m(_{+q]*Et,mw_vuY7IU3Df~-N-');
define('NONCE_KEY',        '<TVur]gkAKe-s:ulGY0v<r+joFi@2IGif: #^kPwO62H!A9isJui//)+LhQhq/-d');
define('AUTH_SALT',        's!}~1Kj*+j?m0Q|jxltrSo6G*Gw@r;|=j7|.-^=jz*2>2 9V&/v(-@RT-UeqaST(');
define('SECURE_AUTH_SALT', 'HVV@3WS?Q%fk7G=o+day$M(=1+[&d^fBOziFQK!}fw@X(T|U M7kY7_GI~=L|0<h');
define('LOGGED_IN_SALT',   'cVY1fze~m#-2g|gK#=0cTH>q%Gk!){oVTd7dE}Ht [5%IL$-xdb-rZ:-CBY4gu&E');
define('NONCE_SALT',       '+RRMS}i: 6`3eCWF~gPz8*abT^N4:#&^K@,X[wQ;|HL{cU.Z(v19^vNJO#%`(,7[');

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
