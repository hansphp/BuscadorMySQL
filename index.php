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
</head>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<style>
body {
  padding-left: 30px;
  padding-right: 30px;
}
td.wait{
	padding:10px;
	font-weight:bold;
	color:#990;
	text-align:center;
}
</style>
<script>
var db = {
	instance: null,
	initialize: function(){
		db.instance = openDatabase('buscado', '1.0', 'Base de datos Cache del Buscador', 4 * 1024 * 1024);
		db.create.tables();
		db.instance.transaction(function (tx) {
			tx.executeSql('SELECT * FROM tables WHERE id >= 0', [], function (tx, results) {
				if(results.rows.length > 0){
					// db.reset.tables();
					$("#database").val(localStorage.getItem('database'));
					$("#filter").val(localStorage.getItem('filter'));
					$("#noempty").prop('checked', localStorage.getItem('noempty')=='true');
					$('#database').prop('disabled', true);
					db.populate.tables($('#filter').val(), localStorage.getItem('noempty')=='true');
				}
			});
		});
		console.log('Base de datos inicializada');
	},
	create:{
		tables: function(){
			db.instance.transaction(function (tx) {
				tx.executeSql("CREATE TABLE IF NOT EXISTS " +
                  "tables(id INTEGER PRIMARY KEY ASC, tabla TEXT, motor TEXT, cotejamiento TEXT, filas INTEGER, estado VARCHAR)", []);
			  
			});
		}
	},
	reset:{
		tables: function(){
			db.instance.transaction(function (tx) {
				tx.executeSql("DROP TABLE tables");
			});
			db.create.tables();
		}
	},
	load: {
		tables: function(idx, table, engine, cotejamiento, filas){
			db.instance.transaction(function (tx) {
			  tx.executeSql('INSERT INTO tables VALUES (?, ?, ?, ?, ?, ?)', [idx, table, engine, cotejamiento, filas, 'process']);
			});
		}
	},
	populate:{
		tables: function (aux, noempty){
			var filas = -1;
			var items = [];
			db.instance.transaction(function (tx) {
				if(noempty){
					filas = 0;
				}
				tx.executeSql('SELECT * FROM tables WHERE (tabla LIKE ?) AND filas > ' + filas, ['%'+aux+'%'], function (tx, results) {
				  for (var i = 0; i < results.rows.length; i++) {
					// console.log(results.rows.item(i).tabla);
					items.push('<tr data-id="'+results.rows.item(i).id+'"><td>' + results.rows.item(i).tabla + "</td> <td>" + results.rows.item(i).motor + "</td><td>" + results.rows.item(i).cotejamiento + "</td> <td>" + results.rows.item(i).filas + "</td><td><img src='"+results.rows.item(i).estado+".png'></td> </tr>");
				  }
				   $( "#tables" ).html(items.join( "" ));
				   $('#filter').prop('disabled', false);
				   $('#noempty').prop('disabled', false);
				   $('#reset').prop('disabled', false);
				});
			});
		}
	}
}

var load = {
	Threads: 4,
	'tables' : function(database){
		db.reset.tables(); // WebSQL
		$( "#tables" ).html('<td colspan="5" class="wait"><img src="loading.gif" height="32px"></td>');
		$.getJSON( 'load.php?database=' + database, function( data ) {
		  $.each( data, function( key, val ) {
			db.load.tables(key, val.TABLE_NAME, val.ENGINE, val.TABLE_COLLATION, val.TABLE_ROWS); // WebSQL
		  });
		}).done(function() {
			db.populate.tables('');
		});	
	},
	'example' : function(){
		var jqxhr = $.post( "ajax.php", function(data) {
		  console.info( "success");
		  console.log(data);
		})
		  .done(function() {
			console.info( "second success" );
		  })
		  .fail(function() {
			console.info( "error" );
		  })
		  .always(function() {
			console.info( "finished" );
		});
	}
};

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
});
</script>

<body role="document">
<?php
$databases = $SQL->consulta("SELECT DISTINCT TABLE_SCHEMA FROM information_schema.TABLES WHERE TABLE_SCHEMA <> 'information_schema'");
?>

<div class="page-header">
        <h1>Buscador MySQL</h1>
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
          <table class="table">
            <thead>
              <tr>
                <th>Tabla</th>
                <th>Motor</th>
                <th>Cotejamiento</th>
                <th>Filas</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="tables">
              <tr>
              	<td colspan="5" class="wait">Lista de tablas en la base de datos</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-6">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
            	<a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a>
            </li>
            <li role="presentation">
            	<a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a>
            </li>
            <li role="presentation">
            	<a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Messages</a>
            </li>
            <li role="presentation">
            	<a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a>
            </li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">...Hilo1</div>
            <div role="tabpanel" class="tab-pane" id="profile">...Hilo2</div>
            <div role="tabpanel" class="tab-pane" id="messages">...Hilo3</div>
            <div role="tabpanel" class="tab-pane" id="settings">...Hilo4</div>
          </div>
        </div>
      </div>
</body>
</html>
