function call_ajax(action)
{
  var flag = "";
    
  if (!flag.length)
  {
    ajax_load = "Please wait";
    loadUrl = "report_process.php";

    passingValue = $("form").serializeArray();
    passingValue.push({name: "action", value: action});
    
    div_message = "#div_report";

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
  $("#ajax_message").html("");
  switch (action)
  {
    case "get_report":
      $('table').stickyTableHeaders();
      break;
  }
}

function print_pdf()
{
  window.open("report_pdf.php?year=" + $("#report_year").val() + "&month=" + $("#report_month").val() );
}

$(function() {
  
});