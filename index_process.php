<?php
require_once("include/func_db.php");

$action = $_POST["action"];
$room = $_POST["room"];
$booking = $_POST["booking"];
$search = $_POST["search"];
$filter_room = $_POST["filter_room"];
$new_attachments = $_POST["new_attachments"];
$delete_attachments = $_POST["delete_attachments"];

function make_link($link, $label, $onclick=null, $icon=null, $title=null)
{
  return "<a href='".$link."' title='".$title."' onclick='".$onclick."'>".$icon." ".$label."</a>";
}

function add_attachments($db_rooms, $attachments, $booking_id)
{
  $result = 1;
  if (!is_dir("files/".$booking_id)) 
  {
    mkdir("files/".$booking_id, 0700);
  }
  
  foreach ($attachments as $attachment)
  { 
    if ($attachment != "")
    {
      copy("files/".$attachment, "files/".$booking_id."/".$attachment);
      unlink("files/".$attachment);
      
      $sql = "INSERT INTO attachments (name, booking_id) VALUES ('".$attachment."', '".$booking_id."')";
      $rows = $db_rooms->exec($sql);
      if ($rows == 0)
      {
        $result = 0;
        break;
      }
    }
  }
  
  return $result;
}

function print_bookings($rows, $db_rooms)
{
  echo "<br />Jumlah booking : ".count($rows)."<br /><br />";
  echo "<table border=1>";
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
    echo "<td rowspan=3>".make_link("#", $row[1], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[6].'")')."</td>";
    echo "<td rowspan=3>".make_link("#", $row[4], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[6].'")')."</td>";
    echo "<td rowspan=3>".make_link("#", $row[5], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[6].'")')."</td>";
    echo "<td>".make_link("#", $row[6], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[6].'")')."</td>";
    echo "<td>".make_link("#", $row[7], 'open_room_calendar('.$row[0].', "'.$row[1].'", "'.$row[2].'", "'.$row[6].'")')."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan=2><textarea readOnly>".$row[8]."</textarea></td>";
    echo "</tr>";
    
    $sql = "SELECT name FROM attachments WHERE booking_id='".$row[3]."'";
    $rows_attachment = db_select($sql, $db_rooms);
    echo "<tr>";
    echo "<td colspan=2><center>";
    if (count($rows_attachment) > 0)
    {
      $files = "";
      foreach ($rows_attachment as $row_attachment)
      {
        echo make_link('#', $row_attachment[0].' <i class="icon-file"></i>'.'<br />', 'window.open("files/'.$row[3].'/'.$row_attachment[0].'")');
      }
    }
    echo "</center></td>";
    echo "</tr>";
  }
  echo "</tbody>";
  echo "</table>";
}

switch($action)
{
  case "get_rooms":
    $sql = "SELECT id, name, gender FROM rooms WHERE name like '%".$filter_room["name"]."%' ORDER BY gender DESC, name ";
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
      $result = $db_rooms->exec($sql);
      
      if ($result > 0)
      {
        $sql = "SELECT id FROM bookings ORDER BY id DESC";
        $rows = db_select($sql, $db_rooms);
        $result = add_attachments($db_rooms, $new_attachments, $rows[0][0]);
        
        if ($result)
        {
          $sql = "COMMIT";
          $db_rooms->exec($sql);
        }
        else
        {
          $sql = "ROLLBACK";
          $db_rooms->exec($sql);
        }
      }
      
      echo $result;
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
      $sql = "START TRANSACTION";
      $result = $db_rooms->exec($sql);
    
      $sql = "UPDATE bookings SET name='".$booking["name"]."', title='".$booking["title"]."', start='".$booking["start"]."', end='".$booking["end"]."', note='".$booking["note"]."' WHERE id='".$booking["id"]."'";
      $result = $db_rooms->exec($sql);
      
      if ($result > 0)
      {
        $result = add_attachments($db_rooms, $new_attachments, $booking["id"]);
        foreach($delete_attachments as $delete_attachment)
        {
          if ($delete_attachment != "")
          {
            $sql = "SELECT name FROM attachments WHERE id='".$delete_attachment."'";
            $rows = db_select($sql, $db_rooms);
            $name = $rows[0][0];
            
            $sql = "DELETE FROM attachments WHERE id='".$delete_attachment."'";
            $result = $db_rooms->exec($sql);
            if ($result > 0)
            {
              unlink("files/".$booking["id"]."/".$name);
            }
          }
        }
        
        if ($result)
        {
          $sql = "COMMIT";
          $db_rooms->exec($sql);
          $result = 1;
        }
        else
        {
          $sql = "ROLLBACK";
          $db_rooms->exec($sql);
        }
      }
      echo $result;
    }
    else
    {
      echo "kamar sudah dibooking pada tanggal tersebut";
    }
    break;
    
  case "delete_booking":
    $sql = "START TRANSACTION";
    $result = $db_rooms->exec($sql);
  
    $sql = "DELETE FROM bookings WHERE id='".$booking["id"]."'";
    $result = $db_rooms->exec($sql);
    if ($result > 0)
    {
      $sql = "SELECT name FROM attachments where booking_id='".$booking["id"]."'";
      $rows = db_select($sql, $db_rooms);
      if (count($rows) > 0)
      {
        foreach($rows as $row)
        {
          unlink("files/".$booking["id"]."/".$row[0]);
        }
        $sql = "DELETE FROM attachments WHERE booking_id='".$booking["id"]."'";
        
        $result = $db_rooms->exec($sql);
        if ($result == 0)
        {
          break;
        }
      }
      
      if ($result > 0)
      {
        if (is_dir("files/".$booking["id"])) 
        {
          rmdir("files/".$booking["id"]);
        }
        $sql = "COMMIT";
        $db_rooms->exec($sql);
        $result = 1;
      }
      else
      {
        $sql = "ROLLBACK";
        $db_rooms->exec($sql);
      }
    }
    echo $result;
    break;
    
  case "get_empty_rooms":
    $sql = "SELECT rooms.id, rooms.name, rooms.gender, bookings.id, bookings.name, bookings.title, bookings.start, bookings.end, bookings.note FROM bookings, rooms ";
    $sql .= "WHERE bookings.room_id=rooms.id AND ";
    $sql .= "((bookings.start <= '".$search["start"]."' AND '".$search["start"]."' <= end) ";
    $sql .= "OR (start <= '".$search["end"]."' AND '".$search["end"]."' <= end) ";
    $sql .= "OR ('".$search["start"]."' <= start AND start <= '".$search["end"]."') ";
    $sql .= "OR ('".$search["start"]."' <= end AND end <= '".$search["end"]."')) ";
    $sql .= "ORDER BY bookings.start, rooms.gender DESC, rooms.name, bookings.name, bookings.title ";
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
    
    $sql = "SELECT id, name, gender FROM rooms WHERE id NOT IN (".$room_ids.") ORDER BY gender DESC, name";
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
    
    print_bookings($rows_full, $db_rooms);
    break;
    
  case "get_bookings":
    $sql = "SELECT rooms.id, rooms.name, rooms.gender, bookings.id, bookings.name, bookings.title, bookings.start, bookings.end, bookings.note FROM bookings, rooms ";
    $sql .= "WHERE bookings.room_id=rooms.id AND ";
    $sql .= "(bookings.title LIKE '%".$search["title"]."%' AND bookings.name LIKE '%".$search["name"]."%') ";
    $sql .= "ORDER BY bookings.start, rooms.gender DESC, rooms.name, bookings.name, bookings.title";
    $rows = db_select($sql, $db_rooms);
      
    print_bookings($rows, $db_rooms);
    break;
}
?>