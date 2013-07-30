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
    if (!$("#book_name").val().length)
    {
      flag += "nama penghuni kosong \n";
    }
    if (!$("#book_date_in").val().length)
    {
      flag += "tanggal masuk kosong \n";
    }
    if (!$("#book_date_out").val().length)
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
      div_message = "#ajax_message_book";
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
  $("#ajax_message_book").html("");
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
        refresh_room_calendar($("#book_room_id").val());
        refresh_search();
      }
      else
      {
        $("#ajax_message_book").html("error tolong dicoba lagi  <br />  <br />");
      }
      break;
      
    case "delete_booking":
      if (response_text == true)
      {
        refresh_room_calendar($("#book_room_id").val());
        refresh_search();
      }
      else
      {
        $("#ajax_message_book").html("error tolong dicoba lagi  <br />  <br />");
      }
      break;
      
    case "get_booking":
      $("#booking_list").html(response_text);
      break;
  }
}

function fill_room_id(id)
{
  $("#room_id").val(id);
}

function delete_room(id, name)
{
  var r=confirm("Yakin menghapus kamar dengan nama " + name + " ?");
  if (r==true)
  {
    fill_room_id(id);
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
      $("#btn_create").show();
      break;
    case "update":
      fill_room_id(id);
      $("#room_name").val(name);
      $("#room_gender").val(gender);
      $("#btn_update").show();
      break;
  }
}

function close_modal_room()
{
  $('#modal_room').foundation("reveal", "close");
  call_ajax("get_rooms");
}

function open_modal_booking()
{
  $("#form_booking input[type=text]").val("");
  $("#book_date_in").datepicker("option", "maxDate", null);
  $("#book_date_out").datepicker("option", "minDate", null);
  $("#ajax_message_book").html("");
  $("#modal_booking").foundation("reveal", "open");
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
      room_id: $("#book_room_id").val()
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
  $("#book_room_id").val(id);
  refresh_room_calendar();
  $("#div_room").show();
}

function delete_booking(id, name, start, end)
{
  var r=confirm("Yakin menghapus booking dari " + name + " ?");
  if (r==true)
  {
    $("#book_id").val(id);
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
  $("#book_date_in").datepicker({
    changeMonth: true,
    dateFormat: "yy-mm-dd",
    onClose: function(selectedDate) {
      $("#book_date_out").datepicker("option", "minDate", selectedDate);
    }
  });
  $("#book_date_out").datepicker({
    changeMonth: true,
    dateFormat: "yy-mm-dd",
    onClose: function(selectedDate) {
      $("#book_date_in").datepicker("option", "maxDate", selectedDate);
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
      delete_booking(calEvent.id, calEvent.title, calEvent.start, calEvent.end);
    }
  });
  $("#div_room").hide();
});