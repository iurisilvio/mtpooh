<?php
$dbh = new PDO('sqlite:./db.db');
$dbh->exec("delete from machine_names");
foreach ($dbh->query('select * from machines order by timestamp asc') as $row) {
  $ins_name = $dbh->quote($row['name']);
  $ins_id = $dbh->quote($row['id']);
  $ins_timestamp = $dbh->quote($row['timestamp']);
  $dbh->exec("insert into machine_names values ($ins_name, $ins_id, $ins_timestamp);");
}

?>
