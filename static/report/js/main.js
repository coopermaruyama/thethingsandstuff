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
    $tfoot = $("#tablefooter"),
    $createButton = $("button#createRow"),
    $filters = [
      $("select#order_by"),
      $("select#order"),
      $("select#per_page")
    ],
    current_page = getParameterByName("page") == "" ? 1 : parseInt(getParameterByName("page")),
    records = {},
    totals_paid = 0,
    total_tax = 0,
    filter = "";
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
  // filter row
  $(document).on("click", "span.filterable", function() {
    filter_column = $(this).data("column");
    filter_value = $(this).text();
    filter = encodeURIComponent("AND `revenue_records`.`"+filter_column+"`='"+filter_value+"'");
    clearRecords();
    fetchRecords();
    $("p.filter-text").html('<strong>Filtering <i>'+filter_column+'</i> by <i>'+filter_value+'</i><br><a href="javascript:location.reload()">remove</a></strong>');
  });
  // lightbox
  var $lightbox = $('#lightbox'); 
  $('[data-target="#lightbox"]').on('click', function(event) {
      $lightbox.find('.close').addClass('hidden');
  });
  $lightbox.on('shown.bs.modal', function (e) {
      $lightbox.find('.close').removeClass('hidden');
  });
  // create record
  $createButton.click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    $form = $(this).closest("form");
    newRecord( $form );
  });
}
function createTotals() {
  $tfoot.html("<tr><th></th><th></th><th></th><th></th><th></th><th><strong>Totals Paid</strong></th><th></th><th><strong>Total Tax</strong></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>");
  $tr = $("<tr></tr>").appendTo( $tfoot );
  totals_paid = 0;
  total_tax = 0;
  $.each(records, function(index,value) {
    totals_paid += parseInt(value.total_paid);
    total_tax += parseInt(value.tax);
  });
  $tr.append('<td></td><td></td><td></td><td></td><td></td>\
                 <td><strong>$'+totals_paid+'</strong></td>\
                 <td></td>\
                 <td><strong>$'+total_tax+'</strong></td>\
                 <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>');
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
function clearRecords() {
  records = {};
  $table.html("");
}
function fetchRecords() {
  $.ajax({
    url: '/static/report/fetch.php?order_by='+$.cookie('order_by')+'&order='+$.cookie('order')+'&per_page='+$.cookie('per_page')+"&page="+current_page+'&filter='+filter,
    type: 'GET',
    dataType: 'text',
    data: {}
  })
  .done(function(data) {
    append = $.cookie("order") == "ASC" ? false : true;
    parsed_data = JSON.parse(data);
    $.each(parsed_data, function(index, val) {
      records[val.id] = new Record(val);
      records[val.id].create( append );
      records[val.id].update();
    });
    createTotals();
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
function newRecord( $form ) {
  var record_type = $('select[name=record_type]', $form).val(),
      customer_name = $('input[name=customer_name]', $form).val(),
      total_paid = $('input[name=total_paid]', $form).val();
  $.ajax({
    url: 'api.php',
    type: 'POST',
    dataType: 'text',
    data: {
      action: "new_record",
      record_type: record_type,
      customer_name: customer_name,
      total_paid: total_paid
    },
  })
  .done(function(data) {
    // console.log("success");
    $("#lightbox").modal("hide")
    var parsed_data = JSON.parse(data),
        record_data = parsed_data["record"],
        record = new Record( record_data );
    record.create(false);
    record.update();
  });
}

// Create the Record Object
var Record = function( data ) { //dataObject is what's returned from the db
  var _this = this;
  this.row_id = data.id;
  this.locked = data.locked == "1" ? true : false;
  this.order_date = data.order_date;
  this.order_type = data.order_type;
  this.customer_name = data.customer_name;
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
  create: function( should_append ) {
    var self = this;
    // create structure
    this.$row                = should_append ? $('<tr id="row-'+this.row_id+'" data-row-id="'+this.row_id+'"></tr>').appendTo( $table ) : $('<tr id="row-'+this.row_id+'" data-row-id="'+this.row_id+'"></tr>').prependTo( $table );
    this.$actions            = $('<td class="actions"></td>').appendTo( this.$row );
    this.$order_date         = $('<td class="order_date"></td>').appendTo( this.$row );
    this.$order_type         = $('<td class="order_type"></td>').appendTo( this.$row );
    this.$customer_name           = $('<td class="customer"></td>').appendTo( this.$row );
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
    this.$actions.on( 'click', "button.editing,button.locked",  function() {
      self.toggleLock()
    });
    this.$row.on( "blur", "input", function() {
      self.saveColumn($(this));
    });
    this.$actions.on( 'click','button.delete', function() {
      var confirm = window.confirm("Are you sure?");
      if (confirm == true) {
        self.destroy();
      }
    });
  },
  update: function() {
    self = this;
    this.$row.find("> td").html('');
    if (this.locked) {
      this.$actions.html('<button type="button" class="btn btn-default locked">Locked</button>');
      this.$order_date.html(this.order_date);
      this.$order_type.html(this.order_type);
      this.$customer_name.html(this.customer_name);
      this.$service_date.html(this.service_date);
      this.$total_paid.html("$"+this.total_paid);
      this.$delivery_address.html(this.shipping_street+'<br><span class="filterable" data-column="shipping_city">'+this.shipping_city+'</span>, <span class="filterable" data-column="shipping_region">'+this.shipping_region+'</span> <span class="filterable" data-column="shipping_postal">'+this.shipping_postal+'</span>');
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
      this.$order_date.html('<input class="form-control" type="text" value="'+this.order_date+'" data-name="order_date"></input>');
      this.$order_type.html('<input class="form-control" type="text" value="'+this.order_type+'" data-name="order_type"></input>');
      this.$customer_name.html('<input class="form-control" type="text" value="'+this.customer_name+'" data-name="customer_name"></input>');
      this.$service_date.html('<input class="form-control" type="text" value="'+this.service_date+'" data-name="service_date"></input>');
      this.$total_paid.html('<input class="form-control" type="text" value="'+this.total_paid+'" data-name="total_paid"></input>');
      this.$delivery_address.html('<input class="form-control" type="text" value="'+this.shipping_street+'" data-name="shipping_street"><br><input class="form-control" type="text" value="'+this.shipping_city+'" data-name="shipping_city"><br><input class="form-control" type="text" value="'+this.shipping_region+'" data-name="shipping_region"><br><input class="form-control" type="text" value="'+this.shipping_postal+'" data-name="shipping_postal">');
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
  destroy: function() {
    var self = this;
    $.ajax({
      url: 'api.php',
      type: 'POST',
      dataType: 'text',
      data: {
        action: "destroy",
        row_id: self.row_id
      },
    })
    .done(function() {
      self.$row.fadeOut();
    });
  },
  saveColumn: function( $inputElem ) {
    var self = this;
    column_name = $inputElem.data("name");
    value = $inputElem.val();
    if (/date/.test(column_name)) {
      value += " " + (new Date()).toTimeString().split(" ")[0];
    };
    $.ajax({
      url: '/static/report/api.php',
      type: 'POST',
      dataType: 'text',
      data: {
        action: 'update_column',
        row_id: self.row_id,
        column_name: column_name,
        new_value: value
      },
    })
    .done(function(data) {
      console.log(data);
      if (data == "success") {
        // console.log("Updated row "+self.row_id+": "+column_name+" to "+value);
        records[self.row_id][column_name] = value;
      };
    });
  },
  toggleLock: function() {
    var self = this;
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
          self['$item'+i].html('<input class="form-control" type="text" value="'+self.items[items_index].item+'" data-name="item'+i+'"></input>');
          self['$item'+i+'_price'].html('<input class="form-control" type="text" value="'+self.items[items_index].price+'" data-name="item'+i+'_price"></input>');
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


