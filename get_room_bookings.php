<?php
require_once("include/func_db.php");

$sql = "select id, name, title, start, end, note from bookings where room_id='".$_POST["room_id"]."'";
$rows = db_select($sql, $db_rooms, "class");

echo json_encode($rows);
?>