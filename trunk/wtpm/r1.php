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

$self_name = basename($_SERVER['SCRIPT_NAME']);
$turing_dir = 'turing4/';

$dbh = new PDO('sqlite:./db.db');

$output = null;
$dia_output = null;
$dia_blob = null;
if ($_POST['do'] === 'true') {
  // putting data into database
  $ins_id = $dbh->quote(rand_str(50));
  $ins_name = $dbh->quote($_POST['name']);
  $ins_comment = $dbh->quote($_POST['comment']);
  $ins_machine = $dbh->quote($_POST['machine']);
  $ins_input = $dbh->quote($_POST['input']);
  $ins_timestamp = $dbh->quote(time());
  $ins_hash = $dbh->quote(make_hash($_POST['name'], $_POST['comment'], $_POST['machine'], $_POST['input']));
  $dbh->exec("insert into machines values ($ins_id, $ins_name, $ins_machine, $ins_input, $ins_timestamp, $ins_hash, $ins_comment);");
  $dbh->exec("insert into machine_names values ($ins_name, $ins_id, $ins_timestamp);");

  // executing program
  $fname = rand_str(50) . '.tm';
  $fp = fopen($fname, 'w');
  fwrite($fp, $_POST['machine']);
  fclose($fp);
  $fname2 = rand_str(50) . '.in';
  $fp2 = fopen($fname2, 'w');
  fwrite($fp2, $_POST['input']);
  fclose($fp2);
  $outlines = array();
  exec("{$turing_dir}turing $fname $fname2", $outlines);
  $output = '';
  foreach ($outlines as $line)
    $output .= $line . "\n";

  // creating diagram
  $tpmgg_flags = "";
  
  if ($_POST['tpmgg_lr'] === 'true')
  {
    $tpmgg_flags .= " -lr";
  }
  else if ($_POST['tpmgg_ef'] === 'true')
  {
    $tpmgg_flags .= ' -ef';
  }
  
  $dia_gfname = $fname . '.graph';
  $dia_fname = $dia_gfname . '.gif';
  $dia_outlines = array();
  exec("{$turing_dir}graph-gen $fname $tpmgg_flags", $dia_outlines);
  $dia_blob = file_get_contents($dia_fname);
  unlink($fname);
  unlink($fname2);
  unlink($dia_gfname);
  unlink($dia_fname);
  $dia_output = '';
  foreach ($dia_outlines as $line)
    $dia_output .= '<!--' . htmlspecialchars($line) . '-->' . "\n";
}

// fetch list of programs
$machine_names = array();
foreach ($dbh->query('select name, id from machine_names order by timestamp desc') as $row)
  $machine_names[] = array('name' => $row['name'], 'id' => $row['id']);

$load_id = null;
if (isset($_GET['load_id']))
  $load_id = $_GET['load_id'];
$loaded_name = 'NOOOOME';
$loaded_comment = 'máquina de exemplo';
$loaded_machine = file_get_contents("{$turing_dir}example/multiplicador.tm");
$loaded_input = '0010001';

if ($_POST['do'] === 'true') {
  $loaded_name = $_POST['name'];
  $loaded_comment = $_POST['comment'];
  $loaded_machine = $_POST['machine'];
  $loaded_input = $_POST['input'];
}

foreach ($dbh->query('select id, name, machine, input, comment from machines where id = ' . $dbh->quote($load_id)) as $row) {
  $loaded_name = $row['name'];
  $loaded_comment = $row['comment'];
  $loaded_machine = $row['machine'];
  $loaded_input = $row['input'];
}

?>
<html>
<head><title>Web Turing-Pooh Machine</title></head>
<body>
<table>
<tr>
<td style="vertical-align: top; border: 1px solid black;">
<form method="post" action="<?php echo $self_name;?><?php if ($load_id != null) echo '?load_id='.$load_id;?>">
  <input type="hidden" name="do" value="true" />
  Nome de sua maquina: (só pra identificacao):<br />
  <textarea name="name" rows="1" style="width: 600px; font-family: monospace;"><?php if (!is_null($loaded_name)) echo htmlspecialchars($loaded_name); ?></textarea><br />
  Breve comentário (também para identificação):<br />
  <textarea name="comment" rows="1" style="width: 600px; font-family: monospace;"><?php if (!is_null($loaded_comment)) echo htmlspecialchars($loaded_comment); ?></textarea><br />
  Maquina: (em formato pooh):<br />
  <textarea name="machine" rows="80" style="width: 600px; font-family: monospace;"><?php if (!is_null($loaded_machine)) echo htmlspecialchars($loaded_machine); ?></textarea><br />
  Input:<br />
  <textarea name="input" rows="4" style="width: 600px; font-family: monospace;"><?php if (!is_null($loaded_input)) echo htmlspecialchars($loaded_input); ?></textarea><br />
  <input type="submit" />
</form>
Máquinas submetidas: <br />
<?php foreach ($machine_names as $row): ?>
<a href="list_name.php?id=<?php echo $row['id'];?>"><?php if ($row['name'] != '') echo htmlspecialchars($row['name']); else echo 'VAZIO';?></a><br />
<?php endforeach; ?>
</td>
<td style="vertical-align: top; border: 1px solid black;">
<div>
Diagrama: <input type="checkbox" value="true" name="tpmgg_lr"> Horizontal&nbsp;&nbsp;<input type="checkbox" value="true" name="tpmgg_ef">Extrair Subrotinas
</div>
<br />
<?php if ($dia_blob !== null): ?>
<img src="data:image/gif;base64,<?php echo base64_encode($dia_blob);?>" /><br />
<?php endif; ?>
Output:<br />
<pre>
<?php echo $output; ?>
</pre>
</td>
</tr>
</table>
</body>
</html>
<?php
echo $dia_outlines;
?>