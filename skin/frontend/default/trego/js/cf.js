function slideUp()
{
    jQuery('#topCartContent:visible').slideUp(500);
    jQuery('.mini-cart-layer').addClass('mini-cart-layer-up');
    jQuery('.mini-cart-layer').removeClass('mini-cart-layer-down');
}

function slideDown()
{
    jQuery('#topCartContent:hidden').slideDown(500);
    /*startTimer()*/ /* optional*/
    jQuery('.mini-cart-layer').addClass('mini-cart-layer-down');
    jQuery('.mini-cart-layer').removeClass('mini-cart-layer-up');
}

function toggleTopCart()
{
    if (!window.cartProcessing) {
        window.cartProcessing = true;
        jQuery('#topCartContent').slideToggle(500, function() {
            window.cartProcessing = false;
        });
        jQuery('.mini-cart-layer').toggleClass('mini-cart-layer-up').toggleClass('mini-cart-layer-down');
    }
}

var timer;
function startTimer()
{
    timer = setTimeout(function(){
        slideUp();
    }, 5000);
}

jQuery(document).ready(function(){
    window.cartProcessing = false;
    jQuery('.mini-cart-layer .top-cart .block-title #cartHeader').click(function(e){
        toggleTopCart();
    });

    // jQuery('.mini-cart-layer .top-cart .block-title #cartHeader').mouseover(function(){
    //     clearTimeout(timer);
    // }).mouseout(function(){
    //     startTimer();
    // });

    // jQuery("#topCartContent").mouseover(function() {
    //     clearTimeout(timer);
    // }).mouseout(function(){
    //     startTimer();
    // });
});

// Custom Accordion menu @author: Landers Optimized --- http://landersoptimized.com
// jQuery(function($) {
//     $('li.level0.parent em.togglenav').click(function(e) {
//       e.preventDefault();
//       $parent = $(this).closest("li.parent");
//       open = /\bopen\b/.test( $parent.attr('class')) ? true : false;
//       if (open) {
//           $parent.removeClass("open").find("ul.level1").slideUp();        
//       }
//       else {
//         $parent.addClass("open").find("ul.level1").slideDown();        
//     }
// });
// });


