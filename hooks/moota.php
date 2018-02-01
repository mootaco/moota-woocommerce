<?php

use Moota\SDK\PushCallbackHandler;
use Moota\Woocommerce\OrderFetcher;
use Moota\Woocommerce\OrderMatcher;
use Moota\Woocommerce\OrderFullfiler;
use Moota\Woocommerce\DuplicateFinder;

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

    $uqCodes = moota_get_on_hold_uqcodes();

    $unique = null;
    $loopCount = 0;

    while (empty($unique) && ++$loopCount < 10) {
        $unique = mt_rand(
            moota_get_option('uq_min', 1),
            moota_get_option('uq_max', 999)
        );

        $unique = !empty($uqCodes) && in_array($unique, $uqCodes) ? null : $unique;
    }

    if (!empty($unique)) {
        if ( moota_get_option('uq_mode', 'increase') == 'decrease' ) {
            $unique = (int) -$unique;
        }

        $uqLabel = moota_get_option('uq_label', 'Kode Unik Moota');

        $woocommerce->cart->add_fee($uqLabel, $unique, true, '');
    }
}

add_action('wp_loaded', 'moota_loaded');

/**
 * Moota Notification Handler
 */
function moota_loaded() {
    if ( !class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'moota_wc_warning' );

        return;
    }

    if (moota_get_option('mode', 'testing') == 'testing') {
        add_action( 'admin_notices', 'moota_warning' );
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (
            array_key_exists('woomoota', $_GET)
            && $_GET['woomoota'] == 'push'
        ) {
            moota_handle_push();
        }
    }

}

function moota_handle_push() {
    moota_init_sdk_config();

    $handler = PushCallbackHandler::createDefault()
        ->setOrderFetcher(new OrderFetcher)
        ->setOrderMatcher(new OrderMatcher)
        ->setOrderFulfiller(new OrderFullfiler)
        ->setDupeFinder(new DuplicateFinder)
    ;

    $statusData = $handler->handle();
    $statusCode = PushCallbackHandler::statusDataToHttpCode($statusData);

    wp_send_json($statusData, $statusCode);
}

add_filter('___MOOTA_DISABLED___woocommerce_admin_order_actions', function (
    $actions
) {
    $actions['capture_payment'] = array(
        'action' => 'capture_payment',
        'url' => get_admin_url(
            null,
            'post.php?post='. get_the_ID() .'&action=capture-payment'
        ),
        'name' => 'Capture Payment',
    );

    return $actions;
});
