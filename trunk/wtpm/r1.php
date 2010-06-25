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
<head>
<link rel="stylesheet" type="text/css" href="tpm_wb+.css" />
<title>Web Turing-Pooh Machine</title>
</head>
<body>
<script type="text/javascript"><!--
  // TODO: Extrair para arquivo .js
  
  function $(id)
  {
    return document.getElementById(id);
  }
  
  function exibeAbaMaquina(index)
  {
    for (var i = 0; i < 3; ++i)
    {
      $('maquinaAbaConteudo' + i).className = (index == i) ? 'abaConteudoAtiva' : 'abaConteudo';
      $('maquinaAba' + i).className = (index == i) ? 'abaAtiva' : 'aba';
    }
  }
  
  function getCodigoMaquina()
  {
    return $('machine').value;
  }
  
  function getInputPadrao()
  {
    return $('input').value;
  }
  
//--></script>
<form method="post" action="<?php echo $self_name;?><?php if ($load_id != null) echo '?load_id='.$load_id;?>">
<div id="mainContainer">
  <div id="maquinasSubmetidas" style="float: right; width: 400px; height: 200px; overflow: scroll; border: black solid 1px; padding: 10px;">
    <div>Máquinas submetidas:</div>
    <div>
      <?php foreach ($machine_names as $row): ?>
        <a href="list_name.php?id=<?php echo $row['id'];?>"><?php if ($row['name'] != '') echo htmlspecialchars($row['name']); else echo 'VAZIO';?></a><br />
      <?php endforeach; ?>
    </div>
  </div>

  <div id="metadataContainer" style="width: 500px;">
  <input type="hidden" name="do" value="true" />
  <div>Nome de sua maquina: (só pra identificacao):</div>
  <textarea name="name" rows="1" style="width: 400px; font-family: monospace;"><?php if (!is_null($loaded_name)) echo htmlspecialchars($loaded_name); ?></textarea><br />
  <div>Breve comentário (também para identificação):</div>
  <textarea name="comment" rows="4" style="width: 400px; font-family: monospace;"><?php if (!is_null($loaded_comment)) echo htmlspecialchars($loaded_comment); ?></textarea><br />
  <div>Input Padrão:</div>
  <textarea name="input" id="input" rows="1" style="width: 400px; font-family: monospace;"><?php if (!is_null($loaded_input)) echo htmlspecialchars($loaded_input); ?></textarea><br />
  </div>
  <div>Maquina: (em formato pooh):</div>
  <div class="abaCabecalho">
    <div class="abaAtiva" onclick="javascript:exibeAbaMaquina(0)" id="maquinaAba0">Código</div>
    <div class="abaSeparador">&nbsp;</div>
    <div class="aba" onclick="javascript:exibeAbaMaquina(2)" id="maquinaAba2">Simulador</div>
    <div class="abaSeparador">&nbsp;</div>
    <div class="aba" onclick="javascript:exibeAbaMaquina(1)" id="maquinaAba1">Diagrama de Estados</div>
    <div>&nbsp;</div>
  </div>
  <div class="abaContainer">
    <div class="abaConteudoAtiva" id="maquinaAbaConteudo0">
      <textarea id="machine" name="machine" style="width: 100%; height: 100%; font-family: monospace;"><?php if (!is_null($loaded_machine)) echo htmlspecialchars($loaded_machine); ?></textarea>
    </div>
    <div class="abaConteudo" id="maquinaAbaConteudo2">
      <iframe src="turing_client.php" style="width: 100%; height:100%;" ></iframe>
    </div>
    <div class="abaConteudo" id="maquinaAbaConteudo1">
      <iframe scrolling="no" src="tpm_gg_client.php" style="width: 100%; height:100%;" ></iframe>
    </div>
  </div>
  <input type="submit" value="Gerar Nova Versão da Máquina" id="btUpdateMachine" />
</form>
</body>
</html>
