function call_ajax(action)
{
  var flag = "";

  if (action == "create_room" || action == "update_room")
  {
    if (!$("#room_name").val().length)
    {
      flag += "nama kamar kosong";
    }
  }
  
  if (action =="create_booking")
  {
    if (!$("#booking_title").val().length)
    {
      flag += "nama penghuni kosong \n";
      flag += "nama penghuni kosong \n";
    }
    if (!$("#booking_start").val().length)
    {
      flag += "tanggal masuk kosong \n";
    }
    if (!$("#booking_end").val().length)
    {
      flag += "tanggal masuk kosong \n";
    }
  }

  if (action == "get_empty_rooms")
  {
    if (!$("#search_start").val().length)
    {
      flag += "tanggal pencarian kosong";
    }
    
    if (!$("#search_end").val().length)
    {
      flag += "tanggal pencarian kosong";
    }
  }
  
  if (!flag.length)
  {
    $("#div_empty_rooms").html("");
    $("#div_bookings").html("");

    ajax_load = "Please wait";
    loadUrl = "index_process.php";

    passingValue = $("form").serializeArray();
    passingValue.push({name: "action", value: action});
    
    if (action == "get_rooms" || action == "create_room" || action == "update_room" || action == "delete_room")
    {
      div_message = "#ajax_message_room";
    }
    else
    {
      div_message = "#ajax_message_booking";
    }

    $(div_message)
    .html(ajax_load)
    .load(loadUrl, passingValue,
      function(response_text)
      { 
        handle_response(action, response_text);
      }
      );
  }
  else
  {
    alert(flag);
  }
}

function handle_response(action, response_text)
{
  $("#ajax_message_room").html("");
  $("#ajax_message_booking").html("");
  switch (action)
  {
    case "get_rooms":
    $("#div_rooms").html(response_text);
    break;

    case "create_room":
    if (response_text == true)
    {
      $("#div_room").hide();
      close_modal_room();
    }
    else
    {
      $("#ajax_message_room").html("error tolong dicoba lagi <br />  <br />");
    }
    break;

    case "update_room":
    if (response_text == true)
    {
      $("#div_room").hide();
      close_modal_room();
    }
    else
    {
      $("#ajax_message_room").html("error tolong dicoba lagi  <br />  <br />");
    }
    break;

    case "delete_room":
    if (response_text == true)
    {
      $("#div_room").hide();
      call_ajax("get_rooms");
    }
    else
    {
      $("#ajax_message_room").html("error tolong dicoba lagi  <br />  <br />");
    }
    break;

    case "create_booking":
    if (response_text == true)
    {
      close_modal_booking();
      refresh_room_calendar($("#booking_room_id").val());
    }
    else
    {
      $("#ajax_message_booking").html("error tolong dicoba lagi  <br />  <br />");
    }
    break;

    case "update_booking":
    if (response_text == true)
    {
      close_modal_booking();
      refresh_room_calendar($("#booking_room_id").val());
    }
    else
    {
      $("#ajax_message_booking").html("error tolong dicoba lagi  <br />  <br />");
    }
    break;

    case "delete_booking":
    if (response_text == true)
    {
      close_modal_booking();
      refresh_room_calendar($("#booking_room_id").val());
    }
    else
    {
      $("#ajax_message_booking").html("error tolong dicoba lagi  <br />  <br />");
    }
    break;

    case "get_empty_rooms":
    $("#div_empty_rooms").html(response_text);
    break;

    case "get_bookings":
    $("#div_bookings").html(response_text);
    break;
  }
}

function delete_room(id, name)
{
  var r=confirm("Yakin menghapus kamar dengan nama " + name + " ?");
  if (r==true)
  {
    $("#room_id").val(id);
    call_ajax("delete_room");
  }
  else
  {
  }
}

function open_modal_room(action, id, name, gender)
{
  $("#form_room input").val("");
  $("#form_room a").hide();
  $("#ajax_message_room").html("");
  $('#modal_room').foundation("reveal", "open");
  switch(action)
  {
    case "create":
    $("#btn_create_room").show();
    break;
    case "update":
    $("#room_id").val(id);
    $("#room_name").val(name);
    $("#room_gender").val(gender);
    $("#btn_update_room").show();
    break;
  }
}

function close_modal_room()
{
  $('#modal_room').foundation("reveal", "close");
  call_ajax("get_rooms");
}

function fill_date(id, value)
{
  var year = value.getFullYear();
  var month = (value.getMonth() + 1).toString();
  if (month.length < 2) month = "0" + month;
  var date = value.getDate().toString();
  if (date.length < 2) date = "0" + date;
  $("#" + id).val(year + "-" + month + "-" + date);
}

function open_modal_booking(action, id, title, start, end, name, note, attachments)
{
  init_attachments();
  $('#progress .bar').css('width', 0 + '%');
  $("#list_file_booking").html("");
  $("#form_booking a").hide();
  $("#form_booking input[type=text]").val("");
  $("#form_booking textarea").val("");
  $("#booking_start").datepicker("option", "maxDate", null);
  $("#booking_end").datepicker("option", "minDate", null);
  $("#ajax_message_booking").html("");
  $("#modal_booking").foundation("reveal", "open");
  switch(action)
  {
    case "create":
    $("#btn_create_booking").show();
    break;
    case "update":
    $("#booking_id").val(id);
    $("#booking_title").val(title);
    $("#booking_name").val(name);
    $("#booking_note").val(note);
    fill_date("booking_start", start);
    if (end != null)
      fill_date("booking_end", end);
    else
      fill_date("booking_end", start);

    $("#booking_start").datepicker("option", "maxDate", new Date($("#booking_end").val()));
    $("#booking_end").datepicker("option", "minDate", new Date($("#booking_start").val()));

    $("#btn_update_booking").show();
    $("#btn_delete_booking").show();

    $.each(attachments, function( index, value ) {
      var add_to_div = '<div id="' + value[0] + '"><a href="#" onclick="add_to_delete_file(' + value[0] + ')"><i class="icon-trash"></i></a> ';
      add_to_div += '<a href="files/' + $("#booking_id").val() + '/' + value[1] + '" target="_blank" >' + value[1] + '';

      var index = value[1].indexOf(".");
      var extension = value[1].substring(index + 1, index + 4).toLowerCase();
      var extensions = Array("jpg", "png", "gif");
      if ($.inArray(extension, extensions) >= 0)
      {
        add_to_div += '<img src="files/' + $("#booking_id").val() + '/' + value[1] + '" height="40" width="40">';
      }
      add_to_div += "</a></div>";
      $("#list_file_booking").append(add_to_div);

       $("#list_hidden_file_booking").append("<input type='hidden' name='exist_attachments[]' value='" +  value[1] + "' /> <br />");
    });
    break;
  }
}

function add_to_delete_file(id)
{
  var r=confirm("Yakin menghapus file ?");
  if (r==true)
  {
    $("#" + id).remove();
    $("#list_hidden_file_booking").append("<input type='hidden' name='delete_attachments[]' value='" +  id + "' /> <br />");
  }
}

function close_modal_booking()
{
  $('#modal_booking').foundation("reveal", "close");
}

function refresh_room_calendar(id, date)
{
  source = 
  {
    url: "get_room_bookings.php",
    type: "POST",
    data: {
      action: "get",
      room_id: $("#booking_room_id").val()
    },
    error: function() {
      alert('there was an error while fetching events!');
    }
  };
  
  $("#room_calendar").fullCalendar("removeEventSource", source);
  $("#room_calendar").fullCalendar("addEventSource", source);
  if (date != null)
  {
    if (date.length > 0)
    {
      $("#room_calendar").fullCalendar( 'gotoDate', new Date(date))
    }
  }
}

function open_room_calendar(id, name, gender, date)
{
  $("#label_room").html(name);
  $("#label_gender").html(gender);
  $("#booking_room_id").val(id);
  refresh_room_calendar($("#booking_room_id").val(), date);
  $("#div_room").show();
}

function delete_booking()
{
  var r=confirm("Yakin menghapus booking dari " + $("#booking_title").val() + " ?");
  if (r==true)
  {
    call_ajax("delete_booking");
  }
}

function init_attachments()
{
  var input = "<input type='hidden' name='new_attachments[]' />";
  input += "<input type='hidden' name='delete_attachments[]' />";
  $("#list_hidden_file_booking").html(input);
}

$(function() {
  $(document).foundation();
  
  //  init datepicker
  $("#booking_start").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "c-50:c+50",
    dateFormat: "yy-mm-dd",
    onClose: function(selectedDate) {
      $("#booking_end").datepicker("option", "minDate", selectedDate);
    }
  });
  $("#booking_end").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "c-50:c+50",
    dateFormat: "yy-mm-dd",
    onClose: function(selectedDate) {
      $("#booking_start").datepicker("option", "maxDate", selectedDate);
    }
  });
  
  $("#search_start").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "c-50:c+50",
    dateFormat: "yy-mm-dd",
    onClose: function(selectedDate) {
      $("#search_end").datepicker("option", "minDate", selectedDate);
    }
  });
  $("#search_end").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "c-50:c+50",
    dateFormat: "yy-mm-dd",
    onClose: function(selectedDate) {
      $("#search_start").datepicker("option", "maxDate", selectedDate);
    }
  });
  
  //  init room calendar
  $("#room_calendar").fullCalendar({
    // put your options and callbacks here
    titleFormat: {
      month: "MMM yyyy"
    },
    eventClick: function(calEvent, jsEvent, view) {
      open_modal_booking("update", calEvent.id, calEvent.title, calEvent.start, calEvent.end, calEvent.name, calEvent.note, calEvent.attachments);
    }
  });
  $("#div_room").hide();
  
  //upload file
  $('#booking_attachment').fileupload({
    dataType: 'json',
    done: function (e, data) 
    {
      $.each(data.result.files, function (index, file) 
      {
        $("#list_file_booking").append("<div>" + file.name + "</div>");
        $("#list_hidden_file_booking").append("<input type='hidden' name='new_attachments[]' value='" +  file.name + "' />");
      });
    },
    progressall: function (e, data) 
    {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $("#progress .bar").css
      (
        "width",
        progress + '%'
        );
    }
  });
  init_attachments();
  
  call_ajax("get_rooms");
});