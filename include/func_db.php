<?php
$db_rooms = db_init("sqlite:rooms");

 /** 
 * Create PDO
 *
 * @param String  $PServer    server location
 * @param String  $PUser      server location
 * @param String  $PPass      server location
 * @param String  $PDatabase  database's name that used
 *
 * @return  PDO created PDO
 */
function db_init($PServer)
{
  $db = new PDO($PServer);

  return $db;
}

/**
 * Execute select query
 * 
 * @param String  $PQuery
 * @param PDO     $PPdo
 *
 * @retun Array results
 */
function db_select($PQuery, $PPdo)
{
  $stmt = $PPdo->prepare($PQuery);
  $stmt->execute();
  $results = $stmt->fetchAll();
  return $results;
}
?>
