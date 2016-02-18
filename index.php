<?php include_once("config.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Buscador MySQL</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<link rel="stylesheet" href="estilo.css">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="db.js"></script>
<script src="load.js"></script>
<script>
$(function() {
	db.initialize();
	$('#database').change(function() {
		localStorage.setItem("database", this.value);
		$('#database').prop('disabled', true);
		load.tables( this.value );
		load.example();
		console.warn('Fin de llamadas');
	});
	$('#filter').keyup(function(){
		localStorage.setItem("filter", this.value);
		db.populate.tables(this.value, $('#noempty').prop('checked'));
	});
	$('#buscar').keyup(function(){
		localStorage.setItem("buscar", this.value);
	});
	$('#noempty').change(function() {
		localStorage.setItem("noempty", this.checked);
		db.populate.tables($('#filter').val(), this.checked);
	});
	$('#reset').click(function(){
		db.reset.tables();
		$('#filter').prop('disabled', true).val('');
		$('#noempty').prop('disabled', true).prop('checked', false);
		$('#reset').prop('disabled', true);
		$('#database').prop('disabled', false);
		localStorage.setItem("filter", '');
		localStorage.setItem("noempty", false);
		$('#tables').html('<tr><td colspan="5" class="wait">Lista de tablas en la base de datos</td></tr>');
		
	});
	$('#boton').click(function(){
		console.warn("Buscando:"+$('#buscar').val());
		var id = -1;
		$('#tables tr').each(function(i, e) {
			// Termina para solo servir al primer elemento.
			id = $(e).attr('data-id');
        });
		load.columns(id);
	});
});
</script>
</head>
<body role="document">
<?php
$databases = $SQL->consulta("SELECT DISTINCT TABLE_SCHEMA FROM information_schema.TABLES WHERE TABLE_SCHEMA <> 'information_schema'");
?>

<div class="page-header">
        <img src="logo.png">
</div>
      <div class="row">
        <div class="col-md-6">
         <div class="col-md-6">
            <div class="form-group">
            <label for="database">Base de datos:</label>
            <select class="form-control" id="database">
            <option>Seleccionar</option>
                <?php foreach($databases as $database) echo "<option>{$database->TABLE_SCHEMA}</option>" ?>
            </select>
            </div>
            <div class="form-group">
            <button type="button" class="btn btn-xs btn-danger" disabled id="reset">Reiniciar</button>
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group">
            <label for="filter">Filtro rápido:</label>
            <input type="text" disabled class="form-control" id="filter">
            </div>
            <div class="form-group">
            No mostrar tablas vacias: <input type="checkbox" disabled id="noempty">
            </div>
            </div>
        </div>
        <div class="col-md-6">
        	<div class="col-md-9">
                 <div class="form-group">
                <label for="buscar">Buscar fragmento:</label>
                <input type="buscar" class="form-control" id="buscar">
                </div>
            </div>
            <div class="col-md-3">
                 <div class="form-group">
                <label for="boton">&nbsp;</label>
                <button type="button" id="boton" class="form-control btn btn-default">Buscar</button>
                </div>
            </div>
        <div>
        </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Tabla</th>
                <th>Motor</th>
                <th>Cotejamiento</th>
                <th>Filas</th>
                <th class="text-right">Progreso</th>
                <th class="text-right">Estado</th>
              </tr>
            </thead>
            <tbody id="tables">
              <tr>
              	<td colspan="6" class="wait">Lista de tablas en la base de datos</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-6">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist" id="tabs-title">
            <li role="presentation" class="active">
            	<a href="#home" aria-controls="home" role="tab" data-toggle="tab">Resultados</a>
            </li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content" id="tabs-content">
            <div role="tabpanel" class="tab-pane active" id="home">
            <table class="table">
            <thead>
              <tr>
                <th>Tabla</th>
                <th>Campo</th>
                <th>Tipo de dato</th>
                <th>Tamaño</th>
                <th>Coincidencias</th>
              </tr>
            </thead>
            <tbody id="founds">
              <tr>
              	<td colspan="5" class="wait">Lista de coinicidencias en la base de datos</td>
              </tr>
            </tbody>
          </table>
            
            </div>
          </div>
        </div>
      </div>
</body>
</html>
