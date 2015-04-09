jQuery(document).ready(function(){
    if(jQuery(".header .form-search input.input-text#search").val() != 'Search...'){
        jQuery("#mob_search").css('display', 'none');
        jQuery(".header .form-search input.input-text#search").css('display', 'block');
        jQuery(".header .form-search button.button").css('display', 'block');
    }
    jQuery("#mob_search").click(function(){
        jQuery(this).css('display', 'none');
        jQuery(".header .form-search input.input-text#search").css('display', 'block');
        jQuery(".header .form-search button.button").css('display', 'block');
        jQuery(".header .form-search input.input-text#search").focus();
    });
    jQuery(".nav-container #mobnav").click(function(){
        if (jQuery(this).parent().hasClass('expanded')){
            jQuery(this).parent().removeClass('expanded');
            jQuery(this).parent().children('#nav').fadeOut();
        } else {
            jQuery(this).parent().addClass('expanded');
            jQuery(this).parent().children('#nav').fadeIn();
        }
        jQuery("#nav ul.level1").each(function(){
            var scrolltop = 0;
            if(jQuery('html').scrollTop()>1)
                scrolltop = jQuery('html').scrollTop();
            else if(jQuery('body').scrollTop()>1)
                scrolltop = jQuery('body').scrollTop();
            jQuery(this).css('top', jQuery(this).parent().offset().top-scrolltop);
        });
    });
    if(jQuery(window).width()<=1024 && jQuery(window).width()>=768 && jQuery('#nav').height()>40)
        jQuery('.header .header-bground').addClass('menu-expanded');
    jQuery(window).resize(function(){
        if(jQuery(window).width()<=1024 && jQuery(window).width()>=768 && jQuery('#nav').height()>40)
            jQuery('.header .header-bground').addClass('menu-expanded');
    })
/*
    if(jQuery(window).height()>550)
        jQuery('.box-scroll').css('height',(jQuery(window).height()-400)+"px");
    else
        jQuery('.box-scroll').css('height',"150px");
    
    jQuery(window).resize(function(){
        if(jQuery(window).height()>550)
            jQuery('.box-scroll').css('height',(jQuery(window).height()-400)+"px");
        else
            jQuery('.box-scroll').css('height',"150px");
        if(jQuery(window).width() <= 1024)
        {
            jQuery(".box-scroll").getNiceScroll().remove();
        } else {
            jQuery('.box-scroll').niceScroll({zindex : 51, objfixed: true});
        }
    });
*/
});