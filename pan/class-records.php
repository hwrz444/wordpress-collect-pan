<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 转存记录表：同一个分享链接在有效期内复用，避免重复转存
 */
class OLSC_Pan_Records
{
    const VERSION = '1.0';

    public static function table() {
        global $wpdb;
        return $wpdb->prefix . 'olsc_pan_records';
    }

    public static function install() {
        global $wpdb;
        $table = self::table();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            src_key varchar(64) NOT NULL DEFAULT '',
            pan varchar(20) NOT NULL DEFAULT '',
            src_url text NOT NULL,
            share_url text NOT NULL,
            share_code varchar(32) NOT NULL DEFAULT '',
            title varchar(255) NOT NULL DEFAULT '',
            created_at datetime NOT NULL,
            expires_at datetime NOT NULL,
            hits int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            KEY src_key (src_key),
            KEY expires_at (expires_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        update_option('olsc_pan_db_version', self::VERSION, false);
    }

    public static function maybe_install() {
        if (get_option('olsc_pan_db_version') !== self::VERSION) {
            self::install();
        }
    }

    public static function find($src_key, $pan) {
        global $wpdb;
        $table = self::table();
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE src_key = %s AND pan = %s AND expires_at > %s ORDER BY id DESC LIMIT 1",
            $src_key,
            $pan,
            current_time('mysql')
        ));
        return $row ? $row : null;
    }

    public static function save($src_key, $pan, $src_url, $share_url, $share_code, $title) {
        global $wpdb;
        $hours = (int) olsc_cfg('cache_hours', 24);
        if ($hours <= 0) {
            $hours = 24;
        }
        $wpdb->insert(self::table(), array(
            'src_key'    => $src_key,
            'pan'        => $pan,
            'src_url'    => $src_url,
            'share_url'  => $share_url,
            'share_code' => (string) $share_code,
            'title'      => mb_substr((string) $title, 0, 200),
            'created_at' => current_time('mysql'),
            'expires_at' => date('Y-m-d H:i:s', current_time('timestamp') + $hours * 3600),
            'hits'       => 0,
        ));
        return $wpdb->insert_id;
    }

    public static function hit($id) {
        global $wpdb;
        $wpdb->query($wpdb->prepare("UPDATE " . self::table() . " SET hits = hits + 1 WHERE id = %d", $id));
    }

    public static function cleanup() {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM " . self::table() . " WHERE expires_at < %s", current_time('mysql')));
    }

    public static function recent($limit = 20) {
        global $wpdb;
        $limit = max(1, (int) $limit);
        return $wpdb->get_results("SELECT * FROM " . self::table() . " ORDER BY id DESC LIMIT {$limit}");
    }
}
