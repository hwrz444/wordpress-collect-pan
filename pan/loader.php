<?php
/**
 * 核心文件加载器（加密版）
 *
 * 发布包里，下面这些文件不再以明文出现：
 *   pan/pan/*.php、class-transfer.php、class-frontend.php、class-ajax.php、
 *   includes/class-license.php
 * 它们被加密成 enc/*.enc，由本文件在运行时解密加载。
 *
 * 说明：这是「防随手拷走 / 防改」级别的保护，不是密码学意义的不可逆；
 * 真要不可读只能上 ionCube / SourceGuardian，或把核心逻辑放服务器。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 解密密钥（拆开存放，避免整串被直接搜到）
 */
function olsc_core_key() {
    static $key = null;
    if ($key === null) {
        $parts = array('ZQLaPSFV', 'skbhXtqZ', 'EntU065F', 'okv45XkHRM8k3zwC4BM=');
        $key = base64_decode(implode('', $parts));
    }
    return $key;
}

/**
 * 加载一个加密文件
 *
 * @param string $rel 相对插件根目录的路径，例如 pan/BasePan.php
 * @return bool
 */
function olsc_core_load($rel) {
    static $loaded = array();

    if (isset($loaded[$rel])) {
        return true;
    }

    $file = OLSC_PAN_DIR . 'enc/' . str_replace('/', '_', $rel) . '.enc';
    $plainFile = OLSC_PAN_DIR . $rel;

    // 源码版（没加密）优先用明文文件；发布版里明文已被删掉，会走下面的解密分支
    if (is_file($plainFile)) {
        require_once $plainFile;
        $loaded[$rel] = true;
        return true;
    }

    if (!is_file($file)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[olsc-pan] 缺少核心文件：' . $file);
        }
        return false;
    }

    $raw = trim(file_get_contents($file));
    $data = base64_decode($raw, true);
    if ($data === false || strlen($data) < 17) {
        return false;
    }

    $iv = substr($data, 0, 16);
    $cipher = substr($data, 16);

    if (!function_exists('openssl_decrypt')) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[olsc-pan] 服务器缺少 openssl 扩展，无法加载核心文件');
        }
        return false;
    }

    $code = openssl_decrypt($cipher, 'AES-256-CBC', olsc_core_key(), OPENSSL_RAW_DATA, $iv);
    if ($code === false || $code === '') {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[olsc-pan] 核心文件解密失败：' . $rel);
        }
        return false;
    }

    // 正常路径：直接 eval
    try {
        eval($code);
        $loaded[$rel] = true;
        return true;
    } catch (\Throwable $e) {
        // 兜底：极少数环境 eval 受限，改成临时文件 include
        $tmp = self_olsc_tmp_file($code);
        if ($tmp !== false) {
            include $tmp;
            @unlink($tmp);
            $loaded[$rel] = true;
            return true;
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[olsc-pan] 加载失败：' . $rel . ' - ' . $e->getMessage());
        }
        return false;
    }
}

/**
 * 兜底用：把代码写到临时文件
 */
function self_olsc_tmp_file($code) {
    $dir = function_exists('wp_upload_dir') ? wp_upload_dir() : array('basedir' => sys_get_temp_dir());
    $base = isset($dir['basedir']) ? $dir['basedir'] : sys_get_temp_dir();
    $tmp = $base . '/.olsc-' . md5(uniqid('', true)) . '.php';
    if (@file_put_contents($tmp, "<?php\n" . $code) === false) {
        return false;
    }
    return $tmp;
}
