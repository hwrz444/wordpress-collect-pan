<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 后台设置页
 */
class OLSC_Pan_Admin
{
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'menu'), 30);
        add_action('admin_post_olsc_pan_save', array(__CLASS__, 'save'));
    }

    public static function menu() {
        add_submenu_page(
            'edit.php?post_type=scrape',
            '网盘转存',
            '网盘转存',
            'manage_options',
            'olsc-pan',
            array(__CLASS__, 'render')
        );
    }

    public static function save() {
        if (!current_user_can('manage_options')) {
            wp_die('没有权限');
        }
        check_admin_referer('olsc_pan_save');

        $in = isset($_POST['olsc']) && is_array($_POST['olsc']) ? wp_unslash($_POST['olsc']) : array();
        $defaults = olsc_pan_defaults();
        $out = array();

        foreach ($defaults as $key => $def) {
            if ($key === 'enabled' || substr($key, -7) === '_enable') {
                $out[$key] = empty($in[$key]) ? 0 : 1;
                continue;
            }
            if ($key === 'cache_hours') {
                $out[$key] = max(1, min(720, (int) (isset($in[$key]) ? $in[$key] : 24)));
                continue;
            }
            if (!isset($in[$key])) {
                $out[$key] = $def;
                continue;
            }
            $val = trim((string) $in[$key]);
            if (strpos($key, 'cookie') !== false || strpos($key, 'token') !== false) {
                $out[$key] = $val; // Cookie / token 原样保存
            } elseif ($key === 'banned') {
                $out[$key] = sanitize_text_field($val);
            } else {
                $out[$key] = sanitize_text_field($val);
            }
        }

        update_option('olsc_pan_settings', $out, false);

        wp_safe_redirect(add_query_arg('olsc_saved', '1', wp_get_referer() ? wp_get_referer() : admin_url('edit.php?post_type=scrape&page=olsc-pan')));
        exit;
    }

    public static function render() {
        if (!current_user_can('manage_options')) {
            return;
        }
        require OLSC_PAN_DIR . 'views/pan-settings.php';
    }
}
