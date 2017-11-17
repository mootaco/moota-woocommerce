<?php

WC_Settings_Tab_Woomota::init();
class WC_Settings_Tab_Woomota {
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    }
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['setting_tab_woomoota'] = __( 'WooMoota', 'woomoota' );
        return $settings_tabs;
    }
}

add_action( 'woocommerce_settings_tabs_setting_tab_woomoota', 'woomota_settings_tab' );
function woomota_settings_tab() {
    woocommerce_admin_fields( get_woomoota_settings() );
}
function get_woomoota_settings() {
    $settings = array(
        'section_title' => array(
            'name'     => __( 'Pengaturan API Key & Nomor Unik Pesanan', 'woomoota' ),
            'type'     => 'title',
            'desc'     => '',
            'id'       => 'woomoota_settings_label'
        ),
        'mode' => array(
            'name' => __( 'Mode', 'woomoota' ),
            'type' => 'select',
            'desc' => __( 'Pilih Mode', 'woomoota' ),
            'default'   =>  'testing',
            'options' => array(
                'testing'       => 'Testing',
                'production'    => 'Production'
            ),
            'id'   => 'woomoota_mode'
        ),
        'api_endpoint' => array(
            'name'              => __( 'API Endpoint', 'woomoota' ),
            'type'              => 'text',
            'css'               => 'min-width:420px;',
            'default'           => add_query_arg( 'woomoota', 'push', get_bloginfo('url') ),
            'desc'              => __( 'Masukan URL ini kedalam pengaturan Push Notification', 'woomoota' ),
            'id'                => 'woomoota_api_endpoint',
            'custom_attributes' => array(
                'disabled'  => true
            )
        ),
        'api_key' => array(
            'name' => __( 'Api Key', 'woomoota' ),
            'type' => 'text',
            'css'      => 'min-width:420px;',
            'desc' => __( 'Dapatkan API Key melalui : <a href="https://app.moota.co/settings?tab=api" target="_new">https://app.moota.co/settings?tab=api</a>', 'woomoota' ),
            'id'   => 'woomoota_api_key'
        ),
        'success_status' => array(
            'name' => __( 'Status Berhasil', 'woomoota' ),
            'type' => 'select',
            'desc' => __( 'Status setelah berhasil menemukan order yang telah dibayar', 'woomoota' ),
            'default'   =>  'processing',
            'options' => array(
                'completed'     => 'Completed',
                'on-hold'       => 'On Hold',
                'processing'    => 'Processing'
            ),
            'id'   => 'woomoota_success_status'
        ),
        'toggle_status' => array(
            'name' => __( 'Nomor Unik ?', 'woomoota' ),
            'type' => 'checkbox',
            'desc' => __( 'Centang, untuk aktifkan fitur penambahan 3 angka unik di setiap akhir pesanan / order. Sebagai pembeda dari order satu dengan yang lainnya.', 'woomoota' ),
            'id'   => 'woomoota_toggle_status'
        ),
        'type_append' => array(
            'name' => __( 'Tipe Tambahan', 'woomoota' ),
            'type' => 'select',
            'desc' => __( 'Increase = Menambah unik number ke total harga, Decrease = Mengurangi total harga dengan unik number', 'woomoota' ),
            'default'   =>  'increase',
            'options' => array(
                'increase'      => 'Increase',
                'decrease'      => 'Decrease'
            ),
            'id'   => 'woomoota_type_append'
        ),
        'unique_start' => array(
            'name' => __( 'Batas Awal Angka Unik', 'woomoota' ),
            'type' => 'number',
            'desc' => __( 'Masukan batas awal angka unik', 'woomoota' ),
            'id'   => 'woomoota_start_unique_number',
            'custom_attributes' => array(
                'min'  => 1,
                'max'  => 999
            )
        ),
        'unique_end' => array(
            'name' => __( 'Batas Akhir Angka Unik', 'woomoota' ),
            'type' => 'number',
            'desc' => __( 'Masukan batas akhir angka unik', 'woomoota' ),
            'id'   => 'woomoota_end_unique_number',
            'custom_attributes' => array(
                'min'  => 1,
                'max'  => 999
            )
        ),
        'section_end' => array(
            'type' => 'sectionend',
            'id' => 'wc_settings_tab_woomoota_section_end'
        )
    );
    return apply_filters( 'wc_setting_tab_woomoota_settings', $settings );
}

add_action( 'woocommerce_update_options_setting_tab_woomoota', 'woo_moota_update_settings' );
function woo_moota_update_settings() {
    woocommerce_update_options( get_woomoota_settings() );
}
