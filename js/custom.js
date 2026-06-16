
(function($) {

  var wow = new WOW({
    boxClass: 'wow', 
    animateClass: 'animated', 
    offset: 0, 
    mobile: false 
  });
  wow.init();

  
  $(window).scroll(function() {
    if ($(".navbar").offset().top > 50) {
      $(".navbar-fixed-top").addClass("top-nav-collapse");
      $(".top-area").addClass("top-padding");
      $(".navbar-brand").addClass("reduce");

      $(".navbar-custom ul.nav ul.dropdown-menu").css("margin-top", "11px");

    } else {
      $(".navbar-fixed-top").removeClass("top-nav-collapse");
      $(".top-area").removeClass("top-padding");
      $(".navbar-brand").removeClass("reduce");

      $(".navbar-custom ul.nav ul.dropdown-menu").css("margin-top", "16px");

    }
  });

	var navMain = $(".navbar-collapse"); 
	navMain.on("click", "a:not([data-toggle])", null, function () {
	   navMain.collapse('hide');
	});

  
  $(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
      $('.scrollup').fadeIn();
    } else {
      $('.scrollup').fadeOut();
    }
  });
  $('.scrollup').click(function() {
    $("html, body").animate({
      scrollTop: 0
    }, 1000);
    return false;
  });

  
  $(function() {
    $('.navbar-nav li a').bind('click', function(event) {
      var $anchor = $(this);
      var nav = $($anchor.attr('href'));
      if (nav.length) {
        $('html, body').stop().animate({
          scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');

        event.preventDefault();
      }
    });
    $('.page-scroll a').bind('click', function(event) {
      var $anchor = $(this);
      $('html, body').stop().animate({
        scrollTop: $($anchor.attr('href')).offset().top
      }, 1500, 'easeInOutExpo');
      event.preventDefault();
    });
  });

  
  $('#owl-works').owlCarousel({
    items: 4,
    itemsDesktop: [1199, 5],
    itemsDesktopSmall: [980, 5],
    itemsTablet: [768, 5],
    itemsTabletSmall: [550, 2],
    itemsMobile: [480, 2],
  });

  
  $('.owl-carousel .item a').nivoLightbox({
    effect: 'fadeScale', 
    theme: 'default', 
    keyboardNav: true, 
    clickOverlayToClose: true, 
    onInit: function() {}, 
    beforeShowLightbox: function() {}, 
    afterShowLightbox: function(lightbox) {}, 
    beforeHideLightbox: function() {}, 
    afterHideLightbox: function() {}, 
    onPrev: function(element) {}, 
    onNext: function(element) {}, 
    errorMessage: 'The requested content cannot be loaded. Please try again later.' 
  });

  jQuery('.appear').appear();
  jQuery(".appear").on("appear", function(data) {
    var id = $(this).attr("id");
    jQuery('.nav li').removeClass('active');
    jQuery(".nav a[href='#" + id + "']").parent().addClass("active");
  });


  
  if ($('.parallax').length) {
    $(window).stellar({
      responsive: true,
      scrollProperty: 'scroll',
      parallaxElements: false,
      horizontalScrolling: false,
      horizontalOffset: 0,
      verticalOffset: 0
    });

  }


  (function($, window, document, undefined) {

    var gridContainer = $('#grid-container'),
      filtersContainer = $('#filters-container');

    
    gridContainer.cubeportfolio({

      defaultFilter: '*',

      animationType: 'sequentially',

      gapHorizontal: 50,

      gapVertical: 40,

      gridAdjustment: 'responsive',

      caption: 'fadeIn',

      displayType: 'lazyLoading',

      displayTypeSpeed: 100,

      
      lightboxDelegate: '.cbp-lightbox',
      lightboxGallery: true,
      lightboxTitleSrc: 'data-title',
      lightboxShowCounter: true,

      
      singlePageDelegate: '.cbp-singlePage',
      singlePageDeeplinking: true,
      singlePageStickyNavigation: true,
      singlePageShowCounter: true,
      singlePageCallback: function(url, element) {

        
        var t = this;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            timeout: 5000
          })
          .done(function(result) {
            t.updateSinglePage(result);
          })
          .fail(function() {
            t.updateSinglePage("Error! Please refresh the page!");
          });

      },

      
      singlePageInlineDelegate: '.cbp-singlePageInline',
      singlePageInlinePosition: 'above',
      singlePageInlineShowCounter: true,
      singlePageInlineInFocus: true,
      singlePageInlineCallback: function(url, element) {
        
      }
    });

    
    filtersContainer.on('click', '.cbp-filter-item', function(e) {

      var me = $(this),
        wrap;

      
      if (!$.data(gridContainer[0], 'cubeportfolio').isAnimating) {

        if (filtersContainer.hasClass('cbp-l-filters-dropdown')) {
          wrap = $('.cbp-l-filters-dropdownWrap');

          wrap.find('.cbp-filter-item').removeClass('cbp-filter-item-active');

          wrap.find('.cbp-l-filters-dropdownHeader').text(me.text());

          me.addClass('cbp-filter-item-active');
        } else {
          me.addClass('cbp-filter-item-active').siblings().removeClass('cbp-filter-item-active');
        }

      }

      
      gridContainer.cubeportfolio('filter', me.data('filter'), function() {});

    });

    
    gridContainer.cubeportfolio('showCounter', filtersContainer.find('.cbp-filter-item'));

  })(jQuery, window, document);


})(jQuery);
$(window).load(function() {
  $(".loader").delay(100).fadeOut();
  $("#page-loader").delay(100).fadeOut("fast");
});
