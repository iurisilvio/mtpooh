<html>
<head>
<script src="prototype.js" type="text/javascript"></script>
</head>
<body>
<script type="text/javascript"><!--  
  function simulaMaquina()
  {
    $("output").innerHTML = 'Gerando...';
    $('machine').value = window.parent.getCodigoMaquina();
  
    new Ajax.Request('turing.php', 
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
 <div><input type="text" name="input" /><input type="submit" value="Go!" onclick="javascript:simulaMaquina()" /></div>
 <div style="padding-top: 20px;">Output:</div>
 <div style="whitespace: pre; font-family: Consolas, Courier New;" id="output">
 &nbsp;
 </div>
</form>
</body>
</html>