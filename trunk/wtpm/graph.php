<?php

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

session_start();

$param = $_SESSION['tpmgg'];

if ($_POST['send_data'] === 'true') {
	$param = array(
		'lr'      => $_POST['lr'],
		'ef'      => $_POST['ef'],
		'dl'      => $_POST['dl'],
		'ratio'   => $_POST['ratio'],
		'machine' => $_POST['machine']
	);
}

$_SESSION['tpmgg'] = $param;

$turing_dir = 'turing4' . DIRECTORY_SEPARATOR;

// creating diagram
$tpmgg_flags = '';
if ($param['lr'] === 'true')
	$tpmgg_flags .= " -lr";
if ($param['ef'] === 'true')
	$tpmgg_flags .= ' -ef';
if ($param['dl'] === 'true')
	$tpmgg_flags .= ' -dl';
if (isset($param['ratio']) && $param['ratio'] !== 'auto') {
	$ratio = (float) $param['ratio'];
	$tpmgg_flags .= " -r $ratio";
}

$basefilename = rand_str(50);
$outputfilename = $basefilename . ".out";

$machinefd = fopen($basefilename, "w+");
fwrite($machinefd, $param['machine']);
fclose($machinefd);

$dia_outlines = array();
exec("{$turing_dir}graph-gen $basefilename $tpmgg_flags -o $outputfilename", $dia_outlines);
$gif = @file_get_contents($outputfilename);

if ($_POST['send_data'] === 'true') {
	header('Content-type: text/html; charset=utf-8');
	if ($gif !== false)
		echo json_encode(array('success' => true));
	else
		echo json_encode(array('success' => false, 'errorText' => join("\n", $dia_outlines)));
} else {
	if ($gif !== false) {
		header('Content-type: image/gif');
		echo($gif);
	}
}

@unlink($basefilename);
@unlink($basefilename . ".graph");
@unlink($outputfilename);
