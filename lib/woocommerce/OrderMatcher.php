<?php namespace Moota\Woocommerce;

use Moota\SDK\Contracts\Push\MatchesOrders;

class OrderMatcher implements MatchesOrders
{
    public function match(array $mootaInflows, array $orders)
    {
        $matchedPayments = array();

        $successStatus = moota_get_option('success_status', 'processing');

        if (!empty($posts)) {
            if ( false /* duplicates */ ) {
//                // Send notification to admin
//                $admin_email = get_bloginfo('admin_email');
//
//                $message = sprintf( __( 'Hai Admin.' ) ) . PHP_EOL . PHP_EOL;
//
//                $message .= sprintf( __( 'Ada order yang sama, dengan nominal Rp %s' ), $notification->amount ) . PHP_EOL . PHP_EOL;
//
//                $message .= sprintf( __( 'Mohon dicek manual.' ) ) . PHP_EOL . PHP_EOL;
//
//                wp_mail( $admin_email, sprintf( __( '[%s] Ada nominal order yang sama - Moota' ), get_option('blogname') ), $message );

            } else {
//                $query->the_post();
//                $order = new WC_Order( get_the_ID() );
//
//                if ( $order->has_status($successStatus) ) {
//                    continue;
//
//                }
//
//                $order->add_order_note('Pembayaran Melalui Bank : ' . strtoupper($notification->bank_type) . ' -  Moota');
//
//                $order->update_status($successStatus);
//
//                array_push($results, array(
//                    'order_id'  =>  $order->get_order_number(),
//                    'status'    =>  $order->get_status(),
//                ));
//
//                wp_reset_postdata();
            }
        }

        return $matchedPayments;
    }
}
