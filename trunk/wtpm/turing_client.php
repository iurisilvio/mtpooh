<html>
<head>
<script src="prototype.js" type="text/javascript"></script>
</head>
<body>
<script type="text/javascript"><!--  
  function simulaMaquina()
  {
    $('machine').value = window.parent.getCodigoMaquina();
  
    new Ajax.Request('tpm_gg.php', 
    {
      method: 'post',
      onSuccess: function(transport) 
      {
        $('output').innerHTML = transport.responseText;
      },
      parameters: $('form').serialize(true)      
    });
  }
//--></script>

<form id="form" action="javascript:void(0)" method="post">
<input type="hidden" name="machine" id="machine" />
 <div>Input: </div>
 <div><input type="text" name="input" /></div>
</form>
</body>
</html>