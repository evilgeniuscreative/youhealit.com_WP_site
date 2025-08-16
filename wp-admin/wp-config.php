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
define('DB_NAME', 'evilgeo2_WPEFD');

/** Database username */
define('DB_USER', 'evilgeo2_WPEFD');

/** Database password */
define('DB_PASSWORD', 'jfzTYbKlZn6Hs0)q5');

/** Database hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY', '9f18c6d6a13d480497ca0f24b116f6d01130488409dbd9f441842559a6a27ffc');
define('SECURE_AUTH_KEY', '27788ac56cc33f2db80d0061dd2e9661d160abbfdeff2c6df15dfe2084b5b4fa');
define('LOGGED_IN_KEY', '4d96fac34914793936297644cd38b37be30a9af33f53cc095efae0f33f66b42f');
define('NONCE_KEY', 'c3784140cc9b6079ffbd01964deb32c72a688d4b7ea9a6e40e15836e9d7f9121');
define('AUTH_SALT', '3831b46fe3297b56becd0c639b4c7c1ec1cdc331f973a14fb6e3d5d8c983fcb2');
define('SECURE_AUTH_SALT', '36f248c1482e5681c4b61a92fdb5cdb64cd65f1555fb657f22626995ef89f60b');
define('LOGGED_IN_SALT', '365bc56f2f3f78ee78c519be2748ef4cd5b49c7da8a863146d60cbe30222613f');
define('NONCE_SALT', '1041b1b48634cb6ca21c5bb96f58af28962b430ae318c8ca00d31318606f5622');

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
$table_prefix = 'oz5_';
define('WP_CRON_LOCK_TIMEOUT', 120);
define('AUTOSAVE_INTERVAL', 300);
define('WP_POST_REVISIONS', 20);
define('EMPTY_TRASH_DAYS', 7);
define('WP_AUTO_UPDATE_CORE', true);

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
