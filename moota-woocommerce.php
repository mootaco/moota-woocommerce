<?php
/*
Plugin Name: Moota Woocommerce
Plugin URI: http://moota.co
Description: Plugin ini adalah addon dari Moota.co sebagai payment gateway woocomerce wordpress dan auto konfirmasi. Integrasikan toko online Anda dengan moota.co, sistem akan auto konfirmasi setiap ada transaksi masuk ke rekening Anda.
Version: 0.4.3
Author: Moota.co
Author URI: https://moota.co
WC requires at least: 3.1.0
WC tested up to: 3.1.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
include('inc/setting.php');

add_action( 'woocommerce_cart_calculate_fees','woocommerce_woomoota_surcharge' );

/**
 * Add Unique to Total Order
 */
function woocommerce_woomoota_surcharge() {
    global $woocommerce;
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    if(get_option('woomoota_toggle_status', 'no') == 'no') {
        return;
    }

    $unique = rand(
        get_option('woomoota_start_unique_number', 1),
        get_option('woomoota_end_unique_number', 999)
    );

    if( get_option('woomoota_type_append', 'increase') == 'decrease') {
        $unique = (int) -$unique;
    }

    $woocommerce->cart->add_fee( 'Kode Unik', $unique, true, '' );
}

add_action('wp_loaded', 'moota_notification_handler');

/**
 * Moota Notification Handler
 */
function moota_notification_handler() {
    if ( !class_exists( 'WooCommerce' ) ) {
        die("Woocommerce Not Found");
        return;
    }

    if(get_option('woomoota_mode', 'testing') == 'testing') {
        add_action( 'admin_notices', 'moota_warning' );
    }

    if( !isset($_GET['woomoota']) && $_GET['woomoota'] != 'push') {
        return;
    }

    if( !moota_check_authorize() ) {
        die("Need Authorize");
        return;
    }

    $notifications = json_decode( file_get_contents("php://input") );
    if(!is_array($notifications)) {
        $notifications = json_decode( $notifications );
    }

    $results = array();

    if( count($notifications) > 0 ) {
        foreach( $notifications as $notification) {
            $args = array(
                'post_type'     => 'shop_order',
                'meta_query' => array(
                    array(
                        'key'     => '_order_total',
                        'value'   => (int) $notification->amount,
                        'type'    => 'numeric',
                        'compare' => '=',
                    ),
                ),
                'post_status'   => 'wc-on-hold',
                'date_query'    => array(
                    array(
                        'column'    =>  'post_date_gmt',
                        'after'    =>  '7 days ago'
                    )
                )
            );
            $query = new WP_Query( $args );

            if( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $order = new WC_Order( get_the_ID() );
                    if( $order->has_status( get_option('woomoota_success_status', 'processing') ) ) {
                        continue;
                    }
                    $order->add_order_note('Pembayaran Melalui Bank : ' . strtoupper($notification->bank_type) . ' -  Moota');
                    $order->update_status( get_option('woomoota_success_status', 'processing') );
                    array_push($results, array(
                        'order_id'  =>  $order->get_order_number(),
                        'status'    =>  $order->get_status(),
                    ));
                }
                wp_reset_postdata();
            }
        }
    }

    print(json_encode($results)); exit();
}

/**
 * Check Moota Authorize
 * @return bool
 */
function moota_check_authorize()
{
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0) {
            list($token, $other) = explode(':', substr($_SERVER['HTTP_AUTHORIZATION'], 6));
            if(get_option('woomoota_mode', 'testing') == 'production' && get_option('woomoota_api_key') == $token) {
                return true;
            }
        }

        if(get_option('woomoota_mode', 'testing') == 'testing' && get_option('woomoota_api_key') == $token) {
            return true;
        }
    }

    if(isset($_GET['apikey'])) {
        $token = $_GET['apikey'];
        if(get_option('woomoota_mode', 'testing') == 'production' && get_option('woomoota_api_key') == $token) {
            return true;
        }

        if(get_option('woomoota_mode', 'testing') == 'testing' && $token == get_option('woomoota_mode', 'testing')) {
            return true;
        }
    }
    die("You'r Not Authenticated");
}

function moota_warning() {
    ?>
    <div class="update-nag notice">
        <p><?php _e( '<b>WooMoota</b> Dalam Mode <b>Testing</b>', 'woomoota' ); ?></p>
    </div>
    <?php
}


