<html>
<body>

<script type="text/javascript"><!--
  function geraGrafo()
  {
    alert(window.parent.getCodigoMaquina());
  }
//--></script>

<form action="tpm_gg.php" method="post">
<input type="hidden" name="machine" id="machine" />
<a href="javascript:void(0)" onclick="geraGrafo()" >Gerar Grafo</a>
</form>
</body>
</html>