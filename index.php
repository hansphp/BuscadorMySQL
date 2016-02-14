<?php include_once("config.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Documento sin título</title>
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
  padding: 30px;
}
</style>
<script>
var load = {
			'tables' : function(database){
				$.getJSON( 'load.php?database=' + database, function( data ) {
				  var items = []; 
				  $.each( data, function( key, val ) {
					items.push("<tr> <td>" + val.TABLE_NAME + "</td> <td>" + val.ENGINE + "</td><td>" + val.TABLE_COLLATION + "</td> <td>" + val.TABLE_ROWS + "</td> </tr>");
				  });
				  
				  $( "#tables" ).html(items.join( "" ));
				});	
			}
};

$(function() {
	$('#database').change(function() {
	  load.tables( this.value );
	});
});
</script>

<body role="document">
<?php
$databases = $SQL->consulta("SELECT DISTINCT TABLE_SCHEMA FROM information_schema.TABLES WHERE TABLE_SCHEMA <> 'information_schema'");
?>

<div class="page-header">
        <h1>Tables</h1>
      </div>
      <div class="row">
        <div class="col-md-6">
            <div class="form-group">
            <label for="database">Base de datos:</label>
            <select class="form-control" id="database">
            <option>Seleccionar</option>
                <?php foreach($databases as $database) echo "<option>{$database->TABLE_SCHEMA}</option>" ?>
            </select>
            </div>
        </div>
        <div class="col-md-6">
        
        <div>
        </div>
        
        
        <div class="form-group">
        <label for="email">Email address:</label>
        <input type="email" class="form-control" id="email">
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
              </tr>
            </thead>
            <tbody id="tables">
              <tr>
                <td>1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
</body>
</html>
