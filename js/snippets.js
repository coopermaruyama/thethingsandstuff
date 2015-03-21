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

