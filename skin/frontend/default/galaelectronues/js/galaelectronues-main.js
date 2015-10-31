(function($) {

EM_Theme = {
	PRODUCTSGRID_ITEM_WIDTH: 370,
	PRODUCTSGRID_ITEM_SPACING: 90,
	CROSSSELL_ITEM_WIDTH: 250,
	CROSSSELL_ITEM_SPACING: 90,
	UPSELL_ITEM_WIDTH: 250,
	UPSELL_ITEM_SPACING: 90
};



if (typeof EM == 'undefined') EM = {};
if (typeof EM.tools == 'undefined') EM.tools = {};


var isMobile = /iPhone|iPod|iPad|Phone|Mobile|Android|hpwos/i.test(navigator.userAgent);
var isPhone = /iPhone|iPod|Phone|Android/i.test(navigator.userAgent);


var domLoaded = false, 
	windowLoaded = false, 
	last_adapt_i, 
	last_adapt_width;


/**
 * Auto positioning product items in products-grid
 *
 * @param (selector/element) productsGridEl products grid element
 * @param (object) options
 * - (integer) width: width of product item
 * - (integer) spacing: spacing between 2 product items
 */
EM.tools.decorateProductsGrid = function (productsGridEl, options) {
	var $productsGridEl = $(productsGridEl);
	if ($productsGridEl.length == 0) return;
	
	var columnCount = Math.floor(($productsGridEl.width() + options.spacing) / (options.width + options.spacing));
	
	$productsGridEl.css({'position':'relative'});
	
	for (var i = 0; i < 20; i++) $('.item', $productsGridEl).removeClass('item-col-'+i);
		
	var maxHeight = 0;
	var i = 0;
	$('.item', $productsGridEl).each(function() {
		var prev = $(this).prevAll('.item-col-' + i).first();
		var top = prev.length > 0 ? prev.position().top + prev.outerHeight(true) + options.spacing: 0;
		
		$(this).addClass('item-col-'+i)
			.css({
				'position': 'absolute',
				'width': options.width + 'px',
				'left': i * (options.spacing + options.width) + 'px',
				'top': top + 'px'
			});
			
		maxHeight = Math.max(maxHeight, $(this).position().top + $(this).outerHeight(true));

		if (++i >= columnCount) i = 0;
	});
	
	$productsGridEl.css({
		'height': maxHeight + options.spacing + 'px'
	});
}

EM.tools.decorateProductCollateralTabs = function() {
	$(document).ready(function() {
		$('.product-collateral').addClass('tab_content').each(function(i) {
			$(this).wrap('<div class="tabs_wrapper collateral_wrapper" />');
			var tabs_wrapper = $(this).parent();
			var tabs_control = $(document.createElement('ul')).addClass('tabs_control').insertBefore(this);
			
			$('.box-collateral', this).addClass('tab-item').each(function(j) {
				var id = 'box_collateral_'+i+'_'+j;
				$(this).addClass('content_'+id);
				tabs_control.append('<li><h2><a href="#'+id+'">'+$('h2', this).html()+'</a></h2></li>');
			});
			
			initToggleTabs(tabs_wrapper);
		});
		
		// update products position after tab showed
		/*
		$('.box-collateral.box-up-sell.tab-item').bind('emtabshow', function() {
			EM.tools.decorateProductsGrid('#upsell-product-table .products-grid', {
				width: EM_Theme.UPSELL_ITEM_WIDTH,
				spacing: EM_Theme.UPSELL_ITEM_SPACING
			});
		});
		*/
	});
};



/**
 * Fix iPhone/iPod auto zoom-in when text fields, select boxes are focus
 */
function fixIPhoneAutoZoomWhenFocus() {
	var viewport = $('head meta[name=viewport]');
	if (viewport.length == 0) {
		$('head').append('<meta name="viewport" content="width=device-width, initial-scale=1.0"/>');
		viewport = $('head meta[name=viewport]');
	}
	
	var old_content = viewport.attr('content');
	
	function zoomDisable(){
		viewport.attr('content', old_content + ', user-scalable=0');
	}
	function zoomEnable(){
		viewport.attr('content', old_content);
	}
	
	$("input[type=text], textarea, select").mouseover(zoomDisable).mousedown(zoomEnable);
}


/**
 * Change the alternative product image when hover
 */
function alternativeProductImage() {
	
	var tm;
	function swap() {
		clearTimeout(tm);
		setTimeout(function() {
			el = $(this).find('img[data-alt-src]');
			var newImg = $(el).data('alt-src');
			var oldImg = $(el).attr('src');
			$(el).attr('src', newImg).data('alt-src', oldImg);
		}.bind(this), 200);
	}
	
	$('.item .product-image img[data-alt-src]').parents('.item').bind('mouseenter', swap).bind('mouseleave', swap);
}


/**
 * Adjust elements to make it responsive
 *
 * Adjusted elements:
 * - Image of product items in products-grid scale to 100% width
 */
function responsive() {
	
	// resize products-grid's product image to full width 100% {{{
	var position = $('.products-grid .item').css('position');
	if (position != 'absolute' && position != 'fixed' && position != 'relative')
		$('.products-grid .item').css('position', 'relative');
		
	var img = $('.products-grid .item .product-image img');
	img.each(function() {
		img.data({
			'width': $(this).width(),
			'height': $(this).height()
		})
	});
	img.removeAttr('width').removeAttr('height').css('width', '100%');
	// }}}
	
	$('.sidebar img, .custom-logo').each(function() {
		$(this).css({
			'max-width': $(this).width(),
			'width': '100%'
		});
	});
}


/**
 * Function called when layout size changed by adapt.js
 */
function whenAdapt(i, width) {
	
	$('body').removeClass('adapt-0 adapt-1 adapt-2 adapt-3 adapt-4 adapt-5 adapt-6')
		.addClass('adapt-'+i);
		
	EM.tools.decorateProductsGrid('.category-products .products-grid', {
		width: EM_Theme.PRODUCTSGRID_ITEM_WIDTH,
		spacing: EM_Theme.PRODUCTSGRID_ITEM_SPACING
	});
	/*EM.tools.decorateProductsGrid('#upsell-product-table .products-grid', {
		width: EM_Theme.UPSELL_ITEM_WIDTH,
		spacing: EM_Theme.UPSELL_ITEM_SPACING
	});
	EM.tools.decorateProductsGrid('#crosssell-products-list', {
		width: EM_Theme.CROSSSELL_ITEM_WIDTH,
		spacing: EM_Theme.CROSSSELL_ITEM_SPACING
	});*/
fixMenuDefault();
}


function initToggleTabs_Product($selector){
	if(jQuery($selector).length > 0){
		var timeout = new Array(jQuery($selector).length);
		var div = new Array(jQuery($selector).length);
		jQuery($selector).addClass('ui-tabs ui-widget ui-widget-content ui-corner-all');
		jQuery($selector).each(function(index,value){
			timeout[index] = null;
			div[index] = jQuery(this);
			div[index].addClass('ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all');
			//When page loads...
			div[index].find(".tab-item").hide(); //Hide all content
			div[index].children('div').children('ul').find("li:first").addClass("ui-tabs-selected").show(); //Activate first tab
			div[index].children('div').children('ul').addClass('ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all');
			div[index].children('div').children('ul').find('li').addClass('ui-state-default ui-corner-top');	
			div[index].find(".tab-item:first").show(); //Show first tab content
			//On Click Event
			div[index].children('div').children('ul').find("li").click(function() {
				var currentTab = jQuery(this);
				if(currentTab.hasClass('ui-tabs-selected'))
					return false;
				if (timeout[index])
					clearTimeout(timeout[index]);
				timeout[index] = setTimeout(function() {
					timeout[index] = null;	
					// Hide old content tab
					jQuery(div[index].children('div').children('ul').find('li.ui-tabs-selected a').attr('href')).toggle('slow');
					
					div[index].children('div').children('ul').find("li").removeClass("ui-tabs-selected"); //Remove any "ui-tabs-selected" class
					currentTab.addClass("ui-tabs-selected"); //Add "active" class to selected tab
					
					var activeTab = currentTab.find("a").attr("href"); //Find the href attribute value to identify the active tab + content
					jQuery(activeTab).toggle('slow'); //Fade in the active ID content
					return false;
				}, 10);
				return false;
			});	
		});
		
	}
}

// Back to top
function backToTop(){
    // hide #back-top first
	jQuery("#back-top").hide();
	
	// fade in #back-top
	
	jQuery(window).scroll(function () {
		if (jQuery(this).scrollTop() > 100) {
			jQuery('#back-top').fadeIn();
		} else {
			jQuery('#back-top').fadeOut();
		}
	});

	// scroll body to 0px on click
	jQuery('#back-top a').click(function () {
		jQuery('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});

}
/**
 * Callback function called when stylesheet is changed by adapt.js
 */
ADAPT_CONFIG.callback = function(i, width) {
	last_adapt_i = i;
	last_adapt_width = width;
	
	whenAdapt(last_adapt_i, last_adapt_width);
};

function toolbar(){

	$('.show').each(function(){
		$(this).insertTitle();
		$(this).insertUl();
		$(this).selectUl();
	});
	$('.sortby').each(function(){
		$(this).insertTitle();
		$(this).insertUl();
		$(this).selectUl();
	});
}

jQuery(document).ready(function() {
	domLoaded = true;  
    initToggleTabs_Product('#product-content-tabs');

	isMobile && fixIPhoneAutoZoomWhenFocus();
	alternativeProductImage();
	setupReviewLink();

	/*if(!isMobile){
        jQuery('.toolbar-switch').on({
            click: function (e) {
                jQuery(this).addClass('over');
            }
        });
		toolbar();
	}*/
	if(!jQuery('body').viewPC()){
		toolbar();
	}
	
	jQuery('.bxslider').bxSlider({
	  minSlides: 2,
	  maxSlides: 2,
	  slideWidth: 360,
	  slideMargin: 10
	});

});

jQuery(window).bind('load', function() {
	windowLoaded = true;
	responsive();
	whenAdapt(last_adapt_i, last_adapt_width);
});

jQuery(document).ready(function(){
    backToTop();
});

})(jQuery);

function showAgreementPopup(e) {
	
	//jQuery('#checkout-agreements .agreement-content').show();
	//$('agreement-content-popup').show();
		
	jQuery('#checkout-agreements label.a-click').parent().parent().children('.agreement-content').show()
		.css({
			'left': (parseInt(document.viewport.getWidth()) - jQuery('#checkout-agreements label.a-click').parent().parent().children('.agreement-content').width())/2 +'px',
			'top': (parseInt(document.viewport.getHeight()) - jQuery('#checkout-agreements label.a-click').parent().parent().children('.agreement-content').height())/2 + 'px'
		});
	
};


function hideAgreementPopup(e) {
	//$('opc-agreement-popup-overlay').hide();
	jQuery('#checkout-agreements .agreement-content').hide();
	
};

function initSlider(e) {
	var $ = jQuery;
	$(e)
	.addClass('jcarousel-skin-tango')
	.jcarousel({
		auto:0,
		buttonNextHTML:'<a class="next" href="javascript:void(0);" title="Next"></a>',
        buttonPrevHTML:'<a class="previous" href="javascript:void(0);" title="Previous"></a>',
		scroll: 1,
		animation:'slow',
		initCallback: function (carousel) {
			var context = carousel.container.context;
			$(context).touchwipe({
				wipeLeft: function() { 
                    carousel.next();
                },
                wipeRight: function() { 
                    carousel.prev();
                },
                preventDefaultEvents: false
            });
            jQuery(window).bind('emadaptchange', function() {
                $(window).trigger('resize.jcarousel');
            });
		}
	});
}

function showReviewTab() {
	
	var $ = jQuery;
	
	function getReviewTabHandle() {
		var classes = $('#customer-reviews').attr('class').split(' ');
		var href = '';
		$(classes).each(function (i, e) {
			if (/content_box_collateral/.test(e)) {
				href = e.replace('content_', '');
			}
		});
		return $('[href="#'+href+'"]');
	}
	
	var reviewTab = getReviewTabHandle();//$('.tabs_control li:contains(Review)');
	if (reviewTab.size()) {
		// scroll to review tab
		$('html, body').animate({
			 scrollTop: reviewTab.offset().top
		}, 500);
		 
		 // show review tab
		reviewTab.click();
	} else if ($('#customer-reviews').size()) {
		// scroll to customer review
		$('html, body').animate({ scrollTop: $('#customer-reviews').offset().top }, 500);
	} else {
		return false;
	}
	return true;
}

function setupReviewLink() {
	jQuery('.r-lnk').click(function (e) {
		if (showReviewTab())
			e.preventDefault();
	});
}


/**
 * Fix iPhone/iPod menu default magento
 */

function fixMenuDefault(){
	isMobile = /iPhone|iPod|Phone|Android|hpwos/i.test(navigator.userAgent);
	isMobileView = jQuery('body').hasClass('adapt-0');
	if(isMobile){
			var lis=$$('#nav li.parent');
			lis.each(function(li) {

			 var dt = new Element('dt');
			 var dd = new Element('dd');
			 var a = li.down(0);
			 var ul = li.down(2);
			 dt.insert(a);
			 dd.insert(ul);
			 dt.insert("<span class='nav'>nav</span>");
			 li.insert(dt,{position:top});
			 li.insert(dd);
			});
			jQuery('#nav li.parent dd ul ').hide();
			jQuery('#nav dt span.nav').click(function()  {
				
		        jQuery('.td_active').removeClass('td_active');
		        var node = jQuery(this).parent().next().find('ul').first();
		        
		        var i = (node.css('display')=="block");
		      
				var string = jQuery(this).parent().parent().parent().parent().parent().attr('class');
				if(string.indexOf('parent')!=-1)
				{
					jQuery(this).parent().parent().parent().parent().parent().addClass('td_active');
					jQuery(this).parent().parent().parent().css("display","block");
					jQuery(this).parent().parent().find('ul').slideUp('500');
				}
				else
				{
					jQuery('#nav li.parent dd ul:visible').slideUp('500');
				}
		        jQuery(this).parent().parent().addClass('td_active');    
		        if (i != true) 	
		        {
		        	jQuery(this).parent().next().find('ul').first().slideDown('500');
		        	
		        }
		        return false;
		    });
		//});

		
	} else {
		var lis=$$('#nav li.parent');
		if (jQuery('#nav li.parent').hasClass('.menu-item-parent')){
				jQuery('#nav li.parent').removeClass('.menu-item-parent');
			}
		lis.each(function(li) {
			
		});
		
		jQuery('#nav li.parent ').addClass('menu-item-link');
		jQuery('#nav li.parent ul ').addClass('menu-container');
		
		jQuery(function($) {
			function menuleft() {
				if(!isMobileView){
					$('#nav .menu-item-link > .menu-container').parent().hover(
							function() {
								var $container = $(this).children('.menu-container');
						        var $grid = $(this).parents('.nav-container').first().parents().first();
						        var $widthGridParent = $(this).parents('.nav-container').first().parents().first().width();    		
						        $container.css('left',$widthGridParent-2+'px');
							},
							function() {					
								var $container = $(this).children('.menu-container');
								$container.removeAttr('style');
							}
					);
				} else if(isMobileView){
					$('#nav .menu-item-link > .menu-container').parent().hover(
					function() {
						var $container = $(this).children('.menu-container');
				        $container.css('display','block');
					},
					function() {					
						var $container = $(this).children('.menu-container');
						$container.css('display','none');
					}
					);
				}
			}
			/*if(!isMobileView){
				menuleft();
			} else{
				var config = {
			        over: function(){
			            jQuery('.shown-sub', this).animate({opacity:1, height:'toggle'}, 100);
			        },
			        timeout: 0, // number = milliseconds delay before onMouseOut
			        out: function(){
			            var that = this;
			            jQuery('.shown-sub', this).animate({opacity:0, height:'toggle'}, 100);

	        		}

    			};
    			jQuery('li.parent .over, li.parent .over .shown-sub').hoverIntent( config );
			}*/
			menuleft();
		});
	}
}
