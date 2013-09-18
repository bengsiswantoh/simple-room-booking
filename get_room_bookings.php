<?php
require_once("include/func_db.php");

$sql = "SELECT id, name, title, start, end, note FROM bookings WHERE room_id='".$_POST["room_id"]."'";
$rows = db_select($sql, $db_rooms, "class");
foreach ($rows as $row)
{
  $row->attachments = array();
  $sql = "SELECT id, name FROM attachments WHERE booking_id='".$row->id."'";
  $rows_attachment = db_select($sql, $db_rooms);
  foreach ($rows_attachment as $row_attachment)
  {
    array_push($row->attachments, [$row_attachment[0], $row_attachment[1]]);
  }
}

echo json_encode($rows, true);
?>