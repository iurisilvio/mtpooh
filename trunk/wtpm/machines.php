<?php 

header('Content-type: text/html; charset=utf-8');

// general functions
function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
{
	$chars_length = (strlen($chars) - 1);
	$string = $chars{rand(0, $chars_length)};
	for ($i = 1; $i < $length; $i = strlen($string)) {
		$r = $chars{rand(0, $chars_length)};
		if ($r != $string{$i - 1}) $string .=  $r;
	}
	return $string;
}

function make_hash($name, $comment, $machine, $input) {
  return hash('md5', hash('sha256', $name) . hash('sha256', $comment) . hash('sha256', $machine) . hash('sha256', $input));
}

// main
$dbh = new PDO('sqlite:./db.db');

if ($_POST['op'] === 'get') {
	$dbid = $dbh->quote($_POST['id']);
	$machine = $dbh->query("select * from machines where id = $dbid")->fetch(PDO::FETCH_ASSOC);
	echo json_encode(array(
		'id'      => $machine['id'],
		'name'    => htmlspecialchars($machine['name']),
		'comment' => htmlspecialchars($machine['comment']),
		'machine' => htmlspecialchars($machine['machine']),
		'input'   => htmlspecialchars($machine['input'])
	));
} else if ($_POST['op'] === 'store') {
	$dbid = $dbh->quote(rand_str(50));
	$dbname = $dbh->quote($_POST['name']);
	$dbcomment = $dbh->quote($_POST['comment']);
	$dbmachine = $dbh->quote($_POST['machine']);
	$dbinput = $dbh->quote($_POST['input']);
	$dbtimestamp = $dbh->quote(time());
	$dbhash = $dbh->quote(make_hash($_POST['name'], $_POST['comment'], $_POST['machine'], $_POST['input']));
	$dbh->exec("insert into machines values ($dbid, $dbname, $dbmachine, $dbinput, $dbtimestamp, $dbhash, $dbcomment)");
	$dbh->exec("insert into machine_names values ($dbname, $dbid, $dbtimestamp)");
	echo json_encode(array('success' => true));
} else if ($_POST['op'] === 'list_all') {
	$response = array();
	foreach ($dbh->query("select name, id from machine_names order by timestamp desc") as $row) {
		$response[] = array(
			'name' => htmlspecialchars($row['name']),
			'id' => $row['id']
			);
	}
	echo json_encode($response);
} else if($_POST['op'] === 'list_name') {
	$dbid = $dbh->quote($_POST['id']);
	$asdf = $dbh->query("select name from machines where id = $dbid")->fetch(PDO::FETCH_ASSOC);
	$dbname = $dbh->quote($asdf['name']);

	$response = array();
	$response['name'] = htmlspecialchars($asdf['name']);

	// delete if requested
	if ($_POST['delete'] === 'true') {
		$dbh->exec("delete from machines where id = $dbid");

		$fdsa = $dbh->query("select count(id) as cnt from machines where name = $dbname")->fetch(PDO::FETCH_ASSOC);
		if ($fdsa['cnt'] == 0) {
			$dbh->exec("delete from machine_names where name = $dbname");
			$response['goback'] = true;
			echo json_encode($response);
			exit();
		} else {
			$asdf = $dbh->query("select id from machines where name = $dbname limit 1")->fetch(PDO::FETCH_ASSOC);
			$dbid = $dbh->quote($asdf['id']);
			$dbh->exec("update machine_names set id = $dbid where name = $dbname");
		}
	} 
	$response['goback'] = false;

	$response['items'] = array();
	foreach ($dbh->query("select name, id, comment, input from machines where name = $dbname order by timestamp desc") as $row)
		$response['items'][] = array(
			'id' => $row['id'],
			'name' => htmlspecialchars($row['name']),
			'comment' => htmlspecialchars($row['comment']),
			'input' => htmlspecialchars($row['input'])
		);

	echo json_encode($response);
} else {

}