// window.onresize = function(){ window.location.reload()}

// JavaScript Document
var $ = jQuery.noConflict();
jQuery(document).ready(function () {

    $(".page-my-account #mailchimp_woocommerce_newsletter").prop('checked', false);

    /* my acc page arrow trigger*/
    $('.woocommerce-MyAccount-navigation-link').after().click(function () {
        $(this).find('a')[0].click();
    });
    
    $(".click-search a").click(function () {
        $(".search-header form").slideToggle();
    });
    // menu
    $(".navbar-toggler").click(function () {
        $("nav").toggleClass("menu-open");
        $("body").toggleClass("o-hidden");
    });
    $(".home-slider").owlCarousel({
        loop: true,
        dots: true,
        nav: true,
        margin: 0,
        items: 1,
        stagePadding: 0,
        autoplay: false,
        smartSpeed: 1500,
        // mouseDrag: false,
        // animateOut: 'fadeOut',
        responsive: {
            768: {
                dots: true, 
            },
            640: {
                dots: true, 
            },
            0: {
                dots: true, 
            }
        }
    }),
    
    // latest offer slider
    $(".brands-offer").owlCarousel({
        items: 5,
        nav: true,
        navText: ["<img src=/wp-content/themes/apparel_direct/assets/images/prev.svg>", "<img src=/wp-content/themes/apparel_direct/assets/images/next.svg>"],
        pagination: false,
        touchDrag: false,
        paginationSpeed: 500,
        dots: false,
        loop: true,
        mouseDrag: true,
        autoplay: false,
        smartSpeed: 1500,
        margin: 10,
        responsive: {
            1600: {
                items: 5
            },
            769: {
                items: 5
            },
            768: {
                items: 3,
				touchDrag: true,
            },
            480: {
                items: 2,
				touchDrag: true,
            },
            300: {
                items: 1,
				touchDrag: true,
            }
        }
    })
    
    
});

var btn = $('#button');

$(window).scroll(function() {
  if ($(window).scrollTop() > 300) {
    btn.addClass('show');
  } else {
    btn.removeClass('show');
  }
});

btn.on('click', function(e) {
  e.preventDefault();
  $('html, body').animate({scrollTop:0}, '300');
});



$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

$(document).ready(function() {

    var owl = $('.brands-offer');
    owl.owlCarousel({
    });

    /*keyboard navigation*/
    $(document.documentElement).keyup(function(event) {    
        if (event.keyCode == 37) { /*left key*/
            owl.trigger('prev.owl.carousel', [700]);
        } else if (event.keyCode == 39) { /*right key*/
            owl.trigger('next.owl.carousel', [700]);
        }
    });

});


$(document).ready(function() {
    // Cache selectors for faster performance.
    if (jQuery('body.single-product .variation_table_wrapper').length > 0) {

    
    var $window = $(window),
        $mainMenuBar = $('.wcbvp-total-wrapper'),
        $mainMenuBarAnchor = $('.wcbvp-cart');

    // Run this on scroll events.
    $window.scroll(function() {
        var window_top = $window.scrollTop();
        var div_top = $mainMenuBarAnchor.offset().top;
        
        if (window_top > div_top) {
            // Make the div sticky.
            $mainMenuBar.addClass('fixed_add_to_cart');
            $mainMenuBar.removeClass('bottom_fixed_add_to_cart');
            jQuery('.type-product').removeClass('parent_fixed_add_to_cart');
            $mainMenuBarAnchor.height($mainMenuBar.height());
        }
        else {
            // Unstick the div.
            $mainMenuBar.removeClass('fixed_add_to_cart');
            $mainMenuBar.addClass('bottom_fixed_add_to_cart');
            jQuery('.type-product').addClass('parent_fixed_add_to_cart');
            $mainMenuBarAnchor.height(0);
        }
    });
    }
});

if(navigator.userAgent.indexOf('Mac') > 0)
$('body').addClass('mac-os');


