<?php

include_once 'lib/vendor/autoload.php';

function moota_debug ($data, $display = false) {
    if ($display) {
        echo '<pre><code>', var_export($data),'</code></pre>';
        return;
    }

    @file_put_contents(
        ABSPATH . 'debug.log',
        json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL,
        FILE_APPEND
    );
}

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
            list($token, ) = explode(':', substr(
                $_SERVER['HTTP_AUTHORIZATION'], 6
            ));

            if (
                moota_get_option('mode', 'testing') == 'production'
                && moota_get_option('api_key') == $token
            ) {
                return true;
            }
        }

        if (
            moota_get_option('mode', 'testing') == 'testing'
            && moota_get_option('api_key') == $token
        ) {
            return true;
        }
    }

    if (isset($_GET['apikey'])) {
        $token = $_GET['apikey'];

        if (
            moota_get_option('mode', 'testing') == 'production'
            && moota_get_option('api_key') == $token
        ) {
            return true;
        }

        if (
            moota_get_option('mode', 'testing') == 'testing'
            && $token == moota_get_option('mode', 'testing')
        ) {
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
        <p><?php _e(
                'Plugin <b>WooCommerce</b> belum terinstall.', 'woomoota'
        ); ?></p>
    </div>
    <?php
}

function moota_init_sdk_config($refresh = null) {
    $refresh = $refresh ?: false;

    if (empty(Moota\SDK\Config::$apiKey)) {
        Moota\SDK\Config::fromArray(
            moota_opts_to_config(moota_populate_options($refresh))
        );
    }
}

function moota_make_api() {
    moota_init_sdk_config();

    static $api;

    if (empty($api)) $api = new Moota\SDK\Api;

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

    if (empty($date) || false === $date) {
        return $strDate;
    }

    return $date->format('d/m/Y');
}

/**
 * @param DateTime $date
 *
 * @return string
 */
function moota_human_date($date) {
    if ( empty($date) || ! is_a($date, \DateTime::class) ) {
        return '';
    }

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

    // zero prefixed
    $zDay = $date->format('d');

    $year = (int) $date->format('Y');
    $nDay = (int) $date->format('w');
    $nMonth = (int) $date->format('n');

    $humanDate =
        "{$dayNames[ $nDay ]}, {$zDay} {$monthNames[ $nMonth ]}. {$year}";

    return $humanDate;
}

/**
 * Get `woomoota_*` option from Wordpress' options table.
 * Uses suffix, instead of full `woomoota_*` key.
 *
 * @param string $suffix
 * @param mixed $default
 *
 * @return mixed
 */
function moota_get_option($suffix, $default = null) {
    return get_option("woomoota_{$suffix}", $default);
}

function moota_populate_options($refresh = null) {
    static $options;

    $refresh = empty($refresh) ? false : $refresh;

    if ( empty($options) || $refresh ) {
        $options = array(
            'api_key' => moota_get_option('api_key'),
            'mode' => moota_get_option('mode'),
            'oldest_order' => moota_get_option('oldest_order'),
            'success_status' => moota_get_option('success_status'),
            'uq_label' => moota_get_option('uq_label'),
            'uq_min' => moota_get_option('uq_min'),
            'uq_mode' => moota_get_option('uq_mode'),
            'uq_max' => moota_get_option('uq_max'),
            'use_uq' => moota_get_option('use_uq'),
        );
    }

    return $options;
}

function moota_opts_to_config($opts) {
    return array(
        'apiKey' => $opts['api_key'],
        'apiTimeout' => 180, // 5 minutes
        'sdkMode' => $opts['mode'],
        'uqMin' => $opts['uq_min'],
        'uqMode' => $opts['uq_mode'] === 'yes' ? true : false,
        'uqMax' => $opts['uq_max'],
        'useUniqueCode' => $opts['use_uq'] === 'yes' ? true : false,
    );
}

function moota_get_on_hold_uqcodes () {
    global $wpdb;

    $uqLabel = moota_get_option('uq_label', 'Kode Unik Moota');
    $uqMax = moota_get_option('uq_max', 999);

    $sql = <<<EOF
SELECT p.`ID`, oi.`order_item_id`, oim.`meta_value` `unique_code`
FROM `{$wpdb->prefix}posts` p
LEFT JOIN `{$wpdb->prefix}woocommerce_order_items` oi
  ON (
    oi.`order_id` = p.`ID`
    AND oi.`order_item_type` = 'fee'
    AND oi.`order_item_name` = '{$uqLabel}'
  )
LEFT JOIN `{$wpdb->prefix}woocommerce_order_itemmeta` oim
  ON (
    oim.`order_item_id` = oi.`order_item_id`
    AND oim.`meta_key` = '_fee_amount'
  )
WHERE `post_type`='shop_order'
  AND `post_status` NOT IN (
    'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed'
  )
LIMIT 0, {$uqMax}
EOF;

    $results = $wpdb->get_results($sql, OBJECT);

    $uqCodes = [];

    foreach ($results as $meta) {
        $uqCodes[] = $meta->unique_code;
    }

    return $uqCodes;
}

function ___($text) {
    return __($text, 'woomota');
}

function _desc($text) {
    return '<br>' . __($text, 'woomota');
}
