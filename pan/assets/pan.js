jQuery(document).ready(function ($) {
    if (typeof OLSC_PAN === 'undefined') {
        return;
    }

    $(document).on('click', '.olsc-pan-btn', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var $box = $btn.closest('.olsc-pan');
        var $msg = $box.find('.olsc-pan-msg');
        var url = $box.data('url');

        if ($btn.data('busy')) {
            return;
        }
        $btn.data('busy', 1).prop('disabled', true);
        $msg.removeClass('ok err').text('转存中，请稍候…');

        $.ajax({
            url: OLSC_PAN.ajax,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'olsc_pan_transfer',
                nonce: OLSC_PAN.nonce,
                url: url
            },
            success: function (res) {
                if (res && res.success && res.data && res.data.url) {
                    $msg.addClass('ok').text('已生成，正在打开…');
                    window.open(res.data.url, '_blank');
                    setTimeout(function () {
                        $msg.text('');
                        $btn.data('busy', 0).prop('disabled', false);
                    }, 2500);
                } else {
                    var m = (res && res.data) ? res.data : '转存失败';
                    $msg.addClass('err').text(m);
                    $btn.data('busy', 0).prop('disabled', false);
                }
            },
            error: function () {
                $msg.addClass('err').text('网络错误，请稍后重试');
                $btn.data('busy', 0).prop('disabled', false);
            }
        });
    });
});
