/*==============================================================================
=            Check if infinite scroll created any duplicates                   =
==============================================================================*/

var tracker = [],
    dupes   = [];
jQuery(".products-grid >.item").each(function(i,v) {
  var _ref;
  (_ref = jQuery('a.product-image', this).attr('href').split('/'))[_ref.length-1];
  jQuery.inArray(_ref, tracker)>-1 ? dupes.push(_ref) : tracker.push( _ref );
});

/*-----  End of Check if infinite scroll created any duplicates         ------*/

/**
 * TODO: 1.Basic site functionality fixes
 * TODO: Make sure pictures are working on all browsers at all zoom levels
 * TODO: Make sure order emails don’t have typos
 * TODO: Make sure filters and sorting are working
 * TODO: Make sure payment error messages are accurate
 * TODO: Duplicate items showing in grid still
 * TODO: New Items category displaying out of stock items – THIS IS NEW PROJECT
 * TODO: “View product categories” is being covered by the banner
 * TODO: Long description fix (see description of  product #3267)
 */