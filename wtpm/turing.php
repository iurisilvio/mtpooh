<?php
  
  $turing_dir = 'turing4/';
  $basefilename = (string)time(); 
  $outputfilename = $basefilename . ".out";

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