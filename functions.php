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
        <p><?php _e(
            '<b>WooMoota</b> Dalam Mode <b>Testing</b>', 'woomoota'
        ); ?></p>
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

function moota_rp_format($money, $withCurr = false) {

    $formatted = number_format(
        $money, 2, ',', '.'
    );

    if ($withCurr) {
        return 'Rp. ' . $formatted;
    }

    return $formatted;
}

/**
 * @param string $strDate
 * @param DateTime $date Pass by ref
 *
 * @return string
 */
function moota_short_date($strDate, &$date) {
    $date = date_create_from_format('Y-m-d H:i:s', $strDate);

    if (empty($date)) {
        return '';
    }

    return $date->format('d/m/Y');
}

/**
 * @param DateTime $date
 *
 * @return string
 */
function moota_human_date(DateTime $date) {
    static $dayNames;
    static $monthNames;

    if (empty($dayNames)) {
        $dayNames = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
        );
        $monthNames = array(
            'Jan', 'Peb', 'Mar', 'Apr', 'Mei', 'Juni',
            'Juli', 'Agt', 'Sept', 'Okt', 'Nop', 'Des',
        );
    }

    $zDay = $date->format('d');
    $year = (int) $date->format('Y');
    $nDay = (int) $date->format('w');
    $nMonth = (int) $date->format('n');

    $humanDate =
        "{$dayNames[ $nDay ]}, {$zDay} {$monthNames[ $nMonth ]}. {$year}";

    return $humanDate;
}
