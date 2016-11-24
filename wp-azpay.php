<?php
/**
 * Plugin Name: WP Donate AZPay
 * Description: Checkout para doação integrado com o gateway de pagamento AZPay - <a href="https://github.com/AZPay/WordPress-Donate-AZPay-Checkout">Documentação</a>
 * Version: 1.0
 * Author: Lucas Palencia e Gabriel Manussakis
**/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('WPAC_VERSION', '1.0');
define('WPAC_DIR', plugin_dir_path( __FILE__ ) );
define('WPAC_URL', plugins_url('', __FILE__) );

require_once( WPAC_DIR . '/inc/sdk-azpay/azpay.php' );
require_once( WPAC_DIR . '/inc/class-wp-azpaycheckout.php' );

/**
 * Plugin is activated.
 */
register_activation_hook( __FILE__, array( 'WP_AZPayCheckout', 'activation' ) );

/**
 * Plugin is deactivated.
 */
register_deactivation_hook( __FILE__, array( 'WP_AZPayCheckout', 'deactivation' ) );

/**
 * Initialize the plugin.
 */
add_action( 'plugins_loaded', array( 'WP_AZPayCheckout', 'getInstance' ) );
