<?php
//set_time_limit ( 60 );
require_once("include/func_db.php");

$action = $_POST["action"];
$report = $_POST["report"];

switch($action)
{
  case "get_report":
    $sql = "SELECT id, name FROM rooms ORDER BY gender desc, name";
    $rooms = db_select($sql, $db_rooms);

    $year_month = $report["year"]."-".$report["month"];

    $max = date("t", strtotime($year_month));
    $start_date = 1;

    $data = "<table border='1'>";
    $data .= "<thead>";
    $data .= "<tr>";
    $data .= "<td rowspan='2'>Kamar</td>";
    $data .= "<td colspan='31'><center>Tanggal</center></td>";
    $data .= "</tr>";
    $data .= "<tr>";
    for ($i = $start_date; $i <= $max; $i++) 
    {
      if (strlen($i) == 1) $i = "0".$i;
      $data .= "<th>".$i."</th>";
    }
    $data .= "</tr>";
    $data .= "</thead>";

    $data .= "<tbody>";
    foreach ($rooms as $room) 
    { 
      $data .= "<tr>";
      $data .= "<td>".$room[1]."</td>";
      
      $sql = "SELECT title, end, start FROM bookings ";
      $sql .= "WHERE bookings.room_id='".$room[0]."' ";
      $bookings = db_select($sql, $db_rooms);
      
      for ($i = $start_date; $i <= $max; $i++) 
      {
        $guest = "";
        $current_date = date("Y-m-d", strtotime($year_month." +".($i - 1) ." day"));
        foreach ($bookings as $booking) 
        {
          $end = $booking[1];
          $start = $booking[2];
          if ($start <= $current_date && $current_date <= $end)
          {
            $guest = $booking[0];
            
            break;
          }
        }
        
        if ($guest == "")
        {
          $data .= "<td></td>";
        }
        else
        {
          if (date("Y-m", strtotime($end)) != $year_month)
          {
            $end = date("Y-m-t", strtotime($year_month));
          }
          if (date("Y-m", strtotime($start)) != $year_month)
          {
            $start = date("Y-m-", strtotime($year_month))."01";
          }
          $colspan = (date("d", strtotime($end)) - date("d", strtotime($start)));
          $i += $colspan;
          $data .= "<td colspan='".($colspan + 1)."'><div class='fc-event fc-event-hori fc-event-start'><div class='fc-event-inner'><span class='fc-event-title'>".$guest."</span></div></div></td>";
        }
      }
      $data .= "</tr>";
    }
    $data .= "</tbody>";
    $data .= "</table>";

    echo $data;
    break;
}
?>