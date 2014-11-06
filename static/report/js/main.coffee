###*
Main JS file for TTAS Reporting System
Author: Cooper Maruyama (cooper@landersoptimized.com)
###

# config
#Main table container for rows

# Intialize
root = exports ? this

root.init = ->
	$("#tablecontents").html ""
	setupCookies()
	setupPagination()
	return
root.initEvents = ->
	
	# filter row
	$(document).on "click", "span.filterable", ->
		filter_column = $(this).data("column")
		filter_value = $(this).text()
		filter = encodeURIComponent("AND `revenue_records`.`" + filter_column + "`='" + filter_value + "'")
		clearRecords()
		fetchRecords()
		$("p.filter-text").html "<strong>Filtering <i>" + filter_column + "</i> by <i>" + filter_value + "</i><br><a href=\"javascript:location.reload()\">remove</a></strong>"
		return

	
	# lightbox
	$lightbox = $("#lightbox")
	$("[data-target=\"#lightbox\"]").on "click", (event) ->
		$lightbox.find(".close").addClass "hidden"
		return

	$lightbox.on "shown.bs.modal", (e) ->
		$lightbox.find(".close").removeClass "hidden"
		return

	$lightbox.find("input.datepicker").datepicker()
	
	# create record
	$createButton.click (e) ->
		e.stopPropagation()
		e.preventDefault()
		$form = $(this).closest("form")
		newRecord $form
		return

	
	# filter date
	this_year = moment().format("YYYY")
	last_year = moment().subtract("years", 1).format("YYYY")
	the_ranges = {}

	the_ranges["Q1 " + this_year] = [
		moment("Jan 1, " + this_year)
		moment("March 31, " + this_year)
	]
	the_ranges["Q2 " + this_year] = [
		moment("April 1, " + this_year)
		moment("Jun 30, " + this_year)
	]
	the_ranges["Q3 " + this_year] = [
		moment("July 1, " + this_year)
		moment("Sep 30, " + this_year)
	]
	the_ranges["Q4 " + this_year] = [
		moment("Oct 1, " + this_year)
		moment("Dec 31, " + this_year)
	]
	the_ranges["Q1 " + last_year] = [
		moment("Jan 1, " + last_year)
		moment("March 31, " + last_year)
	]
	the_ranges["Q2 " + last_year] = [
		moment("April 1, " + last_year)
		moment("Jun 30, " + last_year)
	]
	the_ranges["Q3 " + last_year] = [
		moment("July 1, " + last_year)
		moment("Sep 30, " + last_year)
	]
	the_ranges["Q4 " + last_year] = [
		moment("Oct 1, " + last_year)
		moment("Dec 31, " + last_year)
	]
	$("input[name=daterange]").daterangepicker
		format: "YYYY-MM-DD"
		ranges: the_ranges
	, (start, end, label) ->
		date_filter = encodeURIComponent("AND `revenue_records`.`order_date` BETWEEN '" + start.format("YYYY-MM-DD") + " 00:00:00' AND '" + end.format("YYYY-MM-DD") + " 0:00:00'")
		clearRecords()
		fetchRecords()
		return

	return

# update button
# $("button#update-magento").on("click", function(event) {
#   event.preventDefault();
#   $(this).attr('disabled','disabled').html('<img src="img/ajax-loader-white.gif" alt="" /> Importing...');
#   $.ajax({
#     url: '/static/report/update.php?token=52eb285a49e57',
#     type: 'GET',
#     dataType: 'text',
#   })
#   .done(function() {
#     $("#update-magento").removeAttr("disabled").text("Update Magento Orders");
#   });
# });
root.createTotals = ->
	$tfoot.html "<tr><th></th><th></th><th></th><th></th><th></th><th><strong>Totals Paid</strong></th><th></th><th><strong>Total Tax</strong></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>"
	$tr = $("<tr></tr>").appendTo($tfoot)
	totals_paid = 0
	total_tax = 0
	$.each records, (index, value) ->
		totals_paid += parseFloat(value.total_paid)
		total_tax += parseFloat(value.tax)
		return

	$tr.append "<td></td><td></td><td></td><td></td><td></td>                 <td><strong>$" + totals_paid.toFixed(2) + "</strong></td>                 <td></td>                 <td><strong>$" + total_tax.toFixed(2) + "</strong></td>                 <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>"
	return
root.setupCookies = ->
	$.each [
		"per_page"
		"order"
		"order_by"
	], (index, val) ->
		if typeof $.cookie(val) is "undefined"
			$.cookie val, $("select#" + val).val(),
				expires: 30

		else
			$("select#" + val).val $.cookie(val)
		$("select#" + val).change (event) ->
			$.cookie $(this).attr("name"), $(this).val(),
				expires: 30

			location.reload()
			return

		return

	return
root.setupPagination = ->
	
	# pager
	if getParameterByName("page") is "" or parseInt(getParameterByName("page")) is 1
		$("li#prev_page").addClass "disabled"
		$("li#next_page a").attr "href", updateQueryStringParameter(window.location.href, "page", "2")
	else if parseInt(getParameterByName("page")) > 1
		$("li#prev_page a").attr "href", updateQueryStringParameter(window.location.href, "page", (parseInt(getParameterByName("page")) - 1))
		$("li#next_page a").attr "href", updateQueryStringParameter(window.location.href, "page", (parseInt(getParameterByName("page")) + 1))
	return
clearRecords = ->
	window.records = {}
	window.totals_paid = 0
	window.total_tax = 0
	$table.html ""
	return
root.fetchRecords = ->
	$(".table-responsive").append "<div id=\"loading\"><div id=\"loading-text\"><img src=\"img/ajax-loader-white.gif\"> Loading...</div></div>"
	$.ajax(
		url: "/static/report/fetch.php?order_by=" + $.cookie("order_by") + "&order=" + $.cookie("order") + "&per_page=" + $.cookie("per_page") + "&page=" + current_page + "&filter=" + filter + " " + date_filter
		type: "GET"
		dataType: "text"
		data: {}
	).done (data) ->
		append = (if $.cookie("order") is "ASC" then false else true)
		parsed_data = JSON.parse(data)
		$.each parsed_data, (index, val) ->
			window.records[val.id] = new Record(val)
			window.records[val.id].create append
			window.records[val.id].update()
			return

		createTotals()
		$("#loading").remove()
		return

	return
root.updateQueryStringParameter = (uri, key, value) ->
	re = new RegExp("([?&])" + key + "=.*?(&|$)", "i")
	separator = (if uri.indexOf("?") isnt -1 then "&" else "?")
	if uri.match(re)
		uri.replace re, "$1" + key + "=" + value + "$2"
	else
		uri + separator + key + "=" + value
root.getParameterByName = (name) ->
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]")
	regex = new RegExp("[\\?&]" + name + "=([^&#]*)")
	results = regex.exec(location.search)
	(if not results? then "" else decodeURIComponent(results[1].replace(/\+/g, " ")))
root.newRecord = ($form) ->
	record_type = $("select[name=record_type]", $form).val()
	customer_name = $("input[name=customer_name]", $form).val()
	total_paid = $("input[name=total_paid]", $form).val()
	$.ajax(
		url: "api.php"
		type: "POST"
		dataType: "text"
		data:
			action: "new_record"
			record_type: record_type
			customer_name: customer_name
			total_paid: total_paid
	).done (data) ->
		
		# console.log("success");
		$("#lightbox input").val ""
		$("#lightbox").modal "hide"
		parsed_data = JSON.parse(data)
		record_data = parsed_data["record"]
		record_data.items = {}
		record = new Record(record_data)
		record.create false
		record.update()
		window.records[record.row_id] = record
		window.pocket = record
		console.log record
		return

	return

# Create the Record Object
root.Record = (data) -> #dataObject is what's returned from the db
	_this = this
	@row_id = data.id
	@locked = (if data.locked is "1" then true else false)
	@order_date = data.order_date
	@order_type = data.order_type
	@customer_name = data.customer_name
	@service_date = data.service_date
	@total_paid = data.total_paid
	@tax = parseFloat(data.tax).toFixed(2)
	@shipping_street = data.shipping_street
	@shipping_city = data.shipping_city
	@shipping_region = data.shipping_region
	@shipping_postal = data.shipping_postal
	@items = data.items #object containing items
	return

# initializers
# this.create();
# this.update();
Record:: =
	create: (should_append) ->
		self = this
		
		# create structure
		@$row = (if should_append then $("<tr id=\"row-" + @row_id + "\" data-row-id=\"" + @row_id + "\"></tr>").appendTo($table) else $("<tr id=\"row-" + @row_id + "\" data-row-id=\"" + @row_id + "\"></tr>").prependTo($table))
		@$actions = $("<td class=\"actions\"></td>").appendTo(@$row)
		@$order_date = $("<td class=\"order_date\"></td>").appendTo(@$row)
		@$order_type = $("<td class=\"order_type\"></td>").appendTo(@$row)
		@$customer_name = $("<td class=\"customer\"></td>").appendTo(@$row)
		@$service_date = $("<td class=\"service_date\"></td>").appendTo(@$row)
		@$total_paid = $("<td class=\"total_paid\"></td>").appendTo(@$row)
		@$delivery_address = $("<td class=\"delivery_address\"></td>").appendTo(@$row)
		@$tax = $("<td class=\"tax\"></td>").appendTo(@$row)
		@$item1 = $("<td class=\"item1\"></td>").appendTo(@$row)
		@$item1_price = $("<td class=\"item1_price\"></td>").appendTo(@$row)
		@$item2 = $("<td class=\"item2\"></td>").appendTo(@$row)
		@$item2_price = $("<td class=\"item2_price\"></td>").appendTo(@$row)
		@$item3 = $("<td class=\"item3\"></td>").appendTo(@$row)
		@$item3_price = $("<td class=\"item3_price\"></td>").appendTo(@$row)
		@$item4 = $("<td class=\"item4\"></td>").appendTo(@$row)
		@$item4_price = $("<td class=\"item4_price\"></td>").appendTo(@$row)
		@$item5 = $("<td class=\"item5\"></td>").appendTo(@$row)
		@$item5_price = $("<td class=\"item5_price\"></td>").appendTo(@$row)
		@$cells = [
			this.$row
			this.$actions
			this.$order_date
			this.$order_type
			this.$customer
			this.$service_date
			this.$total_paid
			this.$delivery_address
			this.$tax
			this.$item1
			this.$item1_price
			this.$item2
			this.$item2_price
			this.$item3
			this.$item3_price
			this.$item4
			this.$item4_price
			this.$item5
			this.$item5_price
		]
		
		# bind events
		@$actions.on "click", "button.editing,button.locked", ->
			self.toggleLock()
			return

		@$row.on "change", "input", ->
			self.saveColumn $(this)
			return

		@$actions.on "click", "button.delete", ->
			confirm = window.confirm("Are you sure?")
			self.destroy()  if confirm is true
			return

		return

	update: ->
		self = this
		@$row.find("> td").html ""
		
		# setup total paid
		self.total_paid = 0
		$.each self.items, (i, item) ->
			self.total_paid += parseFloat(item.price)
			return

		@$total_paid.html "$" + self.total_paid
		if @locked
			@$actions.html "<button type=\"button\" class=\"btn btn-default locked\">Locked</button>"
			@$order_date.html @order_date
			@$order_type.html @order_type
			@$customer_name.html @customer_name
			@$service_date.html @service_date
			@$delivery_address.html @shipping_street + "<br><span class=\"filterable\" data-column=\"shipping_city\">" + @shipping_city + "</span>, <span class=\"filterable\" data-column=\"shipping_region\">" + @shipping_region + "</span> <span class=\"filterable\" data-column=\"shipping_postal\">" + @shipping_postal + "</span>"
			@$tax.html "$" + @tax
			@addItems()
			
			# events
			$("button.locked", @$actions).hover (->
				$(this).toggleClass("btn-default").toggleClass("btn-primary").text "Unlock"
				return
			), ->
				$(this).toggleClass("btn-default").toggleClass("btn-primary").text "Locked"
				return

		else
			@$actions.html "<button type=\"button\" class=\"btn btn-warning editing\">Editing</button><br><button type=\"button\" class=\"btn btn-danger delete\">Delete</button>"
			@$order_date.html "<input class=\"form-control\" type=\"text\" value=\"" + @order_date + "\" data-name=\"order_date\"></input>"
			@$order_type.html @order_type
			@$customer_name.html "<input class=\"form-control\" type=\"text\" value=\"" + @customer_name + "\" data-name=\"customer_name\"></input>"
			@$service_date.html "<input class=\"form-control\" type=\"text\" value=\"" + @service_date + "\" data-name=\"service_date\"></input>"
			
			# this.$total_paid.html('<input class="form-control" type="text" value="'+this.total_paid+'" data-name="total_paid"></input>');
			@$delivery_address.html "<input class=\"form-control\" type=\"text\" value=\"" + @shipping_street + "\" data-name=\"shipping_street\" placeholder=\"Street\"><br><input class=\"form-control\" type=\"text\" value=\"" + @shipping_city + "\" data-name=\"shipping_city\" placeholder=\"City\"><br><input class=\"form-control\" type=\"text\" value=\"" + @shipping_region + "\" data-name=\"shipping_region\" placeholder=\"State/Region\"><br><input class=\"form-control\" type=\"text\" value=\"" + @shipping_postal + "\" data-name=\"shipping_postal\" placeholder=\"Postal Code\">"
			@$tax.html "$" + @tax
			@addItems()
			
			# events
			$("input", @$order_date).datepicker()
			$("input", @$service_date).datepicker()
			$("button.editing", @$actions).hover (->
				$(this).text "Save"
				return
			), ->
				$(this).text "Editing"
				return

		return

	destroy: ->
		self = this
		$.ajax(
			url: "api.php"
			type: "POST"
			dataType: "text"
			data:
				action: "destroy"
				row_id: self.row_id
		).done ->
			self.$row.fadeOut()
			return

		return

	saveColumn: ($inputElem) ->
		self = this
		column_name = $inputElem.data("name")
		value = $inputElem.val()
		if /date/.test(column_name)
			console.log value
			value = moment(value).format("YYYY-MM-DD") # + " 12:00:00";
		# velue = encodeURIComponent(value);
		rev_record_id = if /^item[0-9]/.test(column_name) then $inputElem.data("id") else ""
		$.ajax(
			url: "/static/report/api.php"
			type: "POST"
			dataType: "text"
			data:
				action: "update_column"
				row_id: self.row_id
				column_name: column_name
				new_value: value
				rr_id: rev_record_id
		).done (data) ->
			console.log data
			if data is "success"
				
				# console.log("Updated row "+self.row_id+": "+column_name+" to "+value);
				# if (/item[0-9]/.test(column_name)) {
				#   var item_position = column_name.match(/item([0-9])/)[1];
				#   item_position = parseInt(item_position) - 1;
				#   if (/price/.test(column_name)) {
				#     records[self.row_id].items[item_position].price = value;
				#     self.update();
				#   } else {
				#     records[self.row_id].items[item_position].item = value;
				#   }
				# } else {
				#   records[self.row_id][column_name] = value;
				# }
				self.fetch ->
					self.update()
					createTotals()
					return

			return

		return

	toggleLock: ->
		self = this
		row_id = self.row_id
		action = (if @locked then "unlock_row" else "lock_row")
		$.ajax(
			url: "/static/report/api.php"
			type: "POST"
			dataType: "text"
			data:
				action: action
				row_id: row_id
		).done (data) ->
			if data is "success"
				self.locked = not self.locked
				self.update()
			return

		return

	addItems: ->
		self = this
		i = 1

		while i <= 5
			items_index = i - 1
			unless typeof self.items[items_index] is "undefined"
				if self.locked
					self["$item#{i}"].text self.items[items_index].item
					self["$item#{i}" + "_price"].text "$" + self.items[items_index].price
				else
					self["$item#{i}"].html "<input class='form-control' type='text' value='#{self.items[items_index].item}' data-name='item#{i}' data-id='#{self.items[items_index].id}''></input>"
					self["$item#{i}" + "_price"].html "<input class='form-control' type='text' value='#{self.items[items_index].price}' data-name='item#{i}_price' data-id='#{self.items[items_index].id}''></input>"
			
			# self['$item'+i].text(self.items[items_index].item);
			# self['$item'+i+'_price'].text('$'+self.items[items_index].price);
			else #item is undefined
				unless self.locked #and row is unlocked
					self["$item" + i].html "<input class=\"form-control\" type=\"text\" value=\"\" data-name=\"item" + i + "\"></input>"
					self["$item" + i + "_price"].html "<input class=\"form-control\" type=\"text\" value=\"\" data-name=\"item" + i + "_price\"></input>"
			i++
		return

	fetch: (callback) ->
		self = this
		$.ajax(
			url: "/static/report/findOne.php"
			type: "POST"
			dataType: "text"
			data:
				record_id: self.row_id
		).done (response) ->
			data = JSON.parse(response)
			self.row_id = data.id
			self.locked = (if data.locked is "1" then true else false)
			self.order_date = data.order_date
			self.order_type = data.order_type
			self.customer_name = data.customer_name
			self.service_date = data.service_date
			self.total_paid = data.total_paid
			self.tax = parseFloat(data.tax).toFixed(2)
			self.shipping_street = data.shipping_street
			self.shipping_city = data.shipping_city
			self.shipping_region = data.shipping_region
			self.shipping_postal = data.shipping_postal
			self.items = data.items
			callback and callback()
			return

		return


# # # # # # # # # # # # # # # # # # # # /
# 
#  Initializers
#
# # # # # # # # # # # # # # # # /

# code

$table = $("#tablecontents")
$tfoot = $("#tablefooter")
$createButton = $("button#createRow")
$filters = [
	$("select#order_by")
	$("select#order")
	$("select#per_page")
]
current_page = (if getParameterByName("page") is "" then 1 else parseInt(getParameterByName("page")))
@records = {}
totals_paid = 0
total_tax = 0
filter = ""
date_filter = ""

$ ->
	init()
	fetchRecords()
	initEvents()
	return
# $("#tablecontents").delegate("button.editing", 'click', function(e) {

#   // update the data
#   $row = $(this).closest("tr");
#   $row.find("input.save").each(function() {
#     column_name = $(this).attr("name");
#     new_value = $(this).val();
#     $.ajax({
#       url: '/static/report/api.php',
#       type: 'POST',
#       dataType: 'text',
#       data: {
#         action: 'update_column',
#         row_id: row_id,
#         column_name: column_name,
#         new_value: new_value
#       },
#     })
#     .done(function(data) {
#       if (data == "success") {
#         $row.find("input").each(function() {
#           value = $(this).val();
#           $(this).replaceWith(value);
#         })
#       };
#     });
#   });
# });

# datepicker