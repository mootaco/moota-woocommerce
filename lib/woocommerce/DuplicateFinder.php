<?php namespace Moota\Woocommerce;

use Moota\SDK\Config as MootaConfig;
use Moota\SDK\Contracts\Push\FindsDuplicate;

class DuplicateFinder implements FindsDuplicate
{
    public function findDupes(array &$mootaInflows, array &$orders)
    {
        $dupes = array();
        $dupedOrderIds = array();
        $idsToRemove = array();
        $dupedCount = 0;

        // for each inflow, find all orders that has the same total
        foreach ($mootaInflows as $inflow) {
            if (
                !empty($inflow['tags'])
                && !empty($inflow['tags']['order_id'])
            ) {
                continue;
            }

            $dupeKey = $inflow['amount'] . '';

            $dupes[ $dupeKey ] = array_filter($orders, function ($order) use (
                $inflow, &$dupedOrderIds, $dupeKey
            ) {
                /** @var \WC_Order $order */
                $isDuped =
                    (float) $order->get_total() === (float) $inflow['amount'];

                // group ids from orders with the same amount
                if ($isDuped) {
                    if ( ! isset($dupedOrderIds[ $dupeKey ]) ) {
                        $dupedOrderIds[ $dupeKey ] = array();
                    }

                    $dupedOrderIds[ $dupeKey ][] = $order->get_id();
                }

                return $isDuped;
            });
        }

        $message = '';

        foreach ($dupedOrderIds as $amount => $orderIds) {
            if (count($orderIds) <= 1) {
                continue;
            }

            $idsToRemove = array_merge($idsToRemove, $orderIds);

            $dupedCount += count($orderIds) - 1;

            $message .= PHP_EOL . sprintf(
                __('Ada order yang sama untuk nominal %s'),
                moota_rp_format( (float) $amount, true )
            ) . PHP_EOL;

            $message .= sprintf(
                    __('Berikut Order ID yang bersangkutan: %s'),

                    PHP_EOL . '- ' . implode(PHP_EOL . '- ', $orderIds)
                )
                . PHP_EOL . PHP_EOL;
        }

        if ($dupedCount > 0) {
            $message = __('Hai Admin.') . PHP_EOL . PHP_EOL . $message;
            $message .= __('Mohon dicek manual.') . PHP_EOL
                . PHP_EOL;

            if ( MootaConfig::isLive() ) {
                wp_mail(
                    get_bloginfo('admin_email'),
                    sprintf(
                        __('[%s] Ada nominal order yang sama - Moota'),
                        get_option('blogname')
                    ),
                    $message
                );
            }
        }

        // change the duplicates in $orders into nulls
        foreach ($orders as $idx => $order) {
            if ( !empty($order) && in_array($order->get_id(), $idsToRemove) ) {
                $orders[ $idx ] = null;
            }
        }

        // filter out all nulls;
        $orders = array_filter($orders);

        return $orders;
    }
}
