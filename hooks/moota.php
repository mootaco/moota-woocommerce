<?php

add_action(
    'woocommerce_cart_calculate_fees','woocommerce_woomoota_surcharge'
);

/**
 * Add Unique to Total Order
 */
function woocommerce_woomoota_surcharge() {
    global $woocommerce;

    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    if (get_option('woomoota_toggle_status', 'no') == 'no') {
        return;
    }

    $unique = rand(
        get_option('woomoota_start_unique_number', 1),
        get_option('woomoota_end_unique_number', 999)
    );

    if ( get_option('woomoota_type_append', 'increase') == 'decrease') {
        $unique = (int) -$unique;
    }

    $label_unique = get_option('woomoota_label_unique', 'Diskon');

    $woocommerce->cart->add_fee( $label_unique, $unique, true, '' );
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

    if (get_option('woomoota_mode', 'testing') == 'testing') {
        add_action( 'admin_notices', 'moota_warning' );
    }

    if ( !isset($_GET['woomoota']) && $_GET['woomoota'] != 'push') {
        return;
    }

    if ( !moota_check_authorize() ) {
        die("Need Authorize");
        return;
    }

    $notifications = json_decode( file_get_contents("php://input") );
    if (!is_array($notifications)) {
        $notifications = json_decode( $notifications );
    }

    $results = array();

    if ( count($notifications) > 0 ) {
        $range_order = get_option('woomoota_range_order', 7);
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
                        'after'    =>  $range_order . ' days ago'
                    )
                )
            );
            $query = new WP_Query( $args );

            if ( $query->have_posts() ) {
                if ($query->found_posts > 1) {

                    /** Send notification to admin */
                    $admin_email = get_bloginfo('admin_email');
                    $message = sprintf( __( 'Hai Admin.' ) ) . "\r\n\r\n";
                    $message .= sprintf( __( 'Ada order yang sama, dengan nominal Rp %s' ), $notification->amount ). "\r\n\r\n";
                    $message .= sprintf( __( 'Mohon dicek manual.' ) ). "\r\n\r\n";
                    wp_mail( $admin_email, sprintf( __( '[%s] Ada nominal order yang sama - Moota' ), get_option('blogname') ), $message );

                } else {

                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $order = new WC_Order( get_the_ID() );
                        if ( $order->has_status( get_option('woomoota_success_status', 'processing') ) ) {
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
    }

    print(json_encode($results)); exit();
}
