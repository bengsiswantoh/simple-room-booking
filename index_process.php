<?php
require_once("include/func_db.php");

$action = $_POST["action"];
$room = $_POST["room"];
$booking = $_POST["booking"];
$search = $_POST["search"];
$filter_room = $_POST["filter_room"];

function make_link($link, $label, $onclick=null, $icon=null, $title=null)
{
  return "<a href='".$link."' title='".$title."' onclick='".$onclick."'>".$icon." ".$label."</a>";
}

switch($action)
{
  case "get_rooms":
    $sql = "SELECT id, name, gender FROM rooms WHERE name like '%".$filter_room["name"]."%' ORDER BY gender desc, name ";
    $rows = db_select($sql, $db_rooms);
    echo "<br />Total jumlah kamar : ".count($rows)."<br /><br />";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th width='150'>Nama Kamar</th>";
    echo "<th width='150'>Jenis Kelamin</th>";
    echo "<th width='50'>Aksi</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows as $row)
    {
      echo "<tr>";
      echo "<td>".make_link("#", $row[1], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'")')."</td>";
      echo "<td>".make_link("#", $row[2], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'")')."</td>";
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
    $sql = "SELECT id FROM bookings WHERE room_id='".$booking["room_id"]."' AND ";
    $sql .= "((start <= '".$booking["start"]."' AND '".$booking["start"]."' <= end) ";
    $sql .= "OR (start <= '".$booking["end"]."' AND '".$booking["end"]."' <= end) ";
    $sql .= "OR ('".$booking["start"]."' <= start AND start <= '".$booking["end"]."') ";
    $sql .= "OR ('".$booking["start"]."' <= end AND end <= '".$booking["end"]."')) ";
    $rows = db_select($sql, $db_rooms);
    if (!count($rows))
    {
      $sql = "INSERT INTO bookings (name, title, start, end, note, room_id) VALUES ('".$booking["name"]."', '".$booking["title"]."', '".$booking["start"]."', '".$booking["end"]."', '".$booking["note"]."', '".$booking["room_id"]."')";
      echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
    }
    else
    {
      echo "kamar sudah dibooking pada tanggal tersebut";
    }
    break;
    
  case "update_booking":
    $sql = "SELECT id FROM bookings WHERE room_id='".$booking["room_id"]."' AND id <> ".$booking["id"]." AND ";
    $sql .= "((start <= '".$booking["start"]."' AND '".$booking["start"]."' <= end) ";
    $sql .= "OR (start <= '".$booking["end"]."' AND '".$booking["end"]."' <= end) ";
    $sql .= "OR ('".$booking["start"]."' <= start AND start <= '".$booking["end"]."') ";
    $sql .= "OR ('".$booking["start"]."' <= end AND end <= '".$booking["end"]."')) ";
    $rows = db_select($sql, $db_rooms);
    if (!count($rows))
    {
      $sql = "UPDATE bookings SET name='".$booking["name"]."', title='".$booking["title"]."', start='".$booking["start"]."', end='".$booking["end"]."', note='".$booking["note"]."' WHERE id='".$booking["id"]."'";
      echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
    }
    else
    {
      echo "kamar sudah dibooking pada tanggal tersebut";
    }
    break;
    
  case "delete_booking":
    $sql = "DELETE FROM bookings WHERE id='".$booking["id"]."'";
    echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
    break;
    
  case "get_empty_rooms":
    $sql = "SELECT rooms.id, rooms.name, rooms.gender, bookings.start, bookings.end FROM bookings, rooms ";
    $sql .= "WHERE bookings.room_id=rooms.id AND ";
    $sql .= "((bookings.start <= '".$search["start"]."' AND '".$search["start"]."' <= end) ";
    $sql .= "OR (start <= '".$search["end"]."' AND '".$search["end"]."' <= end) ";
    $sql .= "OR ('".$search["start"]."' <= start AND start <= '".$search["end"]."') ";
    $sql .= "OR ('".$search["start"]."' <= end AND end <= '".$search["end"]."')) ";
    $sql .= "ORDER BY bookings.start, rooms.gender, rooms.name ";
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
    
    $sql = "SELECT id, name, gender FROM rooms WHERE id NOT IN (".$room_ids.") ORDER BY gender";
    $rows_empty = db_select($sql, $db_rooms);
    
    echo "<br />Jumlah kamar yang kosong : ".count($rows_empty)."<br /><br />";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th width='150'>Nama Kamar</th>";
    echo "<th width='150'>Jenis Kelamin</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows_empty as $row)
    {
      echo "<tr>";
      echo "<td>".make_link("#", $row[1], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'")')."</td>";
      echo "<td>".make_link("#", $row[2], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'")')."</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    
    echo "<br />Jumlah booking : ".count($rows_full)."<br /><br />";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th width='150'>Nama Kamar</th>";
    echo "<th width='150'>Dari Tanggal</th>";
    echo "<th width='150'>Sampai Tanggal</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows_full as $row)
    {
      echo "<tr>";
      echo "<td>".make_link("#", $row[1], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[3].'")')."</td>";
      echo "<td>".make_link("#", $row[3], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[3].'")')."</td>";
      echo "<td>".make_link("#", $row[4], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[3].'")')."</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    break;
    
  case "get_bookings":
    $sql = "SELECT rooms.id, rooms.name, rooms.gender, bookings.name, bookings.title, bookings.start, bookings.end FROM bookings, rooms ";
    $sql .= "WHERE bookings.room_id=rooms.id AND ";
    $sql .= "(bookings.title LIKE '%".$search["title"]."%' AND bookings.name LIKE '%".$search["name"]."%') ";
    $sql .= "ORDER BY bookings.start, rooms.gender, bookings.name, bookings.title";
    $rows = db_select($sql, $db_rooms);
      
    echo "<br />Jumlah booking : ".count($rows)."<br /><br />";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th width='100'>Nama Kamar</th>";
    echo "<th width='100'>Nama Group</th>";
    echo "<th width='100'>Nama Penghuni</th>";
    echo "<th width='100'>Dari Tanggal</th>";
    echo "<th width='100'>Sampai Tanggal</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($rows as $row)
    {
      echo "<tr>";
      echo "<td>".make_link("#", $row[1], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[5].'")')."</td>";
      echo "<td>".make_link("#", $row[3], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[5].'")')."</td>";
      echo "<td>".make_link("#", $row[4], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[5].'")')."</td>";
      echo "<td>".make_link("#", $row[5], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[5].'")')."</td>";
      echo "<td>".make_link("#", $row[6], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[5].'")')."</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    break;
}
?>