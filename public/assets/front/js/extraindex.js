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

    // best-selling-slider Area Start - match popular-category-slider pattern
    var $best_selling_slider = $(".best-selling-slider");
    if ($best_selling_slider.length) {
        // Destroy any existing instance first
        if ($best_selling_slider.data('owlCarousel')) {
            $best_selling_slider.trigger('destroy.owl.carousel');
        }
        
        // Initialize owl carousel - use empty navText to prevent default arrows
        $best_selling_slider.owlCarousel({
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
                    items: 1,
                    margin: 8
                },
                576: {
                    items: 2,
                    margin: 10
                },
                768: {
                    items: 3,
                    margin: 10
                },
                992: {
                    items: 4,
                    margin: 10
                },
                1200: {
                    items: 5,
                    margin: 10
                },
                1400: {
                    items: 5,
                    margin: 10
                }
            },
            onInitialized: function() {
                // Remove any undefined elements or dots that might appear
                $best_selling_slider.find('.owl-dots, .owl-dot, [class*="undefined"]').remove();
                
                // Clear and set nav buttons with single icons
                $best_selling_slider.find('.owl-nav button').each(function() {
                    var $btn = $(this);
                    // Remove all existing content (text, spans, icons, etc.)
                    $btn.empty();
                    // Add only one icon based on button type
                    if ($btn.hasClass('owl-prev')) {
                        $btn.append('<i class="fas fa-chevron-left"></i>');
                    } else if ($btn.hasClass('owl-next')) {
                        $btn.append('<i class="fas fa-chevron-right"></i>');
                    }
                });
            }
        });
        
        // Fix nav buttons after initialization and ensure they work
        setTimeout(function() {
            // Remove any undefined elements
            $best_selling_slider.find('.owl-dots, .owl-dot, [class*="undefined"], [text*="undefined"]').remove();
            $best_selling_slider.find('*').filter(function() {
                return $(this).text().trim() === 'undefined';
            }).remove();
            
            // Ensure nav buttons only have one icon each and are clickable
            $best_selling_slider.find('.owl-nav button').each(function() {
                var $btn = $(this);
                // Remove all content first
                var isPrev = $btn.hasClass('owl-prev');
                var isNext = $btn.hasClass('owl-next');
                $btn.empty();
                
                // Add only one icon
                if (isPrev) {
                    $btn.append('<i class="fas fa-chevron-left"></i>');
                } else if (isNext) {
                    $btn.append('<i class="fas fa-chevron-right"></i>');
                }
                
                // Ensure button is clickable and remove any blocking styles
                $btn.css({
                    'pointer-events': 'all',
                    'cursor': 'pointer',
                    'z-index': '10000',
                    'position': 'absolute',
                    'opacity': '1',
                    'visibility': 'visible'
                });
                
                // Remove any disabled state if present
                $btn.removeClass('disabled');
                
                // Ensure button is not hidden
                $btn.show();
                
                // Add explicit click handlers as backup - use both jQuery and native
                $btn.off('click touchstart').on('click touchstart', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    // Trigger owl carousel navigation
                    if (isPrev) {
                        $best_selling_slider.trigger('prev.owl.carousel', [300]);
                    } else if (isNext) {
                        $best_selling_slider.trigger('next.owl.carousel', [300]);
                    }
                    
                    return false;
                });
                
                // Also add native event listener as backup
                var btnElement = $btn[0];
                if (btnElement) {
                    btnElement.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (isPrev) {
                            $best_selling_slider.trigger('prev.owl.carousel', [300]);
                        } else if (isNext) {
                            $best_selling_slider.trigger('next.owl.carousel', [300]);
                        }
                    }, true);
                }
            });
            
            // Ensure nav container allows button clicks
            $best_selling_slider.find('.owl-nav').css({
                'pointer-events': 'auto',
                'z-index': '10000'
            });
            
            // Add global event delegation as final backup
            $(document).off('click', '.best-selling-slider .owl-nav button.owl-prev')
                       .on('click', '.best-selling-slider .owl-nav button.owl-prev', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $best_selling_slider.trigger('prev.owl.carousel', [300]);
                return false;
            });
            
            $(document).off('click', '.best-selling-slider .owl-nav button.owl-next')
                       .on('click', '.best-selling-slider .owl-nav button.owl-next', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $best_selling_slider.trigger('next.owl.carousel', [300]);
                return false;
            });
        }, 500);
    }

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


