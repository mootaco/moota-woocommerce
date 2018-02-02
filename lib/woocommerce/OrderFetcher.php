<?php namespace Moota\Woocommerce;

use Moota\SDK\Contracts\Push\FetchesOrders;

class OrderFetcher implements FetchesOrders
{
    public function fetch(array $inflowAmounts)
    {
        $oldestOrder = moota_get_option('oldest_order', 7);

        $queryArgs = array(
            'post_type' => 'shop_order',
            'post_status' => 'wc-on-hold',
            'date_query' => array(
                array(
                    'column' =>  'post_date_gmt',
                    'after' =>  $oldestOrder . ' days ago'
                )
            ),
        );

        foreach ($inflowAmounts as $idx => $inflowAmount) {
            $inflowAmounts[ $idx ] = number_format((float) $inflowAmount, 2);
        }

        if ( !empty($inflowAmounts) && count($inflowAmounts) > 0 ) {
            $queryArgs['meta_query'] = array(
                array(
                    'key' => '_order_total',
                    'value' => $inflowAmounts,
                    'compare' => 'IN',
                ),
            );
        }

        $tmpOrders = (new \WP_Query($queryArgs))->get_posts();
        $orders = array();
        $dbgOrders = [];

        foreach ($tmpOrders as $tmpOrder) {
            $tmp = new \WC_Order($tmpOrder->ID);
            $orders[] = $tmp;
            $dbgOrders[] = (array) $tmp;
        }

        return $orders;
    }
}
