<?php
$dbh = new PDO('sqlite:./db.db');
$dbh->exec('create table machines (id, name, machine, input);');
?>
