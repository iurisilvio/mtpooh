<html>
<head>
<script src="prototype.js" type="text/javascript"></script>
</head>
<body>
<script type="text/javascript"><!--
  function geraGrafo()
  {
    $('errorsContainer').style.display = 'none';
    $('graphContainer').style.display = 'none';
  
    new Ajax.Request('tpm_gg.php', 
    {
      method: 'post',
      onSuccess: function(transport) 
      {
        var response = transport.responseText;
        alert(response.length);
        if (response.substring(0, 5) == 'Error')
        {
          $('errors').innerHTML = response;
          $('errorsContainer').style.display = 'block';
        }
        else
        {      
          $('graphContainer').innerHTML = '<img src="data:image/gif;base64,' + response + '" />';
          $('graphContainer').style.display = 'block';
        }
      },
      parameters: 
      {
        machine: window.parent.getCodigoMaquina()
      }
    });
  }
//--></script>

<form action="tpm_gg.php" method="post">
<input type="hidden" name="machine" id="machine" />
<div>
<a href="javascript:void(0)" onclick="geraGrafo()" >Gerar Grafo</a>
</div>
<div id="graphContainer" style="display: none;">
</div>
<div id="errorsContainer" style="display: none;">
<pre id="errors">
</pre>
</div>
</form>
</body>
</html>