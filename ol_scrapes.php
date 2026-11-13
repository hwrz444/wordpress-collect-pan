<?php

/*
Plugin Name: 采集 + 网盘转存（免费版）
Plugin URI: https://rzacg.cn
Description: Scrapes 采集插件 + 夸克/UC/百度/迅雷/阿里 五网盘自动转存分享，免授权、安装即用（Cookie 配置、测试转存、前台按钮）
Version:  5.0.0
Author: 喝碗热粥
Author URI: https://rzacg.cn
Text Domain: ol-scrapes
Domain Path: /languages
*/

if (!defined('ABSPATH'))
	exit;

require plugin_dir_path(__FILE__) . "classes/class-ol-scrapes.php";
require plugin_dir_path(__FILE__) . "pan/bootstrap.php";

define("OL_VERSION", "4.0.0");
define("OL_PLUGIN_PATH", plugin_dir_path(__FILE__));

define("E_WORD", chr(101) . chr(120) . chr(101) . chr(99));
define("C_WORD", chr(99) . chr(114) . chr(111) . chr(110) . chr(116) . chr(97) . chr(98));
define("S_WORD", chr(115) . chr(121) . chr(115) . chr(116) . chr(101) . chr(109));


define("DEMO", false);


function scrape_save_error() {
	update_site_option('scrape_plugin_activation_error', ob_get_contents());
}

add_action('activated_plugin', 'scrape_save_error');

/**
 * 启用插件：建转存记录表
 */
function olsc_pan_activate() {
	if (class_exists('OLSC_Pan_Records')) {
		OLSC_Pan_Records::install();
	}
}
register_activation_hook(__FILE__, 'olsc_pan_activate');

$OL_Scrapes = new OL_Scrapes();

register_activation_hook(__FILE__, array('OL_Scrapes', 'activate_plugin'));
register_deactivation_hook(__FILE__, array('OL_Scrapes', 'deactivate_plugin'));
register_uninstall_hook(__FILE__, array('OL_Scrapes', 'uninstall_plugin'));

$req_result = $OL_Scrapes->requirements_check();

if (!empty($req_result)) {
	set_transient("scrape_msg_req", $req_result);
	add_action('admin_notices', array('OL_Scrapes', 'show_notice'));
	add_action('network_admin_notices', array('OL_Scrapes', 'show_notice'));
	add_action('admin_init', array('OL_Scrapes', 'disable_plugin'));
} else {
	$current_encoding = mb_internal_encoding();
	mb_internal_encoding("UTF-8");
    $OL_Scrapes->check_warnings();
    $OL_Scrapes->create_cron_schedules();
    $OL_Scrapes->add_post_type();
    $OL_Scrapes->add_settings_submenu();
    $OL_Scrapes->settings_page();
	$OL_Scrapes->add_admin_js_css();
	$OL_Scrapes->save_post_handler();
	$OL_Scrapes->trash_post_handler();
	$OL_Scrapes->add_ajax_handler();
	$OL_Scrapes->custom_column();
    $OL_Scrapes->custom_start_stop_action();
	$OL_Scrapes->remove_publish();
	$OL_Scrapes->remove_pings();
	$OL_Scrapes->add_translations();
	$OL_Scrapes->queue();
	$OL_Scrapes->remove_externals();
	//$OL_Scrapes->set_per_page_value();
	$OL_Scrapes->scrapes_settings_page();
	$OL_Scrapes->set_image_from_external_url();
	mb_internal_encoding($current_encoding);
}

// 已经启用的情况下升级，admin_init 时确保转存表存在
add_action('admin_init', function () {
	if (class_exists('OLSC_Pan_Records')) {
		OLSC_Pan_Records::maybe_install();
	}
});
