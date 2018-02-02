<?php namespace Moota\Woocommerce;

use Moota\SDK\Contracts\Push\MatchesOrders;

class OrderMatcher implements MatchesOrders
{
    public function match(array $mootaInflows, array $orders)
    {
        /** @var \WC_Order[] $orders */

        $matchedPayments = array();

        $guardedPayments = $mootaInflows;

        if (! empty($orders) && count($orders) > 0) {
            // match woocommerce orders with moota transactions
            foreach ($orders as $order) {
                $transAmount = (float) $order->get_total();
                $tmpPayment = null;

                foreach ($guardedPayments as $i => $mootaInflow) {
                    if (empty($guardedPayments[ $i ])) continue;

                    if ( ( (float) $mootaInflow['amount'] ) === $transAmount ) {
                        $tmpPayment = $mootaInflow;

                        $guardedPayments[ $i ] = null;

                        break;
                    }
                }

                if (!empty($tmpPayment)) {
                    $payment = array(
                        'transactionId' => implode('-', array(
                            $order->get_id(),
                            $tmpPayment['id'],
                            $tmpPayment['account_number']
                        )),

                        'orderId' => $order->get_id(),
                        'mootaId' => $tmpPayment['id'],
                        'mootaBank' => $tmpPayment['bank_type'],
                        'mootaAccNo' => $tmpPayment['account_number'],
                        'amount' => $tmpPayment['amount'],
                        'mootaAmount' => $tmpPayment['amount'],
                        'invoiceAmount' => $transAmount,
                        'orderModel' => $order,
                    );

                    if (
                        !empty($inflow['tags'])
                        && !empty($inflow['tags']['order_id'])
                    ) {
                        $payment['mootaOrderId'] =
                            $tmpPayment['tag']['order_id'];
                    }

                    $matchedPayments[]  = $payment;
                }
            }
        }

        return $matchedPayments;
    }
}
