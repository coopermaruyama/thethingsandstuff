	/*
	*
	*
	*/
	jQuery.noConflict();
	jQuery(function($) {
		var myhref,qsbtt;
		if (!window.location.origin) {
		  window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
		}
	// base function
	
	//get IE version
	function ieVersion(){
		var rv = -1; // Return value assumes failure.
		if (navigator.appName == 'Microsoft Internet Explorer'){
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
				rv = parseFloat( RegExp.$1 );
		}
		return rv;
	}

	//read href attr in a tag
	function readHref(){
		var mypath = arguments[0];
		var patt = /\/[^\/]{0,}$/ig;
		if(mypath[mypath.length-1]=="/"){
			mypath = mypath.substring(0,mypath.length-1);
			return (mypath.match(patt)+"/");
		}
		return mypath.match(patt);
	}


	//string trim
	function strTrim(){
		return arguments[0].replace(/^\s+|\s+$/g,"");
	}

	function _qsJnit(){


		
		var selectorObj = arguments[0];
			//selector chon tat ca cac li chua san pham tren luoi
			var listprod = $(selectorObj.itemClass);
			var qsImg;
			var mypath = 'quickview/index/view';
		// alert(EM.Quickview.BASE_URL);
		// if(EM.Quickview.BASE_URL.indexOf('index.php') == -1){
			// mypath = 'quickview/index/view';
		// }else{
			// mypath = 'index.php/quickview/index/view';
		// }
		var baseUrl = EM.Quickview.BASE_URL + mypath;
		
		var _qsHref = "<a id=\"md_quickview_handler\" href=\"#\" style=\"visibility:hidden;position:absolute;top:0;left:0;z-index:999;\"><img style=\"display:none;\" alt=\"quickview\" src=\""+EM.Quickview.QS_IMG+"\" /></a>";
		$(document.body).append(_qsHref);
		
		var qsHandlerImg = $('#md_quickview_handler img');

		$.each(listprod, function(index, value) { 
			var reloadurl = baseUrl;
			
			//get reload url
			myhref = $(value).find(selectorObj.aClass ).first();
			var prodHref = readHref(myhref.attr('href'))[0];
			prodHref[0] == "\/" ? prodHref = prodHref.substring(1,prodHref.length) : prodHref;
			prodHref=strTrim(prodHref);
			
			reloadurl = baseUrl+"/path/"+prodHref;	
			version = ieVersion();	
			if(version < 8.0 && version > -1){
				reloadurl = baseUrl+"/path"+prodHref;
			}
			//end reload url

			
			$(selectorObj.imgClass, this).bind('mouseover', function() {
				var o = $(this).offset();	
				window.saveObj = $(this);	
				theUrl = jQuery(this).closest('a').attr('href');
				if (/catalog\/product\//.test(theUrl)) {
				  re = new RegExp(window.location.origin+".*\/product\/(.*)","i");
				  pageUrl = theUrl.match(re)[1];
				}
				else {
					pageUrl = $(this).closest('a').attr('href').match(/[^\/]+$/);
				}
				targetUrl = window.location.origin+"/"+mypath+"/path/"+pageUrl;
				$('#md_quickview_handler').attr('href',targetUrl);
			});
			$(value).bind('mouseout', function() {
				$('#md_quickview_handler').hide();
			});
			// $(selectorObj.aClass, this).click(function(e) {
			// 	e.preventDefault();
			// 	if ($(this).attr('data-open') != "true") {
			// 		$(this).attr('data-open','true');
			// 		initialHeight = $(this).closest(selectorObj.itemClass).first().height();
			// 		newHeight = initialHeight+500;
			// 		$li = $(this).closest(selectorObj.itemClass);
			// 		$li.css('height',newHeight).attr('data-initial-height',initialHeight);
			// 		$pExpander = $(this).closest(selectorObj.itemClass).find('.product-expander').first()
			// 		$pExpander.load(reloadurl).slideDown();
			// 		$(document).mouseup(function (e) {
			// 			var container = $pExpander;
						
			// 		    if (!container.is(e.target) // if the target of the click isn't the container...
			// 		        && container.has(e.target).length === 0) // ... nor a descendant of the container
			// 		    {
			// 		    	container.slideUp();
			// 		    	$li.animate({height:$li.attr('data-initial-height')});
			// 		    	$li.find(selectorObj.aClass).attr('data-open','false');
			// 		    }
			// 		});
			// 	}
			// });
		});

		//fix bug image disapper when hover
		$('#md_quickview_handler')
		.bind('mouseover', function() {
			$(this).show();
		})
		.bind('click', function() {
			$(this).hide();
		});
		//insert quickview popup

		// $('#md_quickview_handler').fancybox({
		// 		'titleShow'			: false,
		// 		'width'				: EM.Quickview.QS_FRM_WIDTH,
		// 		'height'			: 'auto',//EM.Quickview.QS_FRM_HEIGHT,
		// 		'autoScale'			: false,
		// 		'transitionIn'		: 'none',
		// 		'transitionOut'		: 'none',
		// 		'autoDimensions'	: false,
		// 		'scrolling'     	: 'no',
		// 		'padding' 			:0,
  // 				'margin'			:0,
		// 		'type'				: 'ajax',
		// 		'overlayColor'		: EM.Quickview.OVERLAYCOLOR

		// });


	
	
}

	//end base function
window._qsJinit = _qsJnit({
		itemClass : '.products-grid li.item', //selector for each items in catalog product list,use to insert quickview image
		aClass : 'a.product-image', //selector for each a tag in product items,give us href for one product
		imgClass: '.product-image img' //class for quickview href product-collateral
	});


	_qsJnit({
		itemClass : '.products-grid li.item', //selector for each items in catalog product list,use to insert quickview image
		aClass : 'a.product-image', //selector for each a tag in product items,give us href for one product
		imgClass: '.product-image img' //class for quickview href product-collateral
	});
});


