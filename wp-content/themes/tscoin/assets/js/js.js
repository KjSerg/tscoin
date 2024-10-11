$(document).ready(function () {
    setCheckoutLang();
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
    $('.add-to-cart-button').on('click', function (e) {
        e.preventDefault(); // Зупиняємо звичайну поведінку посилання

        // Отримуємо ID продукту
        var product_id = $(this).data('product_id');

        var $i = $(this).closest('.product_order_controls').find('.quantity_input');
        var q = 1;
        if ($i.length > 0) {
            q = $i.val();
        }

        showPreloader();
        // AJAX-запит
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: 'woocommerce_ajax_add_to_cart',
                product_id: product_id,
                quantity: q,
            },
            success: function (response) {
                if (response.error & response.product_url) {
                    window.location = response.product_url;
                    return;
                }

                // Оновлення кошика і повідомлення користувача
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);


                hidePreloader();
                showMsg(addedString);
            },
            error: function (response) {
                console.log('Error adding to cart:', response);
            }
        });
    });
    $(document).on('click', '.cookies-settings__btn', function (e) {
        e.preventDefault();
        var $t = $(this);
        if ($t.hasClass('accept')) {
            setCookie('user_cookies', 'accept', 365);
        }
        $t.closest('.cookies-settings').addClass('hidden');
    });
    $('.single-news__gallery').slick({
        infinite: true,
        arrows: false,
        slidesToShow: 3,
        responsive: [
            {
                breakpoint: 769,
                settings: {

                    infinite: true,
                    arrows: false,
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 501,
                settings: {

                    infinite: true,
                    arrows: false,
                    slidesToShow: 1,
                }
            },
        ]
    });
});

// window.initAutocomplete = initAutocomplete;

function setCheckoutLang() {
    if (checkout_lang !== '') {
        setCookie('checkout_lang', checkout_lang, 1);
    }
    console.log(checkout_lang)
}

function initAutocomplete() {
    if ($('#google-map-api').length === 0) return;
    $('.address-js').each(function (index) {
        var $t = $(this);
        var id = $t.attr('id');
        if (id === null || id === undefined) {
            $t.attr('id', 'address-input-' + index);
            id = $t.attr('id');
        }
        var addressField = document.querySelector('#' + id);
        var options = {
            fields: ["formatted_address", "address_components", "geometry", "name"],
            strictBounds: false,
            types: [],
        };
        if ($t.hasClass('is-cities')) {
            options = {
                fields: ["formatted_address", "address_components", "geometry", "name"],
                strictBounds: false,
                types: ['(cities)'],
                componentRestrictions: {country: 'ua'}
            };
        }
        var autocomplete = new google.maps.places.Autocomplete(addressField, options);
        autocomplete.addListener("place_changed", function () {
            addressField.removeAttribute('data-selected');
            fillInAddress(autocomplete, addressField);
        });
    });
}

function fillInAddress(autocomplete, addressField) {
    var place = autocomplete.getPlace();
    var lat = place.geometry.location.lat();
    var lng = place.geometry.location.lng();
    var name = place.name;
    var formatted_address = place.formatted_address;
    var address1 = "";
    var postcode = "";
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
    for (const component of place.address_components) {
        const componentType = component.types[0];
        switch (componentType) {
            case "street_number": {
                address1 = component.long_name + ' ' + address1;
                break;
            }
            case "route": {
                address1 += component.short_name;
                break;
            }
            case "postal_code": {
                postcode = component.long_name;
                break;
            }
            case "postal_code_suffix": {
                postcode = postcode + '-' + component.long_name;
                break;
            }
            case "locality":
                $('#city').val(component.long_name);
                break;
            case "administrative_area_level_1": {
                $('#region').val(component.long_name);
                break;
            }
            case "country":
                $('#country').val(component.long_name);
                break;
        }
    }
    // addressField.value = formatted_address;
    // addressField.setAttribute('data-selected', formatted_address);
    console.log(formatted_address)
    $('#post_code').val(postcode);
    $('#address').val(address1);
}