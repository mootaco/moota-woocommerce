<?php namespace Moota\Woocommerce;

use Moota\SDK\Contracts\Push\FindsDuplicate;

class DuplicateFinder implements FindsDuplicate
{
    public function findDupes(array &$mootaInflows, array &$orders)
    {
        $dupes = array();
        $tmpOrders = array();
        $dupedOrderIds = array();

        foreach ($mootaInflows as $inflow) {
            $amount = (float) $inflow['amount'];

            if (empty($dupes[ ($inflow['amount'] . '') ])) {
                $dupes[ ($inflow['amount'] . '') ] = array();
            }

            $currDupes = $dupes[ ($inflow['amount'] . '') ];

            foreach ($orders as $order) {
                if ( ((float) $order->total) === $amount ) {
                    $currDupes[] = $order;
                }
            }

            $dupes[ ($inflow['amount'] . '') ] = $currDupes;
        }

        foreach ($dupes as $amount => $dupedOrders) {
            if (count($dupedOrders) < 2) {
                continue;
            }

            // Send notification to admin
            $adminEmail = get_bloginfo('admin_email');

            $orderIds = array();
            array_walk(
                $dupedOrders,
                function ($item, $idx) use ($orderIds, $dupedOrderIds) {
                    $orderIds[] = $item->ID;
                    $dupedOrderIds[] = $item->ID;
                }
            );

            $orderIds = implode(',', $orderIds);

            $message = __('Hai Admin.') . PHP_EOL . PHP_EOL;

            $message .= sprintf(
                __('Ada order yang sama, dengan nominal %s'),
                moota_rp_format( (float) $amount )
            ) . PHP_EOL . PHP_EOL;

            $message .= __(
                'Berikut Order ID yang bersangkutan: [%s].',
                $orderIds
            ) . PHP_EOL . PHP_EOL;

            $message .= __('Mohon dicek manual.') . PHP_EOL
                . PHP_EOL;

            wp_mail($adminEmail, sprintf(
                __('[%s] Ada nominal order yang sama - Moota'),
                get_option('blogname')
            ), $message);
        }

        foreach ($orders as $order) {
            if ( ! in_array($order->ID, $dupedOrderIds) ) {
                $tmpOrders[] = $order;
            }
        }

        $orders = $tmpOrders;
    }
}
