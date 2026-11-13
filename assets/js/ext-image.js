jQuery(document).ready(function ($) {


    // for all images at single product page
    setTimeout(function () {
        resizeImgExt($);
        jQuery('a.woocommerce-product-gallery__trigger').css('visibility', 'visible');
    }, 2500);

});



function resizeImgExt($) {
    var imgSelector = ".post img, .page img, .widget-content img, .product img, .wp-admin img";
    var resizeImg = function (sSel) {
        jQuery(sSel).each(function () {
            //original size
            var width = $(this)['0'].naturalWidth;
            var height = $(this)['0'].naturalHeight;

            //100%
            var src = jQuery(this)['0'].src;
            if (src && (src.includes('staticflickr.com') || src.includes('storage.googleapis.com/fifu'))) {
                var ratio = width / height;
                jQuery(this).attr('data-large_image_width', jQuery(window).width() * ratio);
                jQuery(this).attr('data-large_image_height', jQuery(window).width());
            } else {
                jQuery(this).attr('data-large_image_width', width);
                jQuery(this).attr('data-large_image_height', height);
            }
        });
    };
    resizeImg(imgSelector);
}