<html>
<head>
<script src="prototype.js" type="text/javascript"></script>
</head>
<body>
<script type="text/javascript"><!--

  $('machine').value = window.parent.getCodigoMaquina();
  
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
        if (response.substring(0, 5) == 'Error')
        {
          $('errors').innerHTML = response;
          $('errorsContainer').style.display = 'block';
        }
        else
        {      
          $('graph').src = 'data:image/gif;base64,' + response;
          $('graphContainer').style.display = 'block';
        }
      },
      parameters: $('form').serialize(true)      
    });
  }
//--></script>

<form id="form" action="tpm_gg.php" method="post">
<input type="hidden" name="machine" id="machine" />
<div>
<a href="javascript:void(0)" onclick="geraGrafo()" >Gerar Grafo (não funciona no IE)</a>
</div>
<div>
<input type="checkbox" name="tpmgg_lr" value="true"> Grafo Horizontal
<input type="checkbox" name="tpmgg_ef" value="true"> Subrotinas em Grafos Separados | 
Razão Altura / Largura:
<select name="tpmgg_ratio">
  <option value="auto">Automático</option>
  <option value="0.33">1 / 3</option>
  <option value="0.50">1 / 2</option>
  <option value="0.67">2 / 3</option>
  <option value="0.80">4 / 5</option>
  <option value="1.0">1 / 1</option>
  <option value="1.25">5 / 4</option>
  <option value="1.50">3 / 2</option>
  <option value="2">2 / 1</option>
  <option value="3">3  / 1</option>
</select>
</div>
<div id="graphContainer" style="display: none;">
<img id="graph" src="" />
</div>
<div id="errorsContainer" style="display: none;">
<pre id="errors">
</pre>
</div>
</form>
</body>
</html>