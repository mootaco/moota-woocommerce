<?php

use Moota\SDK\Config as MootaConfig;

function curr_user_is_admin() {
    if (empty( $user = wp_get_current_user() )) {
        return false;
    }

    return in_array('administrator', (array) $user->roles);
}

/**
 * Check Moota Authorize
 * @return bool
 */
function moota_check_authorize() {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0) {
            list($token, $other) = explode(':', substr($_SERVER['HTTP_AUTHORIZATION'], 6));
            if (get_option('woomoota_mode', 'testing') == 'production' && get_option('woomoota_api_key') == $token) {
                return true;
            }
        }

        if (get_option('woomoota_mode', 'testing') == 'testing' && get_option('woomoota_api_key') == $token) {
            return true;
        }
    }

    if (isset($_GET['apikey'])) {
        $token = $_GET['apikey'];
        if (get_option('woomoota_mode', 'testing') == 'production' && get_option('woomoota_api_key') == $token) {
            return true;
        }

        if (get_option('woomoota_mode', 'testing') == 'testing' && $token == get_option('woomoota_mode', 'testing')) {
            return true;
        }
    }
    wp_die('You are Not Authenticated');
}

function moota_warning() {
    ?>
    <div class="update-nag notice" style="display: block;">
        <p><?php _e( '<b>WooMoota</b> Dalam Mode <b>Testing</b>', 'woomoota' ); ?></p>
    </div>
    <?php
}

function moota_wc_warning() {
    ?>
    <div class="update-nag notice" style="display: block;">
        <p><?php _e( 'Plugin <b>WooCommerce</b> belum terinstall.', 'woomoota' ); ?></p>
    </div>
    <?php
}

function moota_init_sdk_config() {
    if (empty(MootaConfig::$apiKey)) {
        MootaConfig::fromArray(array(
            'apiKey' => get_option('woomoota_api_key'),
            'apiTimeout' => 180, // 5 minutes
            'sdkMode' => get_option('woomoota_mode'),
        ));
    }
}

function moota_make_api() {
    moota_init_sdk_config();

    $api = new Moota\SDK\Api;

    return $api;
}

function terbilang($n) {
    $abil = array(
        "", "satu", "dua", "tiga", "empat", "lima", "enam",
        "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"
    );

    if ($n < 12)
        return " " . $abil[$n];
    elseif ($n < 20)
        return terbilang($n - 10) . "belas";
    elseif ($n < 100)
        return terbilang($n / 10) . " puluh" . terbilang($n % 10);
    elseif ($n < 200)
        return " seratus" . terbilang($n - 100);
    elseif ($n < 1000)
        return terbilang($n / 100) . " ratus" . terbilang($n % 100);
    elseif ($n < 2000)
        return " seribu" . terbilang($n - 1000);
    elseif ($n < 1000000)
        return terbilang($n / 1000) . " ribu" . terbilang($n % 1000);
    elseif ($n < 1000000000)
        return terbilang($n / 1000000) . " juta" . terbilang($n % 1000000);
}
