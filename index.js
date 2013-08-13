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
    
  if (action == "get_booking")
  {
    if (!$("#search_date").val().length)
    {
      flag += "tanggal pencarian kosong";
    }
  }
  
  if (!flag.length)
  {
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
      $("#room_list").html(response_text);
      break;
      
    case "create_room":
      if (response_text == true)
      {
        close_modal_room();
        refresh_search();
        $("#div_room").hide();
      }
      else
      {
        $("#ajax_message_room").html("error tolong dicoba lagi <br />  <br />");
      }
      break;
      
    case "update_room":
      if (response_text == true)
      {
        close_modal_room();
        refresh_search();
        $("#div_room").hide();
      }
      else
      {
        $("#ajax_message_room").html("error tolong dicoba lagi  <br />  <br />");
      }
      break;
      
    case "delete_room":
      if (response_text == true)
      {
        call_ajax("get_rooms");
        refresh_search();
        $("#div_room").hide();
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
        refresh_search();
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
        refresh_search();
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
        refresh_search();
      }
      else
      {
        $("#ajax_message_booking").html("error tolong dicoba lagi  <br />  <br />");
      }
      break;
      
    case "get_booking":
      $("#booking_list").html(response_text);
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
  var date = value.getDate();
  $("#" + id).val(year + "-" + month + "-" + date);
}

function open_modal_booking(action, id, title, start, end)
{
  $("#form_booking a").hide();
  $("#form_booking input[type=text]").val("");
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
      fill_date("booking_start", start);
      if (end != null)
        fill_date("booking_end", end);
      else
        fill_date("booking_end", start);
      $("#btn_update_booking").show();
      $("#btn_delete_booking").show();
      break;
  }
}

function close_modal_booking()
{
  $('#modal_booking').foundation("reveal", "close");
}

function refresh_room_calendar(id)
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
}

function open_room_calendar(id, name)
{
  $("#room_label").html(name);
  $("#booking_room_id").val(id);
  refresh_room_calendar();
  $("#div_room").show();
}

function delete_booking()
{
  var r=confirm("Yakin menghapus booking dari " + $("#booking_title").val() + " ?");
  if (r==true)
  {
    call_ajax("delete_booking");
  }
  else
  {
  }
}

function refresh_search()
{
  if ($("#search_date").val().length)
  {
    call_ajax("get_booking");
  }
}

$(function() {
  $(document).foundation();
  
  call_ajax("get_rooms");
  
  //  init datepicker
  $("#booking_start").datepicker({
    changeMonth: true,
    dateFormat: "yy-mm-dd",
    onClose: function(selectedDate) {
      $("#booking_end").datepicker("option", "minDate", selectedDate);
    }
  });
  $("#booking_end").datepicker({
    changeMonth: true,
    dateFormat: "yy-mm-dd",
    onClose: function(selectedDate) {
      $("#booking_start").datepicker("option", "maxDate", selectedDate);
    }
  });
  
  $("#search_date").datepicker({
    changeMonth: true,
    dateFormat: "yy-mm-dd",
  });
  
  //  init room calendar
  $("#room_calendar").fullCalendar({
    // put your options and callbacks here
    titleFormat: {
      month: "MMM yyyy"
    },
    eventClick: function(calEvent, jsEvent, view) {
      open_modal_booking("update", calEvent.id, calEvent.title, calEvent.start, calEvent.end);
    }
  });
  $("#div_room").hide();
});