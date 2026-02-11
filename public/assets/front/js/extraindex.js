$(function ($) {

	"use strict";

         function lazy (){
			$(".lazy").Lazy({
				scrollDirection: 'vertical',
				effect: "fadeIn",
				effectTime:1000,
				threshold: 0,
				visibleOnly: false,  
				onError: function(element) {
					console.log('error loading ' + element.data('src'));
				}
			});
		}

		$(document).ready(function(){
			lazy();
		})
	// Flash Deal Area Start
    var $hero_slider_main = $(".hero-slider-main");
    $hero_slider_main.owlCarousel({
        navText: [],
        nav: true,
        dots: true,
        loop: true,
        autoplay: true,
        autoplayTimeout: 7000,
        items: 1,
    });

    // popular_category_slider
    var $popular_category_slider = $(".popular-category-slider");
    $popular_category_slider.owlCarousel({
        navText: [],
        nav: true,
        dots: false,
        loop: false,
        autoplayTimeout: 6000,
        smartSpeed: 1200,
        margin: 15,
        responsive: {
            0: {
                items: 2,
            },
            576: {
                items: 2,
            },
            768: {
                items: 3,
            },
            992: {
                items: 4,
            },
            1200: {
                items: 4,
            },
            1400: {
                items: 5
            }
        },
    });



    // Flash Deal Area Start
    var $flash_deal_slider = $(".flash-deal-slider");
    $flash_deal_slider.owlCarousel({
        navText: [],
        nav: true,
        dots: false,
        autoplayTimeout: 6000,
        smartSpeed: 1200,
        margin: 15,
        responsive: {
            0: {
                items: 1,
                margin: 0,
            },
            576: {
                items: 2,
                margin: 0,
            },
            768: {
                items: 3,
                margin: 0,
            },
            992: {
                items: 4,
                margin: 0,
            },
            1200: {
                items: 4,
                margin: 0,
            },
            1400: {
                items: 1,
            },
        },
    });

    // col slider
    var $col_slider = $(".newproduct-slider");
    $col_slider.owlCarousel({
        navText: [],
        nav: true,
        dots: false,
        loop: false,
        autoplayTimeout: 6000,
        smartSpeed: 1200,
        margin: 15,
        responsive: {
            0: {
                items: 1,
            },
            530: {
                items: 1,
            },
        },
    });

    // col slider 2
    var $col_slider2 = $(".toprated-slider");
    $col_slider2.owlCarousel({
        navText: [],
        nav: true,
        dots: false,
        loop: true,
        autoplayTimeout: 6000,
        smartSpeed: 1200,
        margin: 15,
        responsive: {
            0: {
                items: 1,
            },
            530: {
                items: 1,
            },
        },
    });

    // newproduct-slider Area Start
    var $newproduct_slider = $(".features-slider");
    $newproduct_slider.owlCarousel({
        navText: [],
        nav: true,
        dots: false,
        autoplayTimeout: 6000,
        smartSpeed: 1200,
        loop: false,
        margin: 15,
        responsive: {
            0: {
                items: 2,
            },
            576: {
                items: 2,
            },
            768: {
                items: 3,
            },
            992: {
                items: 4,
            },
            1200: {
                items: 4,
            },
            1400: {
                items: 5
            }
        },
    });

    // Helper function to initialize product slider
    function initProductSlider($slider) {
        if (!$slider.length) return;
        
        // Allow overriding large-screen items count via data attribute
        var lgItems = parseInt($slider.data('items-lg')) || 5;
        
        // Destroy any existing instance first
        if ($slider.data('owlCarousel')) {
            $slider.trigger('destroy.owl.carousel');
        }
        
        // Initialize owl carousel
        $slider.owlCarousel({
            navText: [],
            nav: true,
            dots: false,
            loop: false,
            autoplay: true,
            autoplayTimeout: 5000,
            smartSpeed: 1200,
            margin: 10,
            responsive: {
                0: {
                    items: 2,
                    margin: 8,
                    slideBy: 1
                },
                576: {
                    items: 2,
                    margin: 10,
                    slideBy: 1
                },
                768: {
                    items: 3,
                    margin: 10,
                    slideBy: 1
                },
                992: {
                    items: 4,
                    margin: 10,
                    slideBy: 1
                },
                1200: {
                    items: lgItems,
                    margin: 10,
                    slideBy: 1
                },
                1400: {
                    items: lgItems,
                    margin: 10,
                    slideBy: 1
                }
            },
            onInitialized: function() {
                // Remove any undefined elements or dots that might appear
                $slider.find('.owl-dots, .owl-dot, [class*="undefined"]').remove();
                
                // Clear and set nav buttons with single icons
                $slider.find('.owl-nav button').each(function() {
                    var $btn = $(this);
                    $btn.empty();
                    if ($btn.hasClass('owl-prev')) {
                        $btn.append('<i class="fas fa-chevron-left"></i>');
                    } else if ($btn.hasClass('owl-next')) {
                        $btn.append('<i class="fas fa-chevron-right"></i>');
                    }
                });
            }
        });
        
        // Fix nav buttons after initialization
        setTimeout(function() {
            // Remove any undefined elements
            $slider.find('.owl-dots, .owl-dot, [class*="undefined"], [text*="undefined"]').remove();
            $slider.find('*').filter(function() {
                return $(this).text().trim() === 'undefined';
            }).remove();
            
            // Ensure nav buttons only have one icon each and are clickable
            $slider.find('.owl-nav button').each(function() {
                var $btn = $(this);
                var isPrev = $btn.hasClass('owl-prev');
                var isNext = $btn.hasClass('owl-next');
                $btn.empty();
                
                if (isPrev) {
                    $btn.append('<i class="fas fa-chevron-left"></i>');
                } else if (isNext) {
                    $btn.append('<i class="fas fa-chevron-right"></i>');
                }
                
                $btn.css({
                    'pointer-events': 'all',
                    'cursor': 'pointer',
                    'z-index': '10000',
                    'position': 'absolute',
                    'opacity': '1',
                    'visibility': 'visible'
                });
                
                $btn.removeClass('disabled');
                $btn.show();
                
                // Add click handlers
                $btn.off('click touchstart').on('click touchstart', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    if (isPrev) {
                        $slider.trigger('prev.owl.carousel', [300]);
                    } else if (isNext) {
                        $slider.trigger('next.owl.carousel', [300]);
                    }
                    return false;
                });
            });
            
            $slider.find('.owl-nav').css({
                'pointer-events': 'auto',
                'z-index': '10000'
            });
        }, 500);
    }

    // Initialize all best selling sliders (including row-1 and row-2)
    $(".best-selling-slider").each(function() {
        initProductSlider($(this));
    });
    
    // Initialize all featured products sliders (including row-1 and row-2)
    $(".featured-products-slider").each(function() {
        initProductSlider($(this));
    });

    // Initialize low stock products sliders (6 items on large screens)
    $(".low-stock-products-slider").each(function() {
        initProductSlider($(this));
    });

    // Featured products slider initialization is now handled by the helper function above

    // home-blog-slider
    var $home_blog_slider = $(".home-blog-slider");
    $home_blog_slider.owlCarousel({
        navText: [],
        nav: true,
        dots: false,
        autoplayTimeout: 6000,
        smartSpeed: 1200,
        loop: true,
        margin: 15,
        responsive: {
            0: {
                items: 1,
            },
            576: {
                items: 2,
            },
            768: {
                items: 3,
            },
            992: {
                items: 3,
            },
            1200: {
                items: 3,
            },
            1400: {
                items: 4,
            }
        },
    });


    // brand-slider
    var $brand_slider = $(".brand-slider");
    $brand_slider.owlCarousel({
        navText: [],
        nav: true,
        dots: false,
        autoplayTimeout: 6000,
        smartSpeed: 1200,
        loop: true,
        margin: 0,
        responsive: {
            0: {
                items: 2,
            },
            575: {
                items: 3,
            },
            790: {
                items: 4,
            },
            1100: {
                items: 4,
            },
            1200: {
                items: 4,
            },
            1400: {
                items: 5,
            }
        },
    });

    // toprated-slider Area Start
    var $relatedproductsliderv = $(".relatedproductslider");
    $relatedproductsliderv.owlCarousel({
        nav: false,
        dots: true,
        autoplayTimeout: 6000,
        smartSpeed: 1200,
        margin: 15,
        responsive: {
            0: {
                items: 2,
            },
            576: {
                items: 2,
            },
            768: {
                items: 3,
            },
            992: {
                items: 4,
            },
            1200: {
                items: 4,
            },
            1400: {
                items: 5
            }
        },
    });


$('.left-category-area .category-header').on('click', function(){
    $('.left-category-area .category-list').toggleClass("active")
});


$("[data-date-time]").each(function () {
    var $this = $(this),
        finalDate = $(this).attr("data-date-time");
    $this.countdown(finalDate, function (event) {
        $this.html(
            event.strftime(
                "<span>%D<small>Days</small></span></small> <span>%H<small>Hrs</small></span> <span>%M<small>Min</small></span> <span>%S<small>Sec</small></span>"
            )
        );
    });
});

// Subscriber Form Submit
$(document).on("submit", ".subscriber-form", function (e) {
    e.preventDefault();
    var $this = $(this);
    var submit_btn = $this.find("button");
    submit_btn.find(".fa-spin").removeClass("d-none");
    $this.find("input[name=email]").prop("readonly", true);
    submit_btn.prop("disabled", true);
    $.ajax({
        method: "POST",
        url: $(this).prop("action"),
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.errors) {
                for (var error in data.errors) {
                    dangerNotification(data.errors[error]);
                }
            } else {
                if ($this.hasClass("subscription-form")) {
                    $(".close-popup").click();
                }
                successNotification(data);
                $this.find("input[name=email]").val("");
            }
            submit_btn.find(".fa-spin").addClass("d-none");
            $this.find("input[name=email]").prop("readonly", false);
            submit_btn.prop("disabled", false);
        },
    });
});


});


