  <?php

  $turing_dir = 'turing4/';
  $basefilename = (string)time(); 
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
  if (isset($_POST['tpmgg_ratio']))
  {
    $ratio = (float)$_POST['tpmgg_ratio'];
    $tpmgg_flags .= " -r $ratio";
  }
  
  $machinefd = fopen($basefilename, "w+");
  fwrite($machinefd, $_POST['machine']);
  fclose($machinefd);
     
  $dia_outlines = array();
  exec("{$turing_dir}graph-gen $basefilename $tpmgg_flags -o $outputfilename", $dia_outlines);
  $gif = file_get_contents($outputfilename);
  echo($gif);  
  unlink($basefilename);
  unlink($basefilename . ".graph");
  unlink($outfilename);
?>
<html>
<body style="margin: 0px; padding: 0px;">
<?php if ($dia_blob !== null): ?>
<img src="data:image/gif;base64,<?php echo base64_encode($gif);?>" /><br />
<?php endif; ?>
<!--  como q é o else? -->
<?php if ($dia_blob === null): ?>
<div style="padding: 10px;">
<pre>
<? echo($dia_outlines); ?>
</pre>
</div>
<?php endif; ?>