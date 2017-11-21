<?php

add_action(
    'woocommerce_cart_calculate_fees','woocommerce_woomoota_surcharge'
);

/**
 * Add Unique to Total Order
 */
function woocommerce_woomoota_surcharge() {
    global $woocommerce;

    if ( is_admin() && ! defined('DOING_AJAX') ) {
        return;
    }

    if (moota_get_option('use_uq', 'no') == 'no') {
        return;
    }

    $unique = mt_rand(
        moota_get_option('uq_min', 1),
        moota_get_option('uq_max', 999)
    );

    if ( moota_get_option('uq_mode', 'increase') == 'decrease' ) {
        $unique = (int) -$unique;
    }

    $uqLabel = moota_get_option('uq_label', 'Diskon');

    $woocommerce->cart->add_fee($uqLabel, $unique, true, '');
}

add_action('wp_loaded', 'moota_notification_handler');

/**
 * Moota Notification Handler
 */
function moota_notification_handler() {
    if ( !class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'moota_wc_warning' );
        return;
    }

    if (moota_get_option('mode', 'testing') == 'testing') {
        add_action( 'admin_notices', 'moota_warning' );
    }

    if (
        !array_key_exists('woomoota', $_GET)
        || $_GET['woomoota'] != 'push'
    ) {
        return;
    }

    wp_send_json($results);
}
