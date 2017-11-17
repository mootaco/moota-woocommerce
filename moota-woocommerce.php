<?php
/*
Plugin Name: Moota Woocommerce
Plugin URI: http://moota.co
Description: Plugin ini adalah addon dari Moota.co sebagai payment gateway woocomerce wordpress dan auto konfirmasi. Integrasikan toko online Anda dengan moota.co, sistem akan auto konfirmasi setiap ada transaksi masuk ke rekening Anda.
Version: 0.4.5
Author: Moota.co
Author URI: https://moota.co
WC requires at least: 3.1.0
WC tested up to: 3.1.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

include_once 'inc/setting.php';

include_once 'lib/vendor/autoload.php';

include_once 'hooks/moota.php';
include_once 'hooks/admin-menu.php';
