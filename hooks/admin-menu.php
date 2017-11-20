<?php

add_action('admin_menu', function () {
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

            include __DIR__ . '/../pages/moota-index.php';
        },

        // icon_url
        plugins_url('moota-woocommerce/menu-icon.png'),

        // position, 75: `Tools`
        '74.999'
    );
});
