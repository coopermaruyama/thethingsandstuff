function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}
function getURLParameter(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||"";
}
//add/update query strings
function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
	separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
}


jQuery.noConflict();
(function( $ ) {
	$(window).load(function() {
		// $('.etalage_thumb img.etalage_thumb_image').resize();
		/* Resize Category Images Plugin by Envy Websites
		BEGIN */
		// $('.category-products .product-image-area .product-image').each(function(i,v) {
		// 	containerHeight = $(this).height();
		// 	containerWidth = $(this).width();
		// 	img = $('img:eq(0)', this);
		// 	imgStartHeight = img.height();
		// 	imgStartWidth = img.width();

		// 	doesntFit = (imgStartWidth >= containerWidth && imgStartHeight >= containerHeight) ? false : true;

		// 	if (doesntFit) {

		// 		$(this).css('overflow','hidden');

		// 		yStretch = (imgStartHeight < imgStartWidth || imgStartHeight == imgStartWidth) ? true : false;
		// 		multiplier = yStretch ? containerHeight / imgStartHeight : containerWidth / imgStartWidth;

		// 		newHeight = (imgStartHeight * multiplier);
		// 		newWidth = (imgStartWidth * multiplier);
		// 		img.css('min-height',newHeight+'px');
		// 		img.css('min-width',newWidth+'px');

		// 		img.css({"position":"relative","top":"0","left":"0"});
		// 		if (yStretch) {
		// 			xOverflow = ($(img).width() - imgStartWidth) / 2;
		// 			img.css('margin-left',  '-'+xOverflow+'px'); 
		// 		}
		// 		else {
		// 			yOverflow = ($(img).height() - imgStartHeight) / 2;
		// 			img.css('margin-top',  '-'+yOverflow+'px'); 
		// 		};

		// 	};
		// });
		// console.log('dont resizing');
		// $('.category-products').trigger("doneResizing");

		/* Resize Category Images Plugin by Envy Websites
		END */
		
		
	});
$(function() {

	// persist navigation accordion state
	parser = document.createElement("a");
	parser.href = window.location.href;
	if (parser.pathname != "/" && parser.pathname != "") {
		$("ul#nav").find("a").each(function() {
		  regex = new RegExp(parser.pathname);
		  if (regex.test($(this).attr("href"))) {
		    level = parseInt($(this).closest("li").attr("class").match(/level([0-9])/)[1]);
		    for (i = 1; i <= level; i++) {$(this).closest("ul.level"+i).slideDown();}
		  }
		});
	};

	// Mobile search bug fix: since resizing window can toggle the sidebar, and clicking the search box on mobile opens up a keyboard which in turn fires a window resize, the sidebar which contains the searchbox goes away. this fixes that.
	$(".form-search input").click(function(e) { 
		window.pauseResize = true;
		window.setTimeout(function() {
			window.pauseResize = false;
		}, 5000)
	});

	// Mobile Navigation
	$("nav#nav-top select").change(function() {
	  window.location = $(this).find("option:selected").val();
	});

	$("#header-toggle").click(function() {
		$(this).hasClass("open-state") ? $("#header-sidebar, #header-toggle,#header-sidebar-overlay,.copyrights").animate({left:'-=219px'},1000) : $("#header-sidebar, #header-toggle,#header-sidebar-overlay,.copyrights").animate({left:'+=219px'},1000);
		$(this).hasClass("open-state") ? $(".main-container").animate({marginLeft: 0},1000) : $(".main-container").animate({marginLeft: 220},1000);
		$(this).hasClass("open-state") ? $(document).trigger("sidebarWidthChanged", "-=219") : $(document).trigger("sidebarWidthChanged", "+=219");
		$(this).toggleClass("open-state");
	});
	 //  830
	 if ($(window).width() < 835) {
	 	$("#header-sidebar").animate({left:-219},1000);
	 	$("#header-toggle").removeClass("open-state").animate({left:5},1000);
	 	$(".main-container").animate({marginLeft: 1},1000);
	 	$("#header-sidebar-overlay").css('left','0');
	 	$("#opc-wrap").is(':visible') && $("#opc-wrap,#checkoutSteps .buttons-set").animate({left: "-=219"},1000);
	 };
	 $(".products-grid .price-box").hover(function() {
	 	$(this).closest("li.item").css("opacity",".9");
	 },function() {
	 	$(this).closest("li.item").css("opacity","1");
	 });

	 $(".products-grid .price-box").click(function() {
	 	$(this).closest("li.item").find("a.product-image").trigger("click");
	 })
	 var id;
	 $(window).resize(function() {
	     clearTimeout(id);
	     id = setTimeout(doneResizing, 500);
	     
	 });

	 function doneResizing(){
	    if ($("#header-toggle").hasClass("open-state") && $(window).width() < 835 && !window.pauseResize) {
	    	$("#header-toggle").trigger("click");
	    };
	 } 
		/*
		*   INFINITE SCROLL CUSTOM
		*/ 
		window.currpage = 1;
		
		if (/total/.test(jQuery(".toolbar:last .amount").text())) { //returns false if only 1 page available
		  //kinda bootleg, parses onpage text to find out the total amount of items in the collection
			window.collectionItems = parseInt(jQuery(".toolbar:last .amount").text().match(/([0-9]+).total/)[1]);
			window.collectionItemsCounter = 30;
		}
		if ($("ul.products-grid > li.item").size() != window.collectionItems) { //counts items on page v.s. what the toolbar says is available
			$(window).scroll( $.throttle(250, activate) );
		}

		function activate() {
			pixelsLeft = jQuery(document).height() - jQuery(window).scrollTop() - jQuery(window).height();
			if (pixelsLeft < 200 && !window.scrollProcessing && window.collectionItems > window.collectionItemsCounter) {
				window.scrollProcessing = true; //save this so we can avoid another activation before request is done
				window.currpage++;

				$(".category-products").append('<div id="infscr-loading" class="infscr-loading-msg"><img alt="Loading..." src="/media/md/quickview/loadingAnimation.gif"><div><em>Loading more products...</em></div></div>')

				theUrl = updateQueryStringParameter(window.location.href,"p",window.currpage);
				$.ajax({
					url: theUrl,
					data: { sliderAjax: true },
					success: function(response) {
						html = $.parseJSON(response).productlist;
						$items = $(html).first().find("ul.products-grid li.item");
						totalItems = $items.size(); //keep count of items so we can run a callback when finished
						itemsCounter = 0; window.collectionItemsCounter += totalItems;
						if (totalItems > 0 ) {
							$items.each(function(index, value) {
								$("ul.products-grid").append(value);
								itemsCounter++;
								if (itemsCounter == totalItems) { 
									window.scrollProcessing = false; 
									$(".infscr-loading-msg").remove();
									window.Grid.addItems( $items );
								}
							});
						} 
						else {
							$(window).unbind("scroll");
							$("#infscr-loading img").remove();
							$("#infscr-loading em").text("There are no additional products to display. To see more products, try changing your search or filters.");
						}
					}
				});
			}
		}
		/*
		*   /INFINITE SCROLL CUSTOM
		*/ 


	});
})(jQuery);


/*
 * jQuery throttle / debounce - v1.1 - 3/7/2010
 * http://benalman.com/projects/jquery-throttle-debounce-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function(b,c){var $=b.jQuery||b.Cowboy||(b.Cowboy={}),a;$.throttle=a=function(e,f,j,i){var h,d=0;if(typeof f!=="boolean"){i=j;j=f;f=c}function g(){var o=this,m=+new Date()-d,n=arguments;function l(){d=+new Date();j.apply(o,n)}function k(){h=c}if(i&&!h){l()}h&&clearTimeout(h);if(i===c&&m>e){l()}else{if(f!==true){h=setTimeout(i?k:l,i===c?e-m:e)}}}if($.guid){g.guid=j.guid=j.guid||$.guid++}return g};$.debounce=function(d,e,f){return f===c?a(d,e,false):a(d,f,e!==false)}})(this);