"use strict";

//svg
(function ($) {
  $.fn.toSVG = function (options) {
    var params = $.extend({
      svgClass: "replaced-svg",
      onComplete: function onComplete() {}
    }, options);
    this.each(function () {
      var $img = jQuery(this);
      var imgID = $img.attr('id');
      var imgClass = $img.attr('class');
      var imgURL = $img.attr('src');

      if (!/\.(svg)$/i.test(imgURL)) {
        return;
      }

      $.get(imgURL, function (data) {
        var $svg = jQuery(data).find('svg');

        if (typeof imgID !== 'undefined') {
          $svg = $svg.attr('id', imgID);
        }

        if (typeof imgClass !== 'undefined') {
          $svg = $svg.attr('class', imgClass + params.svgClass);
        }

        $svg = $svg.removeAttr('xmlns:a');

        if (!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
          $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'));
        }

        $img.replaceWith($svg);
        typeof params.onComplete == "function" ? params.onComplete.call(this, $svg) : '';
      });
    });
  };
})(jQuery);

$('img.svg').toSVG({
  svgClass: " svg_converted",
  onComplete: function onComplete(data) {}
});
$(document).ready(function () {
  var bw = window.innerWidth;
  var bh = window.innerHeight; //E-mail Ajax Send

  $(".form_send").each(function () {
    var it = $(this);
    it.validate({
      rules: {
        phone: {
          required: true
        }
      },
      messages: {},
      errorPlacement: function errorPlacement(error, element) {},
      submitHandler: function submitHandler(form) {
        var thisForm = $(form); // if (thisForm.find('.mfv_checker input').is(':checked')) {

        $.ajax({
          type: "POST",
          url: thisForm.attr("action"),
          data: thisForm.serialize()
        }).done(function () {
          $.fancybox.close();
          $.fancybox.open({
            src: '#myThanks',
            touch: false,
            baseClass: 'thanks_msg'
          });
          setTimeout(function () {
            $.fancybox.close();
          }, 3000); // $('.mfv_checker').removeClass('error');
          // $(".select_wrapper").removeClass('changed');

          it.trigger("reset");
        });
        return false; // } else {
        //     thisForm.find('.mfv_checker').addClass('error');
        // }
      },
      success: function success() {},
      highlight: function highlight(element, errorClass) {
        $(element).addClass('error');
      },
      unhighlight: function unhighlight(element, errorClass, validClass) {
        $(element).removeClass('error');
      }
    });
  }); // scroll to element

  function scrollToElement(target) {
    var targetTop = target.offset().top;
    $('body,html').animate({
      scrollTop: targetTop - $(".header").outerHeight()
    }, 700);
    event.preventDefault();
  }

  $(document).on('click', ".scroll_btn", function (event) {
    event.preventDefault();
    var th = $(this);
    scrollToElement($(th.attr('href')));
  }); //  fixed menu

  var header = $(".header");
  var loadCurrentScrollTop = $(document).scrollTop();

  if (loadCurrentScrollTop > 54) {
    header.addClass("moved");
  }

  $(window).scroll(function () {
    var currentScrollTop = $(document).scrollTop();

    if (currentScrollTop < 55) {
      header.removeClass("moved");
    } else {
      header.addClass("moved");
    }
  }); //masked

  $('input[type=tel]').mask("+9(999) 999-99-99"); // fancybox

  var scrollWidth = window.innerWidth - document.body.offsetWidth;
  $(".fancybox").fancybox({
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
      if ($('.gal_frame').length == 0) {
        $('body').css('paddingRight', 0);
        $('.header').css('right', 0);

        if (window.innerWidth > 991) {
          $('.soc_list.header_soc_list').css('right', 0);
        }
      }
    }
  });
  $(".fancybox_gal").fancybox({
    touch: false,
    autoFocus: false,
    backFocus: false,
    buttons: ["zoom", "close"],
    baseClass: "galery_frame",
    beforeShow: function beforeShow() {
      $('body').css('paddingRight', scrollWidth + 'px');
      $('.header').css('right', scrollWidth + 'px');

      if (window.innerWidth > 991) {
        $('.soc_list.header_soc_list').css('right', scrollWidth + 'px');
      }
    },
    afterClose: function afterClose() {
      if ($('.galery_frame').length == 0) {
        $('body').css('paddingRight', 0);
        $('.header').css('right', 0);

        if (window.innerWidth > 991) {
          $('.soc_list.header_soc_list').css('right', 0);
        }
      }
    }
  });
  $(".modal_close").click(function (event) {
    $.fancybox.close();
  }); // slick

  $('.bg_frame_slider').on('init', function (event, slick) {
    var ths = $(this);
    setTimeout(function () {
      ths.find('.first_animated_slide').removeClass('first_animated_slide');
    }, 100);
  });
  $('.product_description_slider').slick({
    infinite: true,
    dots: false,
    arrows: false,
    slidesToShow: 1,
    autoplay: false,
    slidesToScroll: 1,
    fade: true,
    swipe: false,
    draggable: false,
    responsive: [{
      breakpoint: 420,
      settings: {
        dots: true,
        swipe: true,
        draggable: true
      }
    }]
  });
  $('.product_thumb_slider').slick({
    infinite: true,
    dots: false,
    arrows: false,
    slidesToShow: 4,
    autoplay: false,
    slidesToScroll: 1,
    swipeToSlide: true,
    focusOnSelect: true,
    asNavFor: '.product_description_slider',
    responsive: [{
      breakpoint: 1199,
      settings: {}
    }]
  });
  $('.bg_frame_slider').slick({
    infinite: true,
    dots: false,
    arrows: false,
    slidesToShow: 1,
    autoplay: false,
    slidesToScroll: 1,
    fade: true,
    swipe: false,
    draggable: false,
    responsive: [{
      breakpoint: 1199,
      settings: {}
    }]
  });
  $('.head_coins_slider').slick({
    infinite: true,
    dots: true,
    arrows: false,
    slidesToShow: 1,
    autoplay: true,
    autoplaySpeed: 6000,
    slidesToScroll: 1,
    fade: true,
    adaptiveHeight: true,
    asNavFor: '.bg_frame_slider',
    customPaging: function customPaging(slider, i) {
      var slideTitle = $(slider.$slides[i]).find('.head_coins_slide').attr('data-name');
      return '<a href="#" class="slick_custom_dot"><span class="dot_title">' + slideTitle + '</span></a>';
    },
    appendDots: '#head_coins_slider_dots',
    responsive: [{
      breakpoint: 1199,
      settings: {}
    }]
  }); // $(".slider").on('beforeChange', function(event, slick, currentSlide) {
  //     // do something
  // });

  $('.slider_controls__js').on('click', '.slider_control', function () {
    var th = $(this),
        trgt = $(th.attr('data-target'));

    if (th.hasClass('prev')) {
      trgt.slick('slickPrev');
    } else {
      trgt.slick('slickNext');
    }
  }); // select 

  $('.select').selectric({
    disableOnMobile: false,
    nativeOnMobile: false,
    // maxHeight: 150,
    onOpen: function onOpen() {
      $(this).closest('.select_wrapper').addClass('opened');
    },
    onClose: function onClose() {
      $(this).closest('.select_wrapper').removeClass('opened');
    }
  }).on('change', function () {
    $(this).closest(".select_wrapper").addClass('changed').attr("data-value", $(this).val());
  });
  $("body").on('click', 'a', function (event) {
    if ($(this).attr('href') == "#" || $(this).attr('href') == "") {
      event.preventDefault();
    }
  }); // tabs

  $(".tabs").on('click', '.tab_link', function () {
    var th = $(this),
        parent = th.attr("data-parent"),
        activeTab = th.attr("data-tab");

    if (!th.hasClass('active')) {
      th.addClass("active").siblings().removeClass("active");
      th.closest(parent).find('.tab_content[data-parent="' + parent + '"]').hide();
      th.closest(parent).find("#" + activeTab).fadeIn().addClass("active").siblings().removeClass("active");

      if ($('.tab_content[data-parent="' + parent + '"]').find(".slick-slider").length > 0) {
        $('.tab_content[data-parent="' + parent + '"]').find(".slick-slider").slick("setPosition");
      }
    }
  }); // textarea expand

  $("textarea").on('keyup', function (event) {
    autosize($(this));
  });
  $(".header_links_trigger__js").on('click', function (event) {
    event.preventDefault();
    var th = $(this),
        trgt = $(th.attr('href'));
    th.toggleClass('opened');
    $('.header').toggleClass('menu_opened');
    trgt.slideToggle(300);
  });
  $(".toggler__js").on('click', function (event) {
    event.preventDefault();
    var th = $(this),
        trgt = $(th.attr('href'));
    th.toggleClass('opened');
    trgt.slideToggle(300);
  });
  $(".filters_trigger__js").on('click', function (event) {
    event.preventDefault();
    var th = $(this),
        trgt = $(th.attr('href'));
    th.closest('section').toggleClass('menu_opened');
    trgt.toggleClass('opened');
  });
  $(".product_filter__js").on('click', function (event) {
    event.preventDefault();
    var th = $(this),
        trgt = th.closest('.product_filter').find('.product_filters_dropdown');
    th.toggleClass('opened');
    trgt.slideToggle(300);
  }); // plus and minus

  $(".minus_btn").click(function () {
    var inp = $(this).parent(".quantity_wrapper").find(".quantity_input"),
        inpVal = parseFloat(inp.val());

    if (inpVal < 2) {
      inpVal = 2;
    }

    inp.val(inpVal - 1);
  });
  $(".plus_btn").click(function () {
    var inp = $(this).parent(".quantity_wrapper").find(".quantity_input"),
        inpVal = parseFloat(inp.val());
    inp.val(inpVal + 1);
  }); // end of  range1pane plus and minus

  $('.cart_block_check input').on('change', function () {
    var th = $(this);

    if (th.is(':checked')) {
      th.closest('.cart_block').removeClass('unactive');
    } else {
      th.closest('.cart_block').addClass('unactive');
    }
  });
  $('.partner_line_trigger__js').on('click', function (e) {
    e.preventDefault();
    var th = $(this),
        trgt = $(th.attr('href'));
    trgt.slideToggle(300);
    th.toggleClass('opened');
  });
}); //END OF DOCUMENT READY
// textarea

function autosize(ths) {
  var el = ths;
  setTimeout(function () {
    if (el.val().length > 0) {
      el.css('height', el.prop('scrollHeight') + 'px');
    } else {
      el.css('height', '');
    }
  }, 10);
} // img.lozad


$(window).on('load resize', function () {
  if ($('.zoom_img').length) {
    if (window.innerWidth > 991) {
      $('.zoom_img').zoom();
    } else {
      $('.zoom_img').trigger('zoom.destroy');
    }
  }
});
$(window).on('load', function () {
  var observer = lozad('.lozad', {
    loaded: function loaded(el) {
      $(el).addClass('lozad_showed');
    }
  });
  observer.observe();

  if ($('.animated_block').length) {
    $('.animated_block').addClass('animated');
  }
});