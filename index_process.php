<?php
require_once("include/func_db.php");

$action = $_POST["action"];
$room = $_POST["room"];
$book = $_POST["book"];
$search = $_POST["search"];

function make_link($link, $label, $onclick=null, $icon=null, $title=null)
{
  return "<a href='".$link."' title='".$title."' onclick='".$onclick."'>".$icon." ".$label."</a>";
}

switch($action)
{
  case "get_rooms":
    $sql = "SELECT id, name, gender FROM rooms";
    $rows = db_select($sql, $db_rooms);
    echo "<br />Total jumlah kamar : ".count($rows)."<br /><br />";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nama Kamar</th>";
    echo "<th>Jenis Kelamin</th>";
    echo "<th>Aksi</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows as $row)
    {
      echo "<tr>";
      echo "<td>".make_link("#", $row[1], 'open_room_calendar('.$row[0].', "'.$row[1].'")')."</td>";
      echo "<td>".make_link("#", $row[2], 'open_room_calendar('.$row[0].', "'.$row[1].'")')."</td>";
      echo "<td>".make_link('#', '', 'open_modal_room("update", "'.$row[0].'", "'.$row[1].'", "'.$row[2].'")', '<i class="icon-edit"></i>', 'Ubah Nama Kamar')." ";
      echo " ".make_link('#', '', 'delete_room("'.$row[0].'", "'.$row[1].'")', '<i class="icon-trash"></i>', 'Hapus Kamar')."</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    break;
    
  case "create_room":
    $sql = "INSERT INTO rooms (name, gender) VALUES ('".$room["name"]."', '".$room["gender"]."')";
    echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
    break;
    
  case "update_room":
    $sql = "UPDATE rooms SET name='".$room["name"]."' WHERE id='".$room["id"]."'";
    echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
    break;
    
  case "delete_room":
    $sql = "DELETE FROM rooms WHERE id='".$room["id"]."'";
    echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
    break;
    
  case "create_booking":
    $sql = "SELECT id FROM bookings WHERE room_id='".$book["room_id"]."' AND ";
    $sql .= "((start <= '".$book["date_in"]."' AND '".$book["date_in"]."' <= end) ";
    $sql .= "OR (start <= '".$book["date_out"]."' AND '".$book["date_out"]."' <= end) ";
    $sql .= "OR ('".$book["date_in"]."' <= start AND start <= '".$book["date_out"]."') ";
    $sql .= "OR ('".$book["date_in"]."' <= end AND end <= '".$book["date_out"]."')) ";
    $rows = db_select($sql, $db_rooms);
    if (!count($rows))
    {
      $sql = "INSERT INTO bookings (title, start, end, room_id) VALUES ('".$book["name"]."', '".$book["date_in"]."', '".$book["date_out"]."', '".$book["room_id"]."')";
      echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
    }
    else
    {
      echo "room already booked";
    }
    break;
    
  case "delete_booking":
    $sql = "DELETE FROM bookings WHERE id='".$_POST["book_id"]."'";
    echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
    break;
    
  case "get_booking":
    $sql = "SELECT rooms.id, rooms.name, bookings.title FROM bookings, rooms WHERE bookings.room_id=rooms.id AND '".$search["date"]."' between bookings.start AND bookings.end";
    $rows_full = db_select($sql, $db_rooms);
    
    $room_ids = "";
    foreach ($rows_full as $row)
    {
      if ($room_ids == "")
      {
          $room_ids = "'".$row[0]."'";
      }
      else
      {
          $room_ids .= ", '".$row[0]."'";
      }
    }
    
    $sql = "SELECT id, name FROM rooms WHERE id NOT IN (".$room_ids.")";
    $rows_empty = db_select($sql, $db_rooms);
    
    echo "<br />Jumlah kamar yang kosong : ".count($rows_empty)."<br /><br />";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nama Kamar</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows_empty as $row)
    {
      echo "<tr>";
      echo "<td>".make_link("#", $row[1], 'open_room_calendar('.$row[0].', "'.$row[1].'")')."</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    
    echo "<br />Jumlah kamar yang sudah dibooking : ".count($rows_full)."<br /><br />";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nama Kamar</th>";
    echo "<th>Nama Penghuni</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows_full as $row)
    {
      echo "<tr>";
      echo "<td>".make_link("#", $row[1], 'open_room_calendar('.$row[0].', "'.$row[1].'")')."</td>";
      echo "<td>".make_link("#", $row[2], 'open_room_calendar('.$row[0].', "'.$row[1].'")')."</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    break;
}
?>