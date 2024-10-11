$(document).ready(function () {
    $(document).on('click', '.account-order-head-button', function (e) {
        e.preventDefault();
        var $t = $(this);
        var $item = $t.closest('.account-order');
        var $body = $item.find('.account-order-body-container');
        var isOpened = $item.hasClass('open');
        if (isOpened) {
            $item.removeClass('open');
            $body.slideUp();
        } else {
            $item.addClass('open');
            $body.slideDown();
        }
    });
    $(document).on('click', '.password-open, .password-close', function (e) {
        e.preventDefault();
        var $t = $(this);
        var isOpen = $t.hasClass('password-open');
        var $wrapper = $t.closest('.password');
        $t.hide();
        if (isOpen) {
            $wrapper.find('.password-close').show();
            $wrapper.find('input').attr('type', 'text');
        } else {
            $wrapper.find('.password-open').show();
            $wrapper.find('input').attr('type', 'password');
        }
    });
    $('select[name="currency"]').selectric({
        disableOnMobile: false,
        nativeOnMobile: false,
        onOpen: function onOpen() {
            $(this).closest('.select_wrapper').addClass('opened');
        },
        onClose: function onClose() {
            $(this).closest('.select_wrapper').removeClass('opened');
        }
    });
    $(document).on('click', '.account-section-box__edit', function (e) {
        e.preventDefault();
        var $t = $(this);
        var $box = $t.closest('.account-section-box');
        $box.addClass('active');
    });
});

