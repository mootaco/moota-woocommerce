<?php
/**
 * Created by pilus <i@pilus.me> at 2018-02-02 10:58.
 */

namespace Moota\Woocommerce;

use Moota\SDK\PushCallbackHandler;

class Hooks
{
    /**
     * Add Unique Number to Total Order
     */
    public static function surcharge()
    {
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

    /**
     * Moota Notification Handler
     */
    public static function wpLoaded()
    {
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
                static::catchWebhook();
            }
        }
    }

    public static function catchWebhook() {
        moota_init_sdk_config();

        $handler = PushCallbackHandler::createDefault()
            ->setOrderFetcher(new OrderFetcher)
            ->setOrderMatcher(new OrderMatcher)
            ->setOrderFulfiller(new OrderFulfiller)
            ->setDupeFinder(new DuplicateFinder)
        ;

        $statusData = $handler->handle();
        $statusCode = PushCallbackHandler::statusDataToHttpCode($statusData);

        wp_send_json($statusData, $statusCode);
    }

    public static function adminOrderActions($actions)
    {
        $actions['capture_payment'] = array(
            'action' => 'capture_payment',
            'url' => get_admin_url(
                null,
                'post.php?post='. get_the_ID() .'&action=capture-payment'
            ),
            'name' => 'Capture Payment',
        );

        return $actions;
    }

    public static function adminMenu()
    {
        add_menu_page(
            // page_title
            'Moota',

            // menu_title
            'Moota',

            // capability
            'manage_woocommerce',

            // menu_slug
            'moota',

            // function
            function () {
                if ( !curr_user_is_admin() )  {
                    return;
                }


                $api = moota_make_api();

                $banks = $api->listBanks();

                $bankId = !empty($_GET['bank'])
                    ? $_GET['bank']
                    : $banks['data'][0]['bank_id'];

                $transactions = $api->getLastTransactions($bankId);

                include ABSPATH
                    . 'wp-content/plugins/moota-woocommerce/pages/moota-index.php';
            },

            // icon_url
            plugins_url('moota-woocommerce/menu-icon.png'),

            // position => 74.999: `Moota`, 75: `Tools`
            '74.999'
        );
    }
}
