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
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

/** Database hostname */
define( 'DB_HOST', 'database' );

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
define( 'AUTH_KEY',          '}2ASE*64dS%{**MoPeXQid^Vu`yE4sOs~Ij4hZ%[%^r:h)>,L4!/B{N)l_RZC{zN' );
define( 'SECURE_AUTH_KEY',   ']#6g+F20cl:GJKZ`$d1&/w:e11{L_fN<2q}`) TLN(fDVa^L;V1F_$S#*nBxLs{N' );
define( 'LOGGED_IN_KEY',     '&`UJ=JV]onpL]35D;to0dZ;J!9<xPc$}V`$XZ?eOb,4#q*sNs!K(W,^dE^I7`DF>' );
define( 'NONCE_KEY',         '=~u]*x5ktbB>cl:S,4e_d5An 3<Y:RJ~YH]M }8vt)[|TZfQsFijV:-!?^A!5}-z' );
define( 'AUTH_SALT',         '0*>-Vq0rDa1>]-yX<j5enDA|%@ IO<w@Oy/NaW.#.g$$R21E?IYZz?]{W],gysA>' );
define( 'SECURE_AUTH_SALT',  'R+u(mL l<(DjPbOKRcawx8IC0Cl[hYT>RY}FyVebC@[KJK[6^{?S`Fux:iVhgOXx' );
define( 'LOGGED_IN_SALT',    'YbobK#0)P{=L@sAqX<>u87_UZuB6$$*PMgmP~8hLc)U$:30)BQJuBxoI%y1=DPz;' );
define( 'NONCE_SALT',        '9lecXWPNW{7K}?$RwuCp>zX#5#B`SIE9NFlA9yI/3Gk*!<yy&2lkuuUfDKkG|;3u' );
define( 'WP_CACHE_KEY_SALT', 'e~WftLi,3umto6<;Y>o,58Cx88cRjX[Cg0q>hic[Y_4FB|zO PW:bzQR0`< {dpv' );


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
