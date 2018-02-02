<?php
/*
Plugin Name: Moota Woocommerce
Plugin URI: http://moota.co
Description: Plugin ini adalah addon dari Moota.co sebagai payment gateway woocomerce wordpress dan auto konfirmasi. Integrasikan toko online Anda dengan moota.co, sistem akan auto konfirmasi setiap ada transaksi masuk ke rekening Anda.
Version: 0.4.5
Author: Moota.co
Author URI: https://moota.co
WC requires at least: 3.1.0
WC tested up to: 3.1.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

include_once 'lib/vendor/autoload.php';

include_once 'inc/setting.php';

use Moota\Woocommerce\Hooks;

add_action('woocommerce_cart_calculate_fees', [Hooks::class, 'surcharge']);

add_action('wp_loaded', [Hooks::class, 'wpLoaded']);

// doesn't work as of woocommerce 3.3, dunno why
add_filter(
    '__MOOTA_DISABLED__woocommerce_admin_order_actions',
    [Hooks::class, 'adminOrderActions'],
    10, // execution order
    1 // number of params
);

add_action('admin_menu', [Hooks::class, 'adminMenu']);
