<?php
require_once("include/func_db.php");

$sql = "select id, title, start, end from bookings where room_id='".$_POST["room_id"]."'";
$rows = db_select($sql, $db_rooms);

echo json_encode($rows);
?>