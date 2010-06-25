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
 <div><input type="text" id="input" name="input" style="width: 80%; font-family: monospace;" /><input type="submit" value="Go!" onclick="javascript:simulaMaquina()" /></div>
 <script type="text/javascript"><!--
  $('input').value = window.parent.getInputPadrao();
 //--></script>
 <div style="padding-top: 20px;">Output:</div>
 <div style="height: 480px; overflow: auto;">
  <pre id="output">
   <pre>
 </div>
</form>
</body>
</html>