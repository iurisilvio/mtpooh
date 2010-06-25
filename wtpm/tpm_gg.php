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

  $turing_dir = 'turing4/';
  $basefilename = rand_str(50);
  $outputfilename = $basefilename . ".out";
  
  // creating diagram
  if ($_POST['tpmgg_lr'] === 'true')
  {
    $tpmgg_flags .= " -lr";
  }
  if ($_POST['tpmgg_ef'] === 'true')
  {
    $tpmgg_flags .= ' -ef';
  }
  if ($_POST['tpmgg_dl'] === 'true')
  {
    $tpmgg_flags .= ' -dl';
  }
  if (isset($_POST['tpmgg_ratio']) && $_POST['tpmgg_ratio'] !== 'auto')
  {
    $ratio = (float)$_POST['tpmgg_ratio'];
    $tpmgg_flags .= " -r $ratio";
  }
  
  $machinefd = fopen($basefilename, "w+");
  fwrite($machinefd, $_POST['machine']);
  fclose($machinefd);
     
  $dia_outlines = array();
  exec("{$turing_dir}graph-gen $basefilename $tpmgg_flags -o $outputfilename", $dia_outlines);
  $gif = @file_get_contents($outputfilename);
  
  if ($gif !== false)
  {
    echo(base64_encode($gif));      
  }
  else 
  {
    echo("Errors were found:\n\n" . join("\n", $dia_outlines));
  }
  
  unlink($basefilename);
  @unlink($basefilename . ".graph");
  @unlink($outputfilename);