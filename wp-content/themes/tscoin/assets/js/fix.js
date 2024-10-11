var doc = document;
var $doc = $(doc);
var load = false;
var loading = false;
var w = window;
var $w = $(w);
var parser = new DOMParser();
var $_body = $('body');
var scrollWidth = w.innerWidth - doc.body.offsetWidth;
var cart = {};

$doc.ready(function () {
    setCart();
    moveToSection();
    setDeliveryPrice();
    initAutoplay();
    $doc.on('submit', '.form-js', function (e) {
        e.preventDefault();
        var $form = $(this);
        var it = $form;
        var $thanks = $('#dialog');
        var this_form = $form.attr('id');
        var test = true,
            thsInputs = $form.find('input, textarea'),
            $select = $form.find('select[required]');
        var $checkbox = $form.find('input[type="checkbox"][required]');
        var $radio = $form.find('input[type="radio"][required]');
        $thanks.find('.modal_title').text('');
        $select.each(function () {
            var $ths = $(this);
            var $label = $ths.closest('.form_element');
            var val = $ths.val();
            if (val === null || val === undefined) {
                test = false;
                $label.addClass('error');
            } else {
                $label.removeClass('error');
            }
        });
        thsInputs.each(function () {
            var thsInput = $(this),
                $label = thsInput.closest('.form_element'),
                thsInputType = thsInput.attr('type'),
                inputName = thsInput.attr('name'),
                thsInputVal = thsInput.val().trim(),
                inputReg = new RegExp(thsInput.data('reg')),
                inputTest = inputReg.test(thsInputVal);
            if (thsInput.attr('required')) {
                if (thsInputType === 'checkbox' || thsInputType === 'radio') {
                    if ($form.find('input[name="' + inputName + '"][required]:checked').length === 0) {
                        test = false;
                        $label.addClass('error');
                    } else {
                        $label.removeClass('error');
                    }
                } else {
                    if (thsInputVal.length <= 0) {
                        test = false;
                        thsInput.addClass('error');
                        $label.addClass('error');
                        thsInput.focus();
                    } else {
                        thsInput.removeClass('error');
                        $label.removeClass('error');
                        if (thsInput.data('reg')) {
                            if (inputTest === false) {
                                test = false;
                                thsInput.addClass('error');
                                $label.addClass('error');
                                thsInput.focus();
                            } else {
                                thsInput.removeClass('error');
                                $label.removeClass('error');
                            }
                        }
                    }
                }
            }
        });
        var $inp = $form.find('input[name="consent_to_data_processing"]');
        if ($inp.length > 0) {
            if ($inp.prop('checked') === false) {
                $inp.closest('.ch_block').addClass('error');
                return;
            }
            $inp.closest('.ch_block').removeClass('error');
        }
        if (test) {
            var thisForm = document.getElementById(this_form);
            var formData = new FormData(thisForm);
            showPreloader();
            it.addClass('not-active');
            showPreloader();
            it.addClass('not-active');
            $.ajax({
                type: $form.attr('method'),
                url: $form.attr('action') || admin_ajax,
                processData: false,
                contentType: false,
                data: formData,
            }).done(function (r) {
                hidePreloader();
                $form.removeClass('not-active');
                $.fancybox.close();
                if (r) {
                    var $body = $doc.find('.cart_sides');
                    var $requestBody = $(parser.parseFromString(r, "text/html"));
                    if (isJsonString(r)) {
                        var res = JSON.parse(r);
                        if ($form.hasClass('form-checkout-package') && res.type === 'success') {
                            $.fancybox.close();
                            $.fancybox.open({
                                src: '#success',
                                touch: false,
                                baseClass: 'thanks_msg'
                            });
                        }
                        if (res.msg !== undefined && res.msg !== '') showMsg(res.msg);
                        if (res.is_reload === 'true') window.location.reload();
                        if (res.url !== undefined && res.url !== '' && res.url !== false) {
                            showPreloader();
                            setTimeout(function () {
                                windowLocation(res.url);
                            }, 1500);
                        }
                        if ($form.hasClass('finish-checkout-form')) {
                            setCookie('remove_packaging', '', 1);
                            setCookie('ts_coin_cart', '', 1);
                            setCookie('page_id', page_id, 1);
                        }
                    } else {
                        if ($form.hasClass('finish-checkout-form')) {
                            $body.html(r);
                            load = false;
                            loadingImages();
                            $_body.removeClass('loading');
                            $body.removeClass('not-active');
                            $doc.find('.cart_step').removeClass('active');
                            setCookie('remove_packaging', '', 1);
                            setCookie('ts_coin_cart', '', 1);
                            window.history.pushState({}, '', $form.attr('data-url'));
                            $doc.find('.ch_block_icon').each(function () {
                                var $t = $(this);
                                $t.addClass('checked');
                            });
                            return;
                        }
                        if ($form.hasClass('checkout-form')) {
                            $body.html($requestBody.find('.cart_sides').html());
                            load = false;
                            loadingImages();
                            $_body.removeClass('loading');
                            $body.removeClass('not-active');
                            $doc.find('.cart_step').removeClass('active');
                            $doc.find('[data-step="addresses"] .ch_block_icon').addClass('checked');
                            $doc.find('[data-step="shipping"]').addClass('active');
                            setDeliveryPrice();
                            return;
                        }
                        showMsg(r);
                    }
                    return;
                }
                $.fancybox.close();
                $.fancybox.open({
                    src: '#myThanks',
                    touch: false,
                    baseClass: 'thanks_msg'
                });
                setTimeout(function () {
                    $.fancybox.close();
                }, 3000);
                setDeliveryPrice();
            });
            return false;
        }
    });
    $doc.on('change', '.currency-input', function (e) {
        var $t = $(this);
        var $form = $t.closest('form');
        var sectionID = $t.closest('section').attr('id');
        $form.find('.section-id-input').val(sectionID);
        $form.trigger('submit')
    });
    $doc.on('change', '.filter-form input', function (e) {
        var $t = $(this);
        var $form = $t.closest('form');
        setFormData($form);
        $form.trigger('submit')
    });
    $doc.on('change', '.remove-packaging', function (e) {
        var $t = $(this);
        var val = $t.val();
        var isChecked = $t.prop('checked') === true;
        var removePackaging = [];
        var cookie = getCookie('remove_packaging');
        if (cookie) {
            removePackaging = cookie.split(',');
        }
        if (!isChecked) {
            removePackaging = removeElementFromArray(removePackaging, val);
        } else {
            removePackaging.push(val);
        }
        cookie = removePackaging.join(',');
        setCookie('remove_packaging', cookie || '', 365);
        setCartTotal();
    });
    $doc.on('submit', '.filter-form', filterForm);
    $doc.on('change', '.file', function (e) {
        var $t = $(this);
        var val = $t.val();
        var $wrapper = $t.closest('.file_field');
        var $output = $wrapper.find('.file_output');
        $output.val(val);
    });
    $doc.on('click', '.add-to-wish-list', function (e) {
        e.preventDefault()
        var $t = $(this);
        var id = $t.attr('data-id');
        var wishList = [];
        var cookie = getCookie('wish_list');
        if (cookie) {
            wishList = cookie.split(',');
        }
        if ($t.hasClass('active')) {
            $t.removeClass('active');
            wishList = removeElementFromArray(wishList, id);
        } else {
            $t.addClass('active');
            wishList.push(id);
        }
        cookie = wishList.join(',');
        setCookie('wish_list', cookie || '', 365);
    });
    $doc.on('click', '.show-modal-info', function (e) {
        e.preventDefault();
        var $t = $(this);
        var id = $t.attr('data-id');
        var img = $t.attr('data-img');
        var title = $t.attr('data-title');
        var href = $t.attr('href');
        var $modal = $doc.find(href);
        var loadedID = $modal.attr('data-id');
        scrollWidth = w.innerWidth - doc.body.offsetWidth;
        var fancyboxOptions = {
            src: href,
            touch: false,
            autoFocus: false,
            backFocus: false,
            buttons: ["close"],
            baseClass: "gal_frame",
            beforeShow: function beforeShow() {
                $('body').css('paddingRight', scrollWidth + 'px');
                $('.header').css('right', scrollWidth + 'px');

                if (window.innerWidth > 991) {
                    $('.soc_list.header_soc_list').css('right', scrollWidth + 'px');
                }
            },
            afterClose: function afterClose() {
                if ($('.gal_frame').length === 0) {
                    $('body').css('paddingRight', 0);
                    $('.header').css('right', 0);

                    if (window.innerWidth > 991) {
                        $('.soc_list.header_soc_list').css('right', 0);
                    }
                }
            }
        };
        if (loadedID !== id) {
            if (load) return;
            load = true;
            $_body.addClass('loading');
            showPreloader();
            $.ajax({
                type: 'POST',
                url: admin_ajax,
                data: {
                    action: 'get_content',
                    id: id
                },
            }).done(function (r) {
                load = false;
                loadingImages();
                $_body.removeClass('loading');
                $modal.find('.product_item_img').html('<img alt="" src="' + img + '">');
                $modal.find('.product_item_body').html('<div class="product_item_title">' + title + '</div>' + r);
                $modal.attr('data-id', id);
                $.fancybox.open(fancyboxOptions);
                hidePreloader();
            });
            return;
        }
        $.fancybox.open(fancyboxOptions);
    });
    $doc.on('click', '.container-product-pagination a', function (e) {
        e.preventDefault();
        showPreloader();
        var $t = $(this);
        var href = $t.attr('href');
        var id = $t.closest('.container-js').attr('id');
        if (id !== undefined) href = href + '#' + id;
        window.location.href = href;
    });
    $doc.on('click', '.add-to-cart', function (e) {
        e.preventDefault();
        var $t = $(this);
        var id = $t.attr('data-id');
        var $wrapper = $t.closest('.product__controls_side');
        var $input = $wrapper.find('.quantity_input');
        var qnt = $input.val() || 1;
        qnt = Number(qnt);
        var old = qnt;
        var max = Number($input.attr('data-max'));
        var reserved = Number($input.attr('data-reserved'));
        var available = Number($input.attr('data-available'));
        console.log(qnt);
        console.log(available);
        if (qnt > available) qnt = available;
        available = available - qnt;
        console.log(qnt);
        console.log(available);
        $input.attr('data-available', available);
        if (cart[id] === undefined) {
            cart[id] = {qnt: qnt};
        } else {
            var q = cart[id].qnt + qnt;
            cart[id].qnt = q;
        }
        setCookie('ts_coin_cart', JSON.stringify(cart), 100);
        localStorage.setItem('ts_coin_cart', JSON.stringify(cart));
        setCartTotal();
        setCartCron();
        showMsg(addedString);
        if (available === 0) {
            $doc.find('.product_order_controls').remove();
        }
    });
    $('.select_lang').on('change', function () {
        location.href = this.value;
    });
    $doc.on('click', '.product_btn.fancybox', function () {
        var $t = $(this);
        var id = $t.attr('data-id');
        $doc.find('input[name="product"]').val(id);
    });
    $doc.on('click', '.cart_block_remove', function (e) {
        e.preventDefault();

        var $t = $(this);
        $t.closest('.cart_block').addClass('not-active');
        var ids = $t.attr('data-ids');
        var id = $t.attr('data-id');
        if (ids !== undefined) {
            ids = ids.split(';');
            ids.forEach(function (element) {
                if (element !== 0 && element !== '') delete cart[element];
            });
        } else {
            delete cart[id];
        }
        setCookie('ts_coin_cart', JSON.stringify(cart), 100);
        localStorage.setItem('ts_coin_cart', JSON.stringify(cart));
        renderCart();
    });
    $doc.on('click', ".minus_btn", function (e) {
        e.preventDefault();
        var $t = $(this);
        var inp = $(this).parent(".quantity_wrapper").find(".quantity_input"),
            inpVal = parseFloat(inp.val());

        if (inpVal < 2) {
            inpVal = 2;
        }
        var v = inpVal - 1;
        inp.val(v);
    });
    $doc.on('click', ".plus_btn", function (e) {
        e.preventDefault();
        var $t = $(this);
        var inp = $(this).parent(".quantity_wrapper").find(".quantity_input"),
            inpVal = parseFloat(inp.val());
        var max = Number(inp.attr('data-max'));
        var reserved = Number(inp.attr('data-reserved'));
        var available = Number(inp.attr('data-available'));
        var v = inpVal + 1;
        if (v > available) v = available;
        inp.val(v);
    });
    $doc.on('change', '.cart_block_check input', function () {
        var th = $(this);
        var ids = th.attr('data-ids');
        var id = th.attr('data-id');
        if (th.is(':checked')) {
            th.closest('.cart_block').removeClass('unactive');
            if (ids !== undefined) {
                ids = ids.split(';');
                ids.forEach(function (element) {
                    if (element && element !== 0 && element !== '' && cart[element] !== undefined) cart[element].is_active = 'true';
                });
            } else {
                if (cart[id] !== undefined) {
                    cart[id].is_active = 'true';
                }
            }
        } else {
            th.closest('.cart_block').addClass('unactive');
            if (ids !== undefined) {
                ids = ids.split(';');
                ids.forEach(function (element) {
                    if (element && element !== 0 && element !== '' && cart[element] !== undefined) cart[element].is_active = 'false';
                });
            } else {
                if (cart[id] !== undefined) {
                    cart[id].is_active = 'false';
                }
            }
        }
        setCookie('ts_coin_cart', JSON.stringify(cart), 100);
        localStorage.setItem('ts_coin_cart', JSON.stringify(cart));
        renderCart();
    });
    $doc.on('change', '.switch-input', function () {
        var $th = $(this);
        var selector = $th.val();
        var $item = $(selector);
        if ($item.length === 0) return;
        if ($th.is(':checked')) {
            $item.slideDown(500);
            $item.find('input').attr('required', 'required');
        } else {
            $item.slideUp(500);
            $item.find('input').removeAttr('required');
        }
    });
    $doc.on('change', 'input[name="delivery_method"]', setDeliveryPrice);
    $doc.on('submit', '.shipment-form-js', function (e) {
        e.preventDefault();
        var $form = $(this);
        var this_form = $form.attr('id');
        var it = $form;
        var $input = $form.find('input[name="delivery_method"]:checked');
        if ($input.length === 0) {
            $form.find('.ch_block').addClass('error');
            return;
        }
        var thisForm = document.getElementById(this_form);
        var formData = new FormData(thisForm);
        showPreloader();
        it.addClass('not-active');
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action') || admin_ajax,
            processData: false,
            contentType: false,
            data: formData,
        }).done(function (r) {
            hidePreloader();
            if (r) {
                var $body = $doc.find('.cart_sides');
                var $requestBody = $(parser.parseFromString(r, "text/html"));
                $body.html($requestBody.find('.cart_sides').html());
                load = false;
                loadingImages();
                $_body.removeClass('loading');
                $body.removeClass('not-active');
                $doc.find('.cart_step').removeClass('active');
                $doc.find('[data-step="shipping"] .ch_block_icon').addClass('checked');
                $doc.find('[data-step="payment"]').addClass('active');
            }
            setDeliveryPrice();
        });
    });
    $doc.on('submit', '.payments-form-js', function (e) {
        e.preventDefault();
        var $form = $(this);
        var it = $form;
        var this_form = $form.attr('id');
        var $input = $form.find('input[name="payment_method"]:checked');
        if ($input.length === 0) {
            $form.find('.ch_block').addClass('error');
            return;
        }
        var thisForm = document.getElementById(this_form);
        var formData = new FormData(thisForm);
        showPreloader();
        it.addClass('not-active');
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action') || admin_ajax,
            processData: false,
            contentType: false,
            data: formData,
        }).done(function (r) {
            hidePreloader();
            if (r) {
                var $body = $doc.find('.cart-container-js');
                var $requestBody = $(parser.parseFromString(r, "text/html"));
                $body.html($requestBody.find('.cart-container-js').html());
                load = false;
                loadingImages();
                $_body.removeClass('loading');
                $body.removeClass('not-active');
                $doc.find('.cart_step').removeClass('active');
                $doc.find('[data-step="payment"] .ch_block_icon').addClass('checked');
                $doc.find('[data-step="complete"]').addClass('active');
            }
        });
    });
    $doc.on('click', '.prev-step-js', function (e) {
        e.preventDefault();
        var $t = $(this);
        var $form = $t.closest('.payments-form-js');
        var it = $form;
        var this_form = $form.attr('id');
        $form.find('[name="step"]').val($t.attr('data-step'));
        var thisForm = document.getElementById(this_form);
        var formData = new FormData(thisForm);
        showPreloader();
        it.addClass('not-active');
        $.ajax({
            type: 'POST',
            url: $form.attr('action') || admin_ajax,
            processData: false,
            contentType: false,
            data: formData,
        }).done(function (r) {
            hidePreloader();
            if (r) {
                var $body = $doc.find('.cart-container-js');
                var $requestBody = $(parser.parseFromString(r, "text/html"));
                $body.html($requestBody.find('.cart-container-js').html());
                load = false;
                loadingImages();
                $_body.removeClass('loading');
                $body.removeClass('not-active');
                $doc.find('.cart_step').removeClass('active');
            }
            setDeliveryPrice();
        });
    });
});

function triggerSlider() {
    var $sliders = $doc.find('.with_autoplay_trigger');
    if ($sliders) {
        $sliders.each(function () {
            var $slider = $(this);
            if ($slider.hasClass('slick-slider')) {
                $slider.slick('slickNext');
            }
        });
    }
}

function initAutoplay() {
    setInterval(triggerSlider, 6000);
}

function setCartTotal() {
    $.ajax({
        type: 'POST',
        url: admin_ajax,
        data: {
            action: 'get_cart_total'
        }
    }).done(function (r) {
        $doc.find('.count-cart-sum').html(r);
    });
}

function setCartCron() {
    $.ajax({
        type: 'POST',
        url: admin_ajax,
        data: {
            action: 'set_cart_cron'
        }
    }).done(function (r) {

    });
}

function setDeliveryPrice() {
    var $th = $doc.find('input[name="delivery_method"]:checked');
    var val = $th.attr('data-price');
    $doc.find('.delivery-price-js').val(val);
    console.log($th);
    console.log(val);
    console.log($doc.find('.delivery-price-js'));
}

function showMassage(message) {
    var scrollWidth = window.innerWidth - document.body.offsetWidth;
    $('#dialog .modal_title').text(message);
    $.fancybox.open({
        src: '#dialog',
        touch: false,
        baseClass: 'thanks_msg',
        beforeShow: function beforeShow() {
            $('body').css('paddingRight', scrollWidth + 'px');
            $('.header').css('right', scrollWidth + 'px');
        },
        afterClose: function afterClose() {
            if ($('.gal_frame').length === 0) {
                $('body').css('paddingRight', 0);
                $('.header').css('right', 0);
            }
        }
    });
    setTimeout(function () {
        $.fancybox.close();
    }, 3000);
}

function windowLocation(url) {
    window.location.href = url;
}

function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function showMsg(msg) {
    $('#myThanks .thanks_title').html(msg);
    $.fancybox.open({
        src: '#myThanks',
        touch: false,
        baseClass: 'thanks_msg'
    });
    setTimeout(function () {
        $.fancybox.close();
    }, 3000);
}

function isValidPassword(password) {
    var regexp = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
    return password.match(regexp);
}

function renderCart() {
    if (load) return;
    load = true;
    $_body.addClass('loading');
    var $body = $doc.find('.cart-container-js');
    $body.addClass('not-active');
    showPreloader();
    $.ajax({
        type: 'GET',
        url: '',
    }).done(function (r) {
        hidePreloader();
        var $requestBody = $(parser.parseFromString(r, "text/html"));
        $body.html($requestBody.find('.cart-container-js').html());
        $doc.find('.count-cart-sum').html($requestBody.find('.count-cart-sum').html());
        load = false;
        loadingImages();
        $_body.removeClass('loading');
        $body.removeClass('not-active');
    });
}

function setCart() {
    var cookie = getCookie('ts_coin_cart') || '{}';
    if (cookie) {
        cart = JSON.parse(cookie);
    }
}

function removeElementFromArray(array, item) {
    var index = array.indexOf(item);
    if (index !== -1) {
        array.splice(index, 1);
    }
    return array;
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000); // ) removed
        expires = "; expires=" + date.toGMTString(); // + added
    }
    document.cookie = name + "=" + value + expires + ";path=/"; // + and " added
}

function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function moveToSection() {
    var windowLocation = window.location;
    var get = windowLocation.search;
    if (get) {
        var url = new URL(windowLocation);
        var sectionID = url.searchParams.get('sectionID');
        if (sectionID !== undefined && sectionID !== '') {
            var $section = $('#' + sectionID);
            if ($section.length === 0) return;
            $('html, body').animate({
                scrollTop: $section.offset().top
            })
        }
    }
}

function filterForm(e) {
    e.preventDefault();
    if (load) return;
    load = true;
    $_body.addClass('loading');
    var $form = $(this);
    var $section = $form.closest('section');
    var $body = $section.find('.container-js');
    showPreloader();
    var action = $form.attr('action');
    var serialize = $form.serialize();
    $form.addClass('not-active');
    $.ajax({
        type: $form.attr('method'),
        url: $form.attr('action'),
        data: $form.serialize(),
    }).done(function (r) {
        hidePreloader();
        var $requestBody = $(parser.parseFromString(r, "text/html"));
        $body.html($requestBody.find('.container-js').html());
        $doc.find('title').text($requestBody.find('title').text());
        load = false;
        loadingImages();
        window.history.pushState({}, '', action + '?' + serialize);
        $form.removeClass('not-active');
        $_body.removeClass('loading');
    });
}

function loadingImages() {
    var observer = lozad('.lozad:not(.lozad_showed)', {
        loaded: function loaded(el) {
            $(el).addClass('lozad_showed');
            if ($(el).hasClass('svg')) {
                $(el).toSVG({
                    svgClass: " svg_converted",
                    onComplete: function onComplete(data) {
                    }
                });
            }
        }
    });
    observer.observe();
}

function showPreloader() {
    $('.preloader').addClass('active');
}

function hidePreloader() {
    $('.preloader').removeClass('active')
}

function setFormData($form) {
    var $inputs = $form.find('input');
    var $section = $form.closest('section');
    var data = {};
    $inputs.each(function (index) {
        var $t = $(this);
        var name = $t.attr('data-name');
        var val = $t.val();
        var isChecked = $t.prop('checked') === true;
        if (isChecked) {
            if (data[name] === undefined) {
                data[name] = [val];
            } else {
                data[name].push(val);
            }
        }
    });
    var html = '';
    if (data) {
        for (var name in data) {
            var item = data[name];
            var elements = item.join();
            html += '<input type="hidden" name="' + name + '" value="' + elements + '">';
        }
    }
    $section.find('.filter-form-box').html(html);
}

function c(value) {
    console.log(value);
}

document.addEventListener('wpcf7mailsent', function (event) {
    $.fancybox.close();
    $('#myThanks .thanks_title').text(event.detail.apiResponse.message);
    $.fancybox.open({
        src: '#myThanks',
        touch: false,
        baseClass: 'thanks_msg'
    });
    $doc.find('input').removeClass('error');
    $doc.find('.ch_block_icon').removeClass('active');
    setTimeout(function () {
        $.fancybox.close();
    }, 3000);
}, false);

document.addEventListener('wpcf7invalid', function (event) {
    var invalid_fields = event.detail.apiResponse.invalid_fields;
    for (var a = 0; a < invalid_fields.length; a++) {
        var id = invalid_fields[a].error_id;
        $doc.find('input[aria-describedby="' + id + '"]').addClass('error');
    }
}, false);