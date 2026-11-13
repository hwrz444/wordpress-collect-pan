<?php
if (!defined('ABSPATH')) {
    exit;
}

$s = olsc_pan_settings();
$types = OLSC_Pan_Transfer::types();
$saved = isset($_GET['olsc_saved']);

$docs = array(
    'quark' => array(
        'title' => '夸克网盘',
        'steps' => array(
            '浏览器打开 <a href="https://pan.quark.cn" target="_blank">pan.quark.cn</a> 并登录（手机号/扫码都行）。',
            '按 F12 打开开发者工具 → 切到「网络 / Network」→ 刷新页面。',
            '在请求列表里点任意一条 <code>drive-pc.quark.cn</code> 的请求，右侧「标头 / Headers」→「请求标头」。',
            '找到 <code>Cookie:</code> 这一行，把冒号后面整段复制下来（要包含 <code>__pus</code>、<code>__puus</code>、<code>b-user-id</code>）。',
            '粘贴到下面输入框，保存。',
        ),
        'folder' => '转存目录：点「读取目录」→ 选一个文件夹，自动填 fid；也可以手动粘贴 fid。留空则转存到根目录。',
    ),
    'uc' => array(
        'title' => 'UC 网盘',
        'steps' => array(
            '打开 <a href="https://drive.uc.cn" target="_blank">drive.uc.cn</a> 登录。',
            'F12 →「网络 / Network」→ 刷新，点任意 <code>pc-api.uc.cn</code> 请求。',
            '复制「请求标头」里的整行 <code>Cookie:</code>。',
            '粘贴保存。',
        ),
        'folder' => '转存目录：点「读取目录」选文件夹，或手填 fid。',
    ),
    'baidu' => array(
        'title' => '百度网盘',
        'steps' => array(
            '打开 <a href="https://pan.baidu.com" target="_blank">pan.baidu.com</a> 登录。',
            'F12 →「网络 / Network」→ 刷新，点任意一条 <code>pan.baidu.com</code> 请求。',
            '复制「请求标头」里完整的 <code>Cookie:</code>（必须包含 <code>BDUSS</code>、<code>STOKEN</code> 两项）。',
            '粘贴保存。Cookie 一般能撑几个月，失效后重新复制一次即可。',
        ),
        'folder' => '转存目录：填网盘里的路径，例如 <code>/采集转存</code>。目录不存在会自动创建。',
    ),
    'xunlei' => array(
        'title' => '迅雷云盘',
        'steps' => array(
            '迅雷这里要的不是 Cookie，而是 <code>refresh_token</code>。',
            '打开 <a href="https://pan.xunlei.com" target="_blank">pan.xunlei.com</a> 登录。',
            'F12 →「应用 / Application」→「本地存储 / Local Storage」→ 选 <code>https://pan.xunlei.com</code>。',
            '在左边列表里找 <code>credentials_</code> 开头的那一条（例如 <code>credentials_Xqp0kJBXWhwaTpB6</code>），点它。',
            '右侧/下方面板是一段 JSON，里面找 <code>refresh_token</code>，值形如 <code>al.q5rFZ6zs…</code>。',
            '只复制 <code>al.</code> 开头那一整串（不要带引号、不要带 <code>refresh_token:</code>）。',
            '粘贴保存。插件会自动刷新并把新的 refresh_token 写回这里。',
        ),
        'folder' => '转存目录：点「读取目录」选文件夹，或手填目录 id。',
    ),
    'ali' => array(
        'title' => '阿里云盘',
        'steps' => array(
            '阿里这里要的也是 <code>refresh_token</code>。',
            '打开 <a href="https://www.alipan.com" target="_blank">www.alipan.com</a> 登录。',
            'F12 →「应用 / Application」→「本地存储 / Local Storage」→ 选 <code>https://www.alipan.com</code>。',
            '找 <code>token</code> 这一项，里面是 JSON，取 <code>refresh_token</code> 的值。',
            '粘贴保存。',
        ),
        'folder' => '转存目录：填 file_id，默认 <code>root</code>（根目录）。',
    ),
);

$nonce = wp_create_nonce('olsc_pan_admin');
?>
<style>
    .olsc-wrap { max-width: 1180px; }
    .olsc-card { background: #fff; border: 1px solid #dcdcde; border-radius: 6px; margin: 16px 0; }
    .olsc-card > h2 { margin: 0; padding: 14px 18px; border-bottom: 1px solid #f0f0f1; font-size: 15px; display: flex; align-items: center; gap: 10px; }
    .olsc-card > .olsc-body { padding: 16px 18px; }
    .olsc-badge { font-size: 12px; padding: 2px 8px; border-radius: 10px; background: #f0f0f1; color: #666; font-weight: normal; }
    .olsc-badge.ok { background: #d1f4e0; color: #00733b; }
    .olsc-badge.no { background: #fde2e1; color: #b32d2e; }
    .olsc-row { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px; }
    .olsc-row > label { width: 110px; flex: none; padding-top: 6px; font-weight: 600; }
    .olsc-row > .olsc-field { flex: 1; }
    .olsc-row textarea { width: 100%; min-height: 90px; font-family: Consolas, Monaco, monospace; font-size: 12px; }
    .olsc-row input[type=text], .olsc-row input[type=number] { width: 100%; }
    .olsc-tip { color: #666; font-size: 12px; margin-top: 6px; line-height: 1.7; }
    .olsc-tip ol { margin: 6px 0 0 18px; }
    .olsc-dirs { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px; }
    .olsc-dirs button { font-size: 12px; }
    .olsc-test { margin-top: 10px; padding: 12px; background: #f6f7f7; border-radius: 4px; }
    .olsc-test input[type=text] { width: 46%; margin-right: 6px; }
    .olsc-result { margin-top: 8px; font-size: 13px; white-space: pre-wrap; word-break: break-all; }
    .olsc-result.ok { color: #00733b; }
    .olsc-result.err { color: #b32d2e; }
    .olsc-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .olsc-table th, .olsc-table td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #f0f0f1; word-break: break-all; }
</style>

<div class="wrap olsc-wrap">
    <h1>网盘转存设置</h1>
    <p>配置好 Cookie / Token 后，文章里的网盘链接会自动变成「转存按钮」，访客点一下就能拿到你账号转存后的新分享链接。</p>

    <?php if ($saved) : ?>
        <div class="notice notice-success is-dismissible"><p>设置已保存。</p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="olsc_pan_save">
        <?php wp_nonce_field('olsc_pan_save'); ?>

        <div class="olsc-card">
            <h2>通用设置</h2>
            <div class="olsc-body">
                <div class="olsc-row">
                    <label>总开关</label>
                    <div class="olsc-field">
                        <label><input type="checkbox" name="olsc[enabled]" value="1" <?php checked((int) $s['enabled'], 1); ?>> 启用「网盘链接 → 转存按钮」</label>
                        <div class="olsc-tip">关掉后文章里仍然是原来的网盘链接文本，不做任何替换。</div>
                    </div>
                </div>
                <div class="olsc-row">
                    <label>缓存时长</label>
                    <div class="olsc-field">
                        <input type="number" name="olsc[cache_hours]" value="<?php echo esc_attr($s['cache_hours']); ?>" min="1" max="720" style="width:120px">
                        <span class="olsc-tip" style="display:inline">小时。同一个分享链接在这个时间内重复点击，直接复用上次的结果，不会重复转存。</span>
                    </div>
                </div>
                <div class="olsc-row">
                    <label>按钮文字前缀</label>
                    <div class="olsc-field">
                        <input type="text" name="olsc[button_prefix]" value="<?php echo esc_attr($s['button_prefix']); ?>" style="width:160px">
                        <span class="olsc-tip" style="display:inline">默认「📦 」，会拼成「📦 夸克网盘」。</span>
                    </div>
                </div>
                <div class="olsc-row">
                    <label>广告过滤词</label>
                    <div class="olsc-field">
                        <input type="text" name="olsc[banned]" value="<?php echo esc_attr($s['banned']); ?>" placeholder="更多资源,关注公众号,微信,QQ群">
                        <div class="olsc-tip">逗号分隔。转存后文件名包含这些词的会被删掉（留空则不删）。</div>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($types as $key => $type) :
            $doc = $docs[$key];
            $cookieKey = $type['cookie'];
            $folderKey = $type['folder'];
            $enableKey = $type['enable'];
            $ready = trim((string) $s[$cookieKey]) !== '';
        ?>
        <div class="olsc-card">
            <h2>
                <?php echo esc_html($doc['title']); ?>
                <span class="olsc-badge <?php echo $ready ? 'ok' : 'no'; ?>"><?php echo $ready ? '已填写凭据' : '未填写'; ?></span>
                <label style="margin-left:auto;font-weight:normal;font-size:13px;">
                    <input type="checkbox" name="olsc[<?php echo esc_attr($enableKey); ?>]" value="1" <?php checked((int) $s[$enableKey], 1); ?>>
                    启用
                </label>
            </h2>
            <div class="olsc-body">
                <div class="olsc-row">
                    <label><?php echo ($key === 'xunlei' || $key === 'ali') ? 'Refresh Token' : 'Cookie'; ?></label>
                    <div class="olsc-field">
                        <textarea name="olsc[<?php echo esc_attr($cookieKey); ?>]" placeholder="粘贴整段 <?php echo ($key === 'xunlei' || $key === 'ali') ? 'refresh_token' : 'Cookie'; ?>"><?php echo esc_textarea($s[$cookieKey]); ?></textarea>
                        <div class="olsc-tip">
                            <b>获取方法：</b>
                            <ol>
                                <?php foreach ($doc['steps'] as $step) : ?>
                                    <li><?php echo wp_kses_post($step); ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="olsc-row">
                    <label>转存目录</label>
                    <div class="olsc-field">
                        <input type="text" id="olsc-folder-<?php echo esc_attr($key); ?>" name="olsc[<?php echo esc_attr($folderKey); ?>]" value="<?php echo esc_attr($s[$folderKey]); ?>">
                        <div class="olsc-dirs">
                            <button type="button" class="button olsc-read-dirs" data-pan="<?php echo esc_attr($key); ?>" data-target="olsc-folder-<?php echo esc_attr($key); ?>">读取目录</button>
                            <span class="olsc-tip" id="olsc-dirs-<?php echo esc_attr($key); ?>"></span>
                        </div>
                        <div class="olsc-tip"><?php echo wp_kses_post($doc['folder']); ?></div>
                    </div>
                </div>
                <div class="olsc-test">
                    <b>测试转存</b>
                    <div style="margin-top:8px;">
                        <input type="text" class="olsc-test-url" placeholder="粘贴一个<?php echo esc_html($doc['title']); ?>分享链接，例如 https://pan.quark.cn/s/xxxx">
                        <input type="text" class="olsc-test-code" placeholder="提取码（可空）" style="width:110px">
                        <button type="button" class="button button-primary olsc-test-btn" data-pan="<?php echo esc_attr($key); ?>">开始测试</button>
                    </div>
                    <div class="olsc-result" id="olsc-result-<?php echo esc_attr($key); ?>"></div>
                    <div class="olsc-tip">测试会真的往你网盘里转存一份并生成分享链接，成功后链接会显示在这里。</div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <p><button type="submit" class="button button-primary button-large">保存设置</button></p>
    </form>

    <?php $recent = OLSC_Pan_Records::recent(10); ?>
    <div class="olsc-card">
        <h2>最近转存记录</h2>
        <div class="olsc-body">
            <?php if (empty($recent)) : ?>
                <p class="olsc-tip">还没有记录。</p>
            <?php else : ?>
                <table class="olsc-table">
                    <thead><tr><th>时间</th><th>网盘</th><th>原链接</th><th>新分享链接</th><th>点击</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent as $r) : ?>
                        <tr>
                            <td><?php echo esc_html($r->created_at); ?></td>
                            <td><?php echo esc_html($r->pan); ?></td>
                            <td><?php echo esc_html($r->src_url); ?></td>
                            <td><?php echo esc_html($r->share_url); ?></td>
                            <td><?php echo (int) $r->hits; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    var ajax = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
    var nonce = <?php echo wp_json_encode($nonce); ?>;

    function post(data) {
        data.nonce = nonce;
        data.action = 'olsc_pan_admin';
        return fetch(ajax, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams(data)
        }).then(function (r) { return r.json(); });
    }

    document.querySelectorAll('.olsc-read-dirs').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var pan = btn.dataset.pan;
            var target = document.getElementById(btn.dataset.target);
            var tip = document.getElementById('olsc-dirs-' + pan);
            tip.textContent = '读取中…';
            post({ op: 'list_dirs', pan: pan }).then(function (res) {
                tip.textContent = '';
                if (!res.success) {
                    tip.textContent = res.data || '读取失败';
                    return;
                }
                if (!res.data.length) {
                    tip.textContent = '这个账号目录为空';
                    return;
                }
                var box = document.createElement('div');
                box.className = 'olsc-dirs';
                res.data.forEach(function (item) {
                    var b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'button';
                    b.textContent = item.name;
                    b.title = item.id;
                    b.addEventListener('click', function () {
                        target.value = item.id;
                    });
                    box.appendChild(b);
                });
                tip.appendChild(box);
            });
        });
    });

    document.querySelectorAll('.olsc-test-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var pan = btn.dataset.pan;
            var card = btn.closest('.olsc-test');
            var url = card.querySelector('.olsc-test-url').value.trim();
            var code = card.querySelector('.olsc-test-code').value.trim();
            var out = document.getElementById('olsc-result-' + pan);
            if (!url) { out.className = 'olsc-result err'; out.textContent = '请先填分享链接'; return; }
            out.className = 'olsc-result';
            out.textContent = '转存中，可能要 10~30 秒…';
            btn.disabled = true;
            post({ op: 'test_transfer', url: url, code: code }).then(function (res) {
                btn.disabled = false;
                if (res.success) {
                    out.className = 'olsc-result ok';
                    out.textContent = '转存成功！\n新分享链接：' + res.data.url + (res.data.code ? '\n提取码：' + res.data.code : '');
                } else {
                    out.className = 'olsc-result err';
                    out.textContent = '失败：' + (res.data || '未知错误');
                }
            }).catch(function () {
                btn.disabled = false;
                out.className = 'olsc-result err';
                out.textContent = '请求出错';
            });
        });
    });
})();
</script>
