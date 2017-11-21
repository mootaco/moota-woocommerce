<?php

WC_Settings_Tab_Woomota::init();

class WC_Settings_Tab_Woomota {
    public static function init() {
        add_filter(
            'woocommerce_settings_tabs_array',
            __CLASS__ . '::add_settings_tab',
            50
        );
    }

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['setting_tab_woomoota'] = ___('WooMoota');

        return $settings_tabs;
    }
}

add_action(
    'woocommerce_settings_tabs_setting_tab_woomoota',
    'woomota_settings_tab'
);

function woomota_settings_tab() {
    woocommerce_admin_fields( get_woomoota_settings() );
}

function get_woomoota_settings() {
    $settings = array(
        'section_title' => array(
            'name' => ___('Pengaturan API Key & Nomor Unik Pesanan'),
            'type' => 'title',
            'desc' => '',
            'id' => 'woomoota_settings_label'
        ),
        'mode' => array(
            'name' => ___('Mode'),
            'type' => 'select',
            'desc' => _desc('Pilih Mode'),
            'default' =>  'testing',
            'options' => array(
                'testing' => 'Testing',
                'production' => 'Production'
            ),
            'id' => 'woomoota_mode'
        ),
        'api_endpoint' => array(
            'name' => ___('API Endpoint'),
            'type' => 'text',
            'css' => 'min-width:420px;',
            'default' => add_query_arg(
                'woomoota', 'push', get_bloginfo('url') . '/'
            ),
            'desc' => _desc(
                'Masukan URL ini kedalam pengaturan Push Notification'
            ),
            'id' => 'woomoota_api_endpoint',
            'custom_attributes' => array(
                'disabled' => true
            )
        ),
        'api_key' => array(
            'name' => ___('API Key'),
            'type' => 'text',
            'css' => 'min-width:420px;',
            'desc' => _desc(
            'Dapatkan API Key melalui : '
                    . '<a href="https://app.moota.co/settings?tab=api" '
                    . 'target="_new">https://app.moota.co/settings?tab=api</a>'
            ),
            'required' => true,
            'id' => 'woomoota_api_key'
        ),
        'success_status' => array(
            'name' => ___('Status Berhasil'),
            'type' => 'select',
            'desc' => _desc(
                'Status setelah berhasil menemukan order yang telah dibayar'
            ),
            'default' =>  'processing',
            'options' => array(
                'completed' => 'Completed',
                'on-hold' => 'On Hold',
                'processing' => 'Processing'
            ),
            'id' => 'woomoota_success_status'
        ),
        'oldest_order' => array(
            'name' => ___('Batas lama pengecekkan order'),
            'type' => 'number',
            'desc' => _desc(
            'Pengecekkan order berdasarkan x hari ke belakang '
                    . '(default: 7 hari kebelakang)'
            ),
            'id' => 'woomoota_oldest_order',
            'default' => 7,
            'custom_attributes' => array(
                'min' => 1,
                'max' => 31
            )
        ),
        'use_uq' => array(
            'name' => ___('Nomor Unik ?'),
            'type' => 'checkbox',
            'desc' => ___(
            'Centang, untuk aktifkan fitur penambahan 3 angka unik di '
                    . 'setiap akhir pesanan / order. Sebagai pembeda dari '
                    . 'order satu dengan yang lainnya.'
            ),
            'id' => 'woomoota_use_uq'
        ),
        'uq_label' => array(
            'name' => ___('Label Kode Unik'),
            'type' => 'text',
            'default' => 'Kode Unik',
            'css' => 'min-width:420px;',
            'desc' => _desc('Label yang akan muncul di form checkout'),
            'id' => 'woomoota_uq_label'
        ),
        'uq_mode' => array(
            'name' => ___('Tipe Kode Unik'),
            'type' => 'select',
            'desc' => _desc(
            'Increase = Menambah unik number ke total harga, '
                    . 'Decrease = Mengurangi total harga dengan unik number'
            ),
            'default' =>  'increase',
            'options' => array(
                'increase' => 'Tambahkan',
                'decrease' => 'Kurangi'
            ),
            'id' => 'woomoota_uq_mode'
        ),
        'uq_min' => array(
            'name' => ___('Angka Unik - Minimum'),
            'type' => 'number',
            'desc' => _desc('Masukan nilai Minimum angka unik'),
            'id' => 'woomoota_uq_min',
            'default' => 1,
            'custom_attributes' => array( 'min' => 1, 'max' => 999 )
        ),
        'uq_max' => array(
            'name' => ___('Angka Unik - Maksimum'),
            'type' => 'number',
            'desc' => _desc('Masukan nilai Maksimum angka unik'),
            'id' => 'woomoota_uq_max',
            'default' => 999,
            'custom_attributes' => array( 'min' => 1, 'max' => 999 )
        ),
        'section_end' => array(
            'type' => 'sectionend',
            'id' => 'wc_settings_tab_woomoota_section_end'
        )
    );

    return apply_filters('wc_setting_tab_woomoota_settings', $settings);
}

add_action(
    'woocommerce_update_options_setting_tab_woomoota',
    'woomoota_update_settings'
);

function woomoota_update_settings() {
    woocommerce_update_options(get_woomoota_settings());
}
