<?php
require_once("include/func_db.php");

$sql = "SELECT * FROM bookings";
$rows = db_select($sql, $db_rooms);
if (count($rows[0]) / 2 == 5 )
{
  $sql = "
    BEGIN TRANSACTION;
    CREATE TEMPORARY TABLE bookings_backup(id, title, start, end, room_id);
    INSERT INTO bookings_backup SELECT id, title, start, end, room_id FROM bookings;
    DROP TABLE bookings;
    CREATE TABLE bookings
    (
      id INTEGER PRIMARY KEY,
      name TEXT,
      title TEXT,
      start TEXT,
      end TEXT,
      note TEXT,
      room_id INTEGER
    );
    INSERT INTO bookings SELECT id, '', title, start, end, '', room_id FROM bookings_backup;
    DROP TABLE bookings_backup;
    COMMIT;";

  echo $db_rooms->exec($sql) or die(print_r($db_rooms->errorInfo(), true));
 }
?>