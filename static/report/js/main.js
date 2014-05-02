/****
**
*
*  Main JS file for TTAS Reporting System
*  Author: Cooper Maruyama (cooper@landersoptimized.com)
*
**
****/
// config
var $table = $('#tablecontents'), //Main table container for rows
    $filters = [
      $("select#order_by"),
      $("select#order"),
      $("select#per_page")
    ],
    current_page = getParameterByName("page") == "" ? 1 : parseInt(getParameterByName("page")),
    records = {};
// Intialize
$(function() {
  init();
  fetchRecords();
  initEvents();
});
// 
function init() {
  $("#tablecontents").html('');
  setupCookies();
  setupPagination();
}
function initEvents() {
  $table.delegate( "input", "blur", function() {
    console.log($(this));
    name = $(this).data("name");
    value = $(this).val();
    row_id = $(this).closest("tr").data("row-id");
    saveColumn(row_id, name, value)
  });
}
function setupCookies() {
  $.each(['per_page','order','order_by'], function(index, val) {
    if (typeof $.cookie(val) == "undefined") {
     $.cookie(val, $("select#"+val).val(), { expires: 30 });
    } else {
     $("select#"+val).val($.cookie(val));
    };
    $("select#"+val).change(function(event) {
      $.cookie($(this).attr('name'),$(this).val(), { expires: 30 });
      location.reload();
    });
  });
}
function setupPagination() {
  // pager
  if (getParameterByName("page") == "" || parseInt(getParameterByName("page")) == 1 ) {
    $("li#prev_page").addClass("disabled");
    $("li#next_page a").attr('href',updateQueryStringParameter(window.location.href,"page", "2") );
  } else if (parseInt(getParameterByName("page")) > 1) {
    $("li#prev_page a").attr('href',updateQueryStringParameter(window.location.href,"page", (parseInt(getParameterByName("page")) - 1) ) );
    $("li#next_page a").attr('href',updateQueryStringParameter(window.location.href,"page", (parseInt(getParameterByName("page")) + 1) ) );
  };
}
function fetchRecords() {
  $.ajax({
    url: '/static/report/fetch.php?order_by='+$.cookie('order_by')+'&order='+$.cookie('order')+'&per_page='+$.cookie('per_page')+"&page="+current_page,
    type: 'GET',
    dataType: 'text',
    data: {}
  })
  .done(function(data) {
    parsed_data = JSON.parse(data);
    $.each(parsed_data, function(index, val) {
      records[val.id] = new Record(val);
      records[val.id].create();
      records[val.id].update();
    });
  });
}
function saveColumn( row_id, name, value ) {
  column_name = name;
  value = value;
  if (/date/.test(column_name)) {
    value += " " + (new Date()).toTimeString().split(" ")[0];
  };
  $.ajax({
    url: '/static/report/api.php',
    type: 'POST',
    dataType: 'text',
    data: {
      action: 'update_column',
      row_id: row_id,
      column_name: column_name,
      new_value: value
    },
  })
  .done(function(data) {
    if (data == "success") {
      console.log("Updated row "+row_id+": "+column_name+" to "+value);
    };
  });
}
function updateQueryStringParameter(uri, key, value) {
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, '$1' + key + "=" + value + '$2');
  }
  else {
    return uri + separator + key + "=" + value;
  }
}
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

// Create the Record Object
var Record = function( data ) { //dataObject is what's returned from the db
  var _this = this;
  this.row_id = data.id;
  this.locked = data.locked == "1" ? true : false;
  this.order_date = data.order_date;
  this.order_type = data.order_type;
  this.customer = data.customer_name;
  this.service_date = data.service_date;
  this.total_paid = data.total_paid;
  this.shipping_street = data.shipping_street;
  this.shipping_city = data.shipping_city;
  this.shipping_region = data.shipping_region;
  this.shipping_postal = data.shipping_postal;
  this.items = data.items; //object containing items
  // initializers
  // this.create();
  // this.update();
}

Record.prototype = {
  create: function() {
    self = this;
    // create structure
    this.$row                = $('<tr id="row-'+this.row_id+'" data-row-id="'+this.row_id+'"></tr>').appendTo( $table );
    this.$actions            = $('<td class="actions"></td>').appendTo( this.$row );
    this.$order_date         = $('<td class="order_date"></td>').appendTo( this.$row );
    this.$order_type         = $('<td class="order_type"></td>').appendTo( this.$row );
    this.$customer           = $('<td class="customer"></td>').appendTo( this.$row );
    this.$service_date       = $('<td class="service_date"></td>').appendTo( this.$row );
    this.$total_paid         = $('<td class="total_paid"></td>').appendTo( this.$row );
    this.$delivery_address   = $('<td class="delivery_address"></td>').appendTo( this.$row );
    this.$tax                = $('<td class="tax"></td>').appendTo( this.$row );
    this.$item1              = $('<td class="item1"></td>').appendTo( this.$row );
    this.$item1_price        = $('<td class="item1_price"></td>').appendTo( this.$row );
    this.$item2              = $('<td class="item2"></td>').appendTo( this.$row );
    this.$item2_price        = $('<td class="item2_price"></td>').appendTo( this.$row );
    this.$item3              = $('<td class="item3"></td>').appendTo( this.$row );
    this.$item3_price        = $('<td class="item3_price"></td>').appendTo( this.$row );
    this.$item4              = $('<td class="item4"></td>').appendTo( this.$row );
    this.$item4_price        = $('<td class="item4_price"></td>').appendTo( this.$row );
    this.$item5              = $('<td class="item5"></td>').appendTo( this.$row );
    this.$item5_price        = $('<td class="item5_price"></td>').appendTo( this.$row );
    this.$cells = [this.$row, this.$actions, this.$order_date, this.$order_type, this.$customer, this.$service_date, this.$total_paid, this.$delivery_address, this.$tax, this.$item1, this.$item1_price, this.$item2, this.$item2_price, this.$item3, this.$item3_price, this.$item4, this.$item4_price, this.$item5, this.$item5_price]
    // bind events
    $(this.$actions).delegate( "button.editing,button.locked", 'click', $.proxy( self.toggleLock, self ));
  },
  update: function() {
    self = this;
    this.$row.find("> td").html('');
    if (this.locked) {
      this.$actions.html('<button type="button" class="btn btn-default locked">Locked</button>');
      this.$order_date.html(this.order_date);
      this.$order_type.html(this.order_type);
      this.$customer.html(this.customer);
      this.$service_date.html(this.service_date);
      this.$total_paid.html(this.total_paid);
      this.$delivery_address.html(this.shipping_street+'<br>'+this.shipping_city+', '+this.shipping_region+' '+this.shipping_postal);
      this.$tax.html(this.calculateTax());
      this.addItems();
      // events
      $("button.locked", this.$actions).hover(function() {
        $(this).toggleClass("btn-default").toggleClass("btn-primary").text("Unlock")
      }, function() {
        $(this).toggleClass("btn-default").toggleClass("btn-primary").text("Locked")
      });
    } else {
      this.$actions.html('<button type="button" class="btn btn-warning editing">Editing</button><br><button type="button" class="btn btn-danger delete">Delete</button>');
      this.$order_date.html('<input type="text" value="'+this.order_date+'" data-name="order_date"></input>');
      this.$order_type.html('<input type="text" value="'+this.order_type+'" data-name="order_type"></input>');
      this.$customer.html('<input type="text" value="'+this.customer+'" data-name="customer"></input>');
      this.$service_date.html('<input type="text" value="'+this.service_date+'" data-name="service_date"></input>');
      this.$total_paid.html('<input type="text" value="'+this.total_paid+'" data-name="total_paid"></input>');
      this.$delivery_address.html('<input type="text" value="'+this.shipping_street+'" data-name="shipping_street"><br><input type="text" value="'+this.shipping_city+'" data-name="shipping_city"><br><input type="text" value="'+this.shipping_region+'" data-name="shipping_region"><br><input type="text" value="'+this.shipping_postal+'" data-name="shipping_postal">');
      this.$tax.html(this.calculateTax());
      this.addItems();
      // events
      $("input", this.$order_date).datepicker();
      $("input", this.$service_date).datepicker();
      $("button.editing", this.$actions).hover(function() {
        $(this).text("Save")
      }, function() {
        $(this).text("Editing")
      });
    }
  },
  toggleLock: function() {
    self = this;
    row_id = self.row_id;
    action = this.locked ? "unlock_row" : "lock_row";
    $.ajax({
      url: '/static/report/api.php',
      type: 'POST',
      dataType: 'text',
      data: {
        action: action,
        row_id: row_id
      },
    })
    .done(function(data) {
      if (data == "success") {
        self.locked = !self.locked;
        self.update();
      }
    });
  },
  calculateTax: function() {
    // TO DO
    return "(not set)";
  },
  addItems: function() { 
    self = this;
    for (var i = 1; i <= 5; i++) {
      items_index = i - 1;
      if (typeof self.items[items_index] != "undefined") {
        if (self.locked) {
          self['$item'+i].text(self.items[items_index].item);
          self['$item'+i+'_price'].text('$'+self.items[items_index].price);
        } else {
          self['$item'+i].html('<input type="text" value="'+self.items[items_index].item+'" data-name="item'+i+'"></input>');
          self['$item'+i+'_price'].html('<input type="text" value="'+self.items[items_index].price+'" data-name="item'+i+'_price"></input>');
        }
      }
    }
  }
}


































   
    



// $("#tablecontents").delegate("button.editing", 'click', function(e) {
  
//   // update the data
//   $row = $(this).closest("tr");
//   $row.find("input.save").each(function() {
//     column_name = $(this).attr("name");
//     new_value = $(this).val();
//     $.ajax({
//       url: '/static/report/api.php',
//       type: 'POST',
//       dataType: 'text',
//       data: {
//         action: 'update_column',
//         row_id: row_id,
//         column_name: column_name,
//         new_value: new_value
//       },
//     })
//     .done(function(data) {
//       if (data == "success") {
//         $row.find("input").each(function() {
//           value = $(this).val();
//           $(this).replaceWith(value);
//         })
//       };
//     });
//   });
// });

// datepicker


