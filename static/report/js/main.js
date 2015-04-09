// Generated by CoffeeScript 1.8.0

/**
Main JS file for TTAS Reporting System
Author: Cooper Maruyama (cooper@landersoptimized.com)
 */

(function() {
  var $createButton, $daterangepicker, $filters, $table, $tfoot, clearRecords, current_page, date_filter, filter, root, total_tax, totals_paid;

  root = typeof exports !== "undefined" && exports !== null ? exports : this;

  root.init = function() {
    $("#tablecontents").html("");
    setupCookies();
    setupPagination();
  };

  root.initEvents = function() {
    var $lightbox, cookie_daterange, cookie_daterange_end, cookie_daterange_start, last_year, the_ranges, this_year;
    $(document).on("click", "span.filterable", function() {
      var filter_column, filter_value;
      filter_column = $(this).data("column");
      filter_value = $(this).text();
      $.cookie(filter_column, filter_value);
      window.filter = encodeURIComponent("AND `revenue_records`.`" + filter_column + "`='" + filter_value + "'");
      clearRecords();
      fetchRecords();
      $("p.filter-text").html("<strong>Filtering <i>" + filter_column + "</i> by <i>" + filter_value + "</i><br><a href=\"javascript:location.reload()\">remove</a></strong>");
    });
    $lightbox = $("#lightbox");
    $("[data-target=\"#lightbox\"]").on("click", function(event) {
      $lightbox.find(".close").addClass("hidden");
    });
    $lightbox.on("shown.bs.modal", function(e) {
      $lightbox.find(".close").removeClass("hidden");
    });
    $lightbox.find("input.datepicker").datepicker();
    $createButton.click(function(e) {
      var $form;
      e.stopPropagation();
      e.preventDefault();
      $form = $(this).closest("form");
      newRecord($form);
    });
    this_year = moment().format("YYYY");
    last_year = moment().subtract("years", 1).format("YYYY");
    the_ranges = {};
    the_ranges["Last 30 Days"] = [moment().subtract(1, "month"), moment()];
    the_ranges["Q1 " + this_year] = [moment("Jan 1, " + this_year), moment("March 31, " + this_year)];
    the_ranges["Q2 " + this_year] = [moment("April 1, " + this_year), moment("Jun 30, " + this_year)];
    the_ranges["Q3 " + this_year] = [moment("July 1, " + this_year), moment("Sep 30, " + this_year)];
    the_ranges["Q4 " + this_year] = [moment("Oct 1, " + this_year), moment("Dec 31, " + this_year)];
    the_ranges["Q1 " + last_year] = [moment("Jan 1, " + last_year), moment("March 31, " + last_year)];
    the_ranges["Q2 " + last_year] = [moment("April 1, " + last_year), moment("Jun 30, " + last_year)];
    the_ranges["Q3 " + last_year] = [moment("July 1, " + last_year), moment("Sep 30, " + last_year)];
    the_ranges["Q4 " + last_year] = [moment("Oct 1, " + last_year), moment("Dec 31, " + last_year)];
    $("input[name=daterange]").daterangepicker({
      format: "YYYY-MM-DD",
      ranges: the_ranges,
      startDate: moment().subtract(1, "month").format("YYYY-MM-DD"),
      endDate: moment().format("YYYY-MM-DD")
    }, function(start, end, label) {
      var date_filter, date_range;
      date_range = start.format("YYYY-MM-DD") + " 00:00:00' AND '" + end.format("YYYY-MM-DD") + " 0:00:00'";
      date_filter = encodeURIComponent("AND `revenue_records`.`order_date` BETWEEN '" + date_range);
      $.cookie("date_range", date_filter);
      clearRecords();
      fetchRecords();
    });
    cookie_daterange = $.cookie('date_range');
    if (cookie_daterange != null) {
      cookie_daterange_start = cookie_daterange.match(/[0-9]{4}-[0-9]{2}-[0-9]{2}/g)[0];
      cookie_daterange_end = cookie_daterange.match(/[0-9]{4}-[0-9]{2}-[0-9]{2}/g)[1];
      $daterangepicker.val("" + cookie_daterange_start + " - " + cookie_daterange_end);
    }
  };

  root.createTotals = function() {
    var $tr, total_tax, totals_paid;
    $tfoot.html("<tr><th></th><th></th><th></th><th></th><th></th><th><strong>Totals Paid</strong></th><th></th><th><strong>Total Tax</strong></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>");
    $tr = $("<tr></tr>").appendTo($tfoot);
    totals_paid = 0;
    total_tax = 0;
    $.each(records, function(index, value) {
      totals_paid += parseFloat(value.total_paid);
      total_tax += parseFloat(value.tax);
    });
    $tr.append("<td></td><td></td><td></td><td></td><td></td>                 <td><strong>$" + totals_paid.toFixed(2) + "</strong></td>                 <td></td>                 <td><strong>$" + total_tax.toFixed(2) + "</strong></td>                 <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>");
  };

  root.setupCookies = function() {
    $.each(["per_page", "order", "order_by"], function(index, val) {
      if (typeof $.cookie(val) === "undefined") {
        $.cookie(val, $("select#" + val).val(), {
          expires: 30
        });
      } else {
        $("select#" + val).val($.cookie(val));
      }
      $("select#" + val).change(function(event) {
        $.cookie($(this).attr("name"), $(this).val(), {
          expires: 30
        });
        clearRecords();
        fetchRecords();
      });
    });
  };

  root.setupPagination = function() {
    if (getParameterByName("page") === "" || parseInt(getParameterByName("page")) === 1) {
      $("li#prev_page").addClass("disabled");
      return $("li#next_page a").attr("href", updateQueryStringParameter(window.location.href, "page", "2"));
    } else if (parseInt(getParameterByName("page")) > 1) {
      $("li#prev_page a").attr("href", updateQueryStringParameter(window.location.href, "page", parseInt(getParameterByName("page")) - 1));
      return $("li#next_page a").attr("href", updateQueryStringParameter(window.location.href, "page", parseInt(getParameterByName("page")) + 1));
    }
  };

  clearRecords = function() {
    window.records = {};
    window.totals_paid = 0;
    window.total_tax = 0;
    $table.html("");
  };

  root.fetchRecords = function() {
    var decoded_daterange, decoded_filter, encoded_filter;
    $(".table-responsive").append("<div id=\"loading\"><div id=\"loading-text\"><img src=\"img/ajax-loader-white.gif\"> Loading...</div></div>");
    window.filter = window.filter != null ? window.filter : "";
    decoded_daterange = decodeURIComponent($.cookie('date_range'));
    decoded_filter = decodeURIComponent(window.filter);
    encoded_filter = $.cookie("date_range") != null ? "" + decoded_filter + " " + decoded_daterange : window.filter;
    $.ajax({
      url: "/static/report/fetch.php?order_by=" + $.cookie("order_by") + "&order=" + $.cookie("order") + "&per_page=" + $.cookie("per_page") + "&page=" + current_page + "&filter=" + encoded_filter,
      type: "GET",
      dataType: "text",
      data: {}
    }).done(function(data) {
      var append, is_last_page, parsed_data, records_count;
      append = ($.cookie("order") === "ASC" ? false : true);
      parsed_data = JSON.parse(data);
      $.each(parsed_data, function(index, val) {
        window.records[val.id] = new Record(val);
        window.records[val.id].create(append);
        window.records[val.id].update();
      });
      createTotals();
      $("#loading").remove();
      records_count = parsed_data.length;
      is_last_page = false;
      if (records_count > 0) {
        if (records_count < parseInt($.cookie("per_page"))) {
          is_last_page = true;
        }
        if (is_last_page) {
          $("li#next_page").addClass("disabled");
        }
      }
    });
  };

  root.updateQueryStringParameter = function(uri, key, value) {
    var re, separator;
    re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    separator = (uri.indexOf("?") !== -1 ? "&" : "?");
    if (uri.match(re)) {
      return uri.replace(re, "$1" + key + "=" + value + "$2");
    } else {
      return uri + separator + key + "=" + value;
    }
  };

  root.getParameterByName = function(name) {
    var regex, results;
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
    results = regex.exec(location.search);
    if (results == null) {
      return "";
    } else {
      return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
  };

  root.newRecord = function($form) {
    var customer_name, record_type, total_paid;
    record_type = $("select[name=record_type]", $form).val();
    customer_name = $("input[name=customer_name]", $form).val();
    total_paid = $("input[name=total_paid]", $form).val();
    $.ajax({
      url: "api.php",
      type: "POST",
      dataType: "text",
      data: {
        action: "new_record",
        record_type: record_type,
        customer_name: customer_name,
        total_paid: total_paid
      }
    }).done(function(data) {
      var parsed_data, record, record_data;
      $("#lightbox input").val("");
      $("#lightbox").modal("hide");
      parsed_data = JSON.parse(data);
      record_data = parsed_data["record"];
      record_data.items = {};
      record = new Record(record_data);
      record.create(false);
      record.update();
      window.records[record.row_id] = record;
      window.pocket = record;
      console.log(record);
    });
  };

  root.Record = function(data) {
    var _this;
    _this = this;
    this.row_id = data.id;
    this.locked = (data.locked === "1" ? true : false);
    this.order_date = data.order_date;
    this.order_type = data.order_type;
    this.customer_name = data.customer_name;
    this.service_date = data.service_date;
    this.total_paid = data.total_paid;
    this.tax = parseFloat(data.tax).toFixed(2);
    this.shipping_street = data.shipping_street;
    this.shipping_city = data.shipping_city;
    this.shipping_region = data.shipping_region;
    this.shipping_postal = data.shipping_postal;
    this.items = data.items;
  };

  Record.prototype = {
    create: function(should_append) {
      var self;
      self = this;
      this.$row = (should_append ? $("<tr id=\"row-" + this.row_id + "\" data-row-id=\"" + this.row_id + "\"></tr>").appendTo($table) : $("<tr id=\"row-" + this.row_id + "\" data-row-id=\"" + this.row_id + "\"></tr>").prependTo($table));
      this.$actions = $("<td class=\"actions\"></td>").appendTo(this.$row);
      this.$order_date = $("<td class=\"order_date\"></td>").appendTo(this.$row);
      this.$order_type = $("<td class=\"order_type\"></td>").appendTo(this.$row);
      this.$customer_name = $("<td class=\"customer\"></td>").appendTo(this.$row);
      this.$service_date = $("<td class=\"service_date\"></td>").appendTo(this.$row);
      this.$total_paid = $("<td class=\"total_paid\"></td>").appendTo(this.$row);
      this.$delivery_address = $("<td class=\"delivery_address\"></td>").appendTo(this.$row);
      this.$tax = $("<td class=\"tax\"></td>").appendTo(this.$row);
      this.$item1 = $("<td class=\"item1\"></td>").appendTo(this.$row);
      this.$item1_price = $("<td class=\"item1_price\"></td>").appendTo(this.$row);
      this.$item2 = $("<td class=\"item2\"></td>").appendTo(this.$row);
      this.$item2_price = $("<td class=\"item2_price\"></td>").appendTo(this.$row);
      this.$item3 = $("<td class=\"item3\"></td>").appendTo(this.$row);
      this.$item3_price = $("<td class=\"item3_price\"></td>").appendTo(this.$row);
      this.$item4 = $("<td class=\"item4\"></td>").appendTo(this.$row);
      this.$item4_price = $("<td class=\"item4_price\"></td>").appendTo(this.$row);
      this.$item5 = $("<td class=\"item5\"></td>").appendTo(this.$row);
      this.$item5_price = $("<td class=\"item5_price\"></td>").appendTo(this.$row);
      this.$cells = [this.$row, this.$actions, this.$order_date, this.$order_type, this.$customer, this.$service_date, this.$total_paid, this.$delivery_address, this.$tax, this.$item1, this.$item1_price, this.$item2, this.$item2_price, this.$item3, this.$item3_price, this.$item4, this.$item4_price, this.$item5, this.$item5_price];
      this.$actions.on("click", "button.editing,button.locked", function() {
        self.toggleLock();
      });
      this.$row.on("change", "input", function() {
        self.saveColumn($(this));
      });
      this.$actions.on("click", "button.delete", function() {
        var confirm;
        confirm = window.confirm("Are you sure?");
        if (confirm === true) {
          self.destroy();
        }
      });
    },
    update: function() {
      var self;
      self = this;
      this.$row.find("> td").html("");
      self.total_paid = 0;
      $.each(self.items, function(i, item) {
        self.total_paid += parseFloat(item.price);
      });
      this.$total_paid.html("$" + self.total_paid);
      if (this.locked) {
        this.$actions.html("<button type=\"button\" class=\"btn btn-default locked\">Locked</button>");
        this.$order_date.html(this.order_date);
        this.$order_type.html(this.order_type);
        this.$customer_name.html(this.customer_name);
        this.$service_date.html(this.service_date);
        this.$delivery_address.html(this.shipping_street + "<br><span class=\"filterable\" data-column=\"shipping_city\">" + this.shipping_city + "</span>, <span class=\"filterable\" data-column=\"shipping_region\">" + this.shipping_region + "</span> <span class=\"filterable\" data-column=\"shipping_postal\">" + this.shipping_postal + "</span>");
        this.$tax.html("$" + this.tax);
        this.addItems();
        $("button.locked", this.$actions).hover((function() {
          $(this).toggleClass("btn-default").toggleClass("btn-primary").text("Unlock");
        }), function() {
          $(this).toggleClass("btn-default").toggleClass("btn-primary").text("Locked");
        });
      } else {
        this.$actions.html("<button type=\"button\" class=\"btn btn-warning editing\">Editing</button><br><button type=\"button\" class=\"btn btn-danger delete\">Delete</button>");
        this.$order_date.html("<input class=\"form-control\" type=\"text\" value=\"" + this.order_date + "\" data-name=\"order_date\"></input>");
        this.$order_type.html(this.order_type);
        this.$customer_name.html("<input class=\"form-control\" type=\"text\" value=\"" + this.customer_name + "\" data-name=\"customer_name\"></input>");
        this.$service_date.html("<input class=\"form-control\" type=\"text\" value=\"" + this.service_date + "\" data-name=\"service_date\"></input>");
        this.$delivery_address.html("<input class=\"form-control\" type=\"text\" value=\"" + this.shipping_street + "\" data-name=\"shipping_street\" placeholder=\"Street\"><br><input class=\"form-control\" type=\"text\" value=\"" + this.shipping_city + "\" data-name=\"shipping_city\" placeholder=\"City\"><br><input class=\"form-control\" type=\"text\" value=\"" + this.shipping_region + "\" data-name=\"shipping_region\" placeholder=\"State/Region\"><br><input class=\"form-control\" type=\"text\" value=\"" + this.shipping_postal + "\" data-name=\"shipping_postal\" placeholder=\"Postal Code\">");
        this.$tax.html("$" + this.tax);
        this.addItems();
        $("input", this.$order_date).datepicker();
        $("input", this.$service_date).datepicker();
        $("button.editing", this.$actions).hover((function() {
          $(this).text("Save");
        }), function() {
          $(this).text("Editing");
        });
      }
    },
    destroy: function() {
      var self;
      self = this;
      $.ajax({
        url: "api.php",
        type: "POST",
        dataType: "text",
        data: {
          action: "destroy",
          row_id: self.row_id
        }
      }).done(function() {
        self.$row.fadeOut();
      });
    },
    saveColumn: function($inputElem) {
      var column_name, rev_record_id, self, value;
      self = this;
      column_name = $inputElem.data("name");
      value = $inputElem.val();
      if (/date/.test(column_name)) {
        console.log(value);
        value = moment(value).format("YYYY-MM-DD");
      }
      rev_record_id = /^item[0-9]/.test(column_name) ? $inputElem.data("id") : "";
      $.ajax({
        url: "/static/report/api.php",
        type: "POST",
        dataType: "text",
        data: {
          action: "update_column",
          row_id: self.row_id,
          column_name: column_name,
          new_value: value,
          rr_id: rev_record_id
        }
      }).done(function(data) {
        console.log(data);
        if (data === "success") {
          self.fetch(function() {
            self.update();
            createTotals();
          });
        }
      });
    },
    toggleLock: function() {
      var action, row_id, self;
      self = this;
      row_id = self.row_id;
      action = (this.locked ? "unlock_row" : "lock_row");
      $.ajax({
        url: "/static/report/api.php",
        type: "POST",
        dataType: "text",
        data: {
          action: action,
          row_id: row_id
        }
      }).done(function(data) {
        if (data === "success") {
          self.locked = !self.locked;
          self.update();
        }
      });
    },
    addItems: function() {
      var i, items_index, self;
      self = this;
      i = 1;
      while (i <= 5) {
        items_index = i - 1;
        if (typeof self.items[items_index] !== "undefined") {
          if (self.locked) {
            self["$item" + i].text(self.items[items_index].item);
            self[("$item" + i) + "_price"].text("$" + self.items[items_index].price);
          } else {
            self["$item" + i].html("<input class='form-control' type='text' value='" + self.items[items_index].item + "' data-name='item" + i + "' data-id='" + self.items[items_index].id + "''></input>");
            self[("$item" + i) + "_price"].html("<input class='form-control' type='text' value='" + self.items[items_index].price + "' data-name='item" + i + "_price' data-id='" + self.items[items_index].id + "''></input>");
          }
        } else {
          if (!self.locked) {
            self["$item" + i].html("<input class=\"form-control\" type=\"text\" value=\"\" data-name=\"item" + i + "\"></input>");
            self["$item" + i + "_price"].html("<input class=\"form-control\" type=\"text\" value=\"\" data-name=\"item" + i + "_price\"></input>");
          }
        }
        i++;
      }
    },
    fetch: function(callback) {
      var self;
      self = this;
      $.ajax({
        url: "/static/report/findOne.php",
        type: "POST",
        dataType: "text",
        data: {
          record_id: self.row_id
        }
      }).done(function(response) {
        var data;
        data = JSON.parse(response);
        self.row_id = data.id;
        self.locked = (data.locked === "1" ? true : false);
        self.order_date = data.order_date;
        self.order_type = data.order_type;
        self.customer_name = data.customer_name;
        self.service_date = data.service_date;
        self.total_paid = data.total_paid;
        self.tax = parseFloat(data.tax).toFixed(2);
        self.shipping_street = data.shipping_street;
        self.shipping_city = data.shipping_city;
        self.shipping_region = data.shipping_region;
        self.shipping_postal = data.shipping_postal;
        self.items = data.items;
        callback && callback();
      });
    }
  };

  $table = $("#tablecontents");

  $tfoot = $("#tablefooter");

  $daterangepicker = $("#input-daterange");

  $createButton = $("button#createRow");

  $filters = [$("select#order_by"), $("select#order"), $("select#per_page")];

  current_page = (getParameterByName("page") === "" ? 1 : parseInt(getParameterByName("page")));

  this.records = {};

  totals_paid = 0;

  total_tax = 0;

  filter = "";

  date_filter = "";

  $(function() {
    init();
    fetchRecords();
    initEvents();
  });

}).call(this);