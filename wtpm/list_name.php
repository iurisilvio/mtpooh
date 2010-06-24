<?php 

header('Content-type: text/html; charset=utf-8');

// main
$dbh = new PDO('sqlite:./db.db');

$dbid = $dbh->quote($_GET['id']);
$asdf = $dbh->query("select name from machines where id = $dbid")->fetch(PDO::FETCH_ASSOC);
$name = $dbh->quote($asdf['name']);

// delete if requested
if ($_GET['delete'] != '') {
  $dbid = $dbh->quote($_GET['delete']);
  $dbh->exec("delete from machines where id = $dbid");

  $fdsa = $dbh->query("select count(id) as cnt from machines where name = $name")->fetch(PDO::FETCH_ASSOC);
  if ($fdsa['cnt'] == 0) {
    $dbh->exec("delete from machine_names where name = $name");
    header('Location: index.php');
    exit();
  }
}

foreach ($dbh->query("select * from machines where name = $name order by timestamp desc") as $row)
  $machines[] = $row;

?>
<html>
<head><title>Web Turing-Pooh Machine</title></head>
<body>
<a href="index.php">BACK</a><br />
Para deletar máquinas, basta clicar no [X].<br />
<?php foreach ($machines as $row): ?>
<a href="list_name.php?id=<?php echo $row['id'];?>&delete=<?php echo $row['id'];?>">[X]</a>
<a href="index.php?load_id=<?php echo $row['id'];?>"><?php echo htmlspecialchars($row['name']);?> (<?php echo htmlspecialchars($row['comment']);?>, input: <span style="font-family: monospace;"><?php echo htmlspecialchars(trim($row['input']));?></span>)</a><br />
<?php endforeach; ?>
</body>
</html>
