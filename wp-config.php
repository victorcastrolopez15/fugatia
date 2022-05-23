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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'fugatia' );

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
define( 'AUTH_KEY',         '[^pM2V7Eae<Mq~)h  t6?,bYC]tp.!~{m>Rv0M38^yQs9Oz]&(ha;1>dZWFTbo)y' );
define( 'SECURE_AUTH_KEY',  'gheLK53V|jm[gxf[]skV-8?mz.8Kd_MI V}6J7J5}(0^FvGG7aW$TPw~P:nuWU=G' );
define( 'LOGGED_IN_KEY',    '[cPFm{KM3Qf?4k}9c^bU@3-X$X#K+lVI&..6VR>rCZl[MB ANf[6$5%&%)Cy;M!m' );
define( 'NONCE_KEY',        'w9MOH}=p9t4VVGz@-8gRQ,_,vCrSRQ.$oZO<nm?f_>np_t_HFje9r&1TV71Y]qnv' );
define( 'AUTH_SALT',        'O9Hs/>}0h>oR1nQD1|]L* 4rj4cQ)McMXr|*~hM*%L9SlzW`0` MVZ!toQ*)SUl)' );
define( 'SECURE_AUTH_SALT', ']}&[IxD%s?0`<en9_L3c}$P^PxaL$_KL&`@^71D&.SYL!UVXGcNSPY^DE{ ?+0V!' );
define( 'LOGGED_IN_SALT',   'T/f*;D|[g!70)${WaRktz@t6B0]i+Zc/zfjmo4,;&JLz&bt.,LIU%seWzARV3dqJ' );
define( 'NONCE_SALT',       '[ PF1xdw_De7Vu++W&WkCa%|7FEKi8h;]FMC:oC)is*48M76+%(Md|*)s;{ULX;:' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'pw_';

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
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
