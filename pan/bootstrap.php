<?php
/**
 * 网盘转存模块 —— 引导文件
 *
 * 把 xinyue-search 的 netdisk 库移植到 WordPress：
 *  - 去掉 ThinkPHP 依赖（Config / input / Db / curlHelper）
 *  - 统一配置读取（WP option）
 *  - 提供 olsc_cfg / olsc_http / olsc_ok / olsc_err 这几个全局助手
 */

if (!defined('ABSPATH')) {
    exit;
}

define('OLSC_PAN_DIR', plugin_dir_path(__FILE__));
define('OLSC_PAN_URL', plugin_dir_url(__FILE__));

require_once OLSC_PAN_DIR . 'loader.php';

// 加密核心（发布包里只有 enc/*.enc，没有明文）
olsc_core_load('pan/pan/BasePan.php');
olsc_core_load('pan/pan/BaiduWork.php');
olsc_core_load('pan/pan/BaiduPan.php');
olsc_core_load('pan/pan/QuarkPan.php');
olsc_core_load('pan/pan/UcPan.php');
olsc_core_load('pan/pan/XunleiPan.php');
olsc_core_load('pan/pan/AlipanPan.php');

require_once OLSC_PAN_DIR . 'class-records.php';
olsc_core_load('pan/class-transfer.php');
olsc_core_load('pan/class-frontend.php');
require_once OLSC_PAN_DIR . 'class-admin.php';
olsc_core_load('pan/class-ajax.php');

/* 免费版：不加载卡密模块，任何人拿到即可使用 */

/* ===================== PHP 兼容垫片 ===================== */

// 迅雷/阿里 的类里用了 str_starts_with（PHP 8.0+），7.x 站点补一个
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        $haystack = (string) $haystack;
        $needle = (string) $needle;
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('mb_strtolower')) {
    function mb_strtolower($str) {
        return strtolower((string) $str);
    }
}
if (!function_exists('mb_strpos')) {
    function mb_strpos($haystack, $needle, $offset = 0) {
        return strpos((string) $haystack, (string) $needle, (int) $offset);
    }
}

/* ===================== 配置读写 ===================== */

/**
 * 所有网盘配置存在一个 option 里，键名与原始 qfshop 配置一一对应
 */
function olsc_pan_defaults() {
    return array(
        // 全局
        'enabled'        => 1,
        'cache_hours'    => 24,
        'banned'         => '',
        'button_prefix'  => '📦 ',
        'title_suffix'   => '',

        // 夸克
        'quark_enable'   => 1,
        'quark_cookie'   => '',
        'quark_file'     => '0',      // 转存目录 fid，0 = 根目录

        // UC
        'uc_enable'      => 1,
        'uc_cookie'      => '',
        'uc_file'        => '0',

        // 百度
        'baidu_enable'   => 1,
        'baidu_cookie'   => '',
        'baidu_file'     => '/采集转存',   // 路径

        // 迅雷
        'xunlei_enable'  => 1,
        'xunlei_cookie'  => '',      // 实际上是 refresh_token
        'xunlei_file'    => '',      // 转存目录 id

        // 阿里
        'ali_enable'     => 1,
        'ali_token'      => '',      // refresh_token
        'ali_file'       => 'root',  // 转存目录 file_id
    );
}

function olsc_pan_settings() {
    $saved = get_option('olsc_pan_settings', array());
    if (!is_array($saved)) {
        $saved = array();
    }
    return array_merge(olsc_pan_defaults(), $saved);
}

/**
 * 读一个配置项；兼容原项目 Config('qfshop.xxx') 的键名
 */
function olsc_cfg($key, $default = '') {
    $settings = olsc_pan_settings();

    $map = array(
        'Authorization'  => 'ali_token',
        'quark_banned'   => 'banned',
        'quark_file_time' => 'quark_file',
        'uc_file_time'   => 'uc_file',
        'baidu_file_time' => 'baidu_file',
        'xunlei_file_time' => 'xunlei_file',
        'ali_file_time'  => 'ali_file',
    );
    $k = isset($map[$key]) ? $map[$key] : $key;

    if (isset($settings[$k]) && $settings[$k] !== '') {
        return $settings[$k];
    }
    return $default;
}

/**
 * 保存一个配置项（迅雷 refresh_token 轮换时会用到）
 */
function olsc_set($key, $value) {
    $settings = get_option('olsc_pan_settings', array());
    if (!is_array($settings)) {
        $settings = array();
    }
    $settings[$key] = $value;
    update_option('olsc_pan_settings', $settings, false);
}

/* ===================== 兼容原项目的返回结构 ===================== */

function olsc_ok($message = 'success', $data = null) {
    return array('code' => 200, 'message' => $message, 'data' => $data === null ? '' : $data);
}

function olsc_err($message = 'error', $code = 500) {
    return array('code' => $code, 'message' => $message);
}

/* ===================== HTTP 助手（移植 curlHelper） ===================== */

function olsc_http($url, $method = 'POST', $data = null, $header = array(), $queryParams = '', $cookies = '', $timeout = 30) {
    if (!empty($queryParams) && is_array($queryParams)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($queryParams);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    if (!empty($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    if (!empty($cookies)) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    }

    switch (strtoupper($method)) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null && $data !== '') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case 'PUT':
        case 'DELETE':
        case 'PATCH':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            if ($data !== null && $data !== '') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case 'GET':
        default:
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
    }

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return array('error' => $error, 'body' => '', 'header' => '', 'detail' => array());
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $output = array(
        'header' => substr($response, 0, $headerSize),
        'body'   => substr($response, $headerSize),
        'detail' => curl_getinfo($ch),
        'error'  => '',
    );
    curl_close($ch);
    return $output;
}

/**
 * 调试日志（默认只在 WP_DEBUG 时写）
 */
function olsc_log($message) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[olsc-pan] ' . (is_scalar($message) ? $message : wp_json_encode($message)));
    }
}

/* ===================== 初始化 ===================== */

add_action('plugins_loaded', function () {
    OLSC_Pan_Ajax::init();
    OLSC_Pan_Frontend::init();
    if (is_admin()) {
        OLSC_Pan_Admin::init();
    }
}, 20);
