  <?php
  
  // creating diagram
  $tpmgg_flags = "";
  
  if ($_POST['tpmgg_lr'] === 'true')
  {
    $tpmgg_flags .= " -lr";
  }
  if ($_POST['tpmgg_ef'] === 'true')
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
    
?>
<html>
<body>
Hello World
</body>
</html>