<?php namespace Moota\Woocommerce;

use Moota\SDK\Contracts\Push\FulfillsOrder;

class OrderFullfiler implements FulfillsOrder
{
    public function fulfill($order)
    {
        $successStatus = moota_get_option('success_status', 'processing');

        /** @var \WC_Order $orderModel */
        $orderModel = $order['orderModel'];

        if (
            !empty($order['mootaOrderId'])
            || $orderModel->has_status($successStatus)
        ) {
            return false;
        }

        $orderModel->add_order_note(
            ___('Pembayaran Melalui Bank: ')
            . '<strong>' . strtoupper($order['mootaBank']) . '</strong>'
            . ' -  Moota'
        );

        $statusUpdated = $orderModel->update_status($successStatus);

        if ($statusUpdated) {
            $api = moota_make_api();
            $api->linkOrderWithMoota($order['mootaId'], $order['orderId']);

            $orderModel->add_meta_data(
                '_moota_id', $order['mootaId'], true
            );

            $orderModel->save_meta_data();
        }

        return $statusUpdated;
    }
}
