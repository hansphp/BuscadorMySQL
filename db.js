// JavaScript Document
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
					$("#buscar").val(localStorage.getItem('buscar'));
					$("#noempty").prop('checked', localStorage.getItem('noempty')=='true');
					$('#database').prop('disabled', true);
					db.populate.tables($('#filter').val(), localStorage.getItem('noempty')=='true');
				}
			});
		});
		db.reset.columns();
		console.log('Base de datos inicializada');
	},
	create:{
		tables: function(){
			db.instance.transaction(function (tx) {
				tx.executeSql("CREATE TABLE IF NOT EXISTS " +
                  "tables(id INTEGER PRIMARY KEY ASC, tabla TEXT, motor TEXT, cotejamiento TEXT, filas INTEGER, estado VARCHAR, cargado VARCHAR)", []);
			});
		},
		columns: function(){
			db.instance.transaction(function (tx) {
				tx.executeSql("CREATE TABLE IF NOT EXISTS " +
                  "columns(id INTEGER PRIMARY KEY ASC, tabla TEXT, tabla_id INTEGER, columna TEXT, tipo TEXT, type TEXT, size INTEGER, estado VARCHAR)", []);
			});
		}
	},
	reset:{
		tables: function(){
			db.instance.transaction(function (tx) {
				tx.executeSql("DROP TABLE tables");
			});
			db.create.tables();
		},
		columns: function(){
			db.instance.transaction(function (tx) {
				tx.executeSql("DROP TABLE columns");
			});
			db.create.columns();
		}
	},
	load: {
		tables: function(idx, table, engine, cotejamiento, filas){
			db.instance.transaction(function (tx) {
			  tx.executeSql('INSERT INTO tables VALUES (?, ?, ?, ?, ?, ?, ?)', [idx, table, engine, cotejamiento, filas, 'process', 'NO']);
			});
		},
		columns: function(tabla, tabla_id, columna, tipo, type, size){
			db.instance.transaction(function (tx) {
			  tx.executeSql('INSERT INTO columns (tabla, tabla_id, columna, tipo, type, size, estado) VALUES (?, ?, ?, ?, ?, ?, ?)', [tabla, tabla_id, columna, tipo, type, size, 'process']);
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
					items.push('<tr data-id="'+results.rows.item(i).id+'"><td>' + results.rows.item(i).tabla + "</td> <td>" + results.rows.item(i).motor + "</td><td>" + results.rows.item(i).cotejamiento + "</td> <td>" + results.rows.item(i).filas + "</td><td><img src='"+results.rows.item(i).estado+".png'></td> </tr>");
				  }
				  
				   $( "#tables" ).html(items.join( "" ));
				   $('#filter').prop('disabled', false);
				   $('#noempty').prop('disabled', false);
				   $('#reset').prop('disabled', false);
				});
			});
		},
		columns : function (id, th, callback){
			db.instance.transaction(function (tx) {
				var items = [];
				tx.executeSql('SELECT * FROM columns WHERE tabla_id = ?', [id], function (tx, results) {
				  for (var i = 0; i < results.rows.length; i++) {
					  items.push('<tr><td>'+results.rows.item(i).columna+'</td><td>'+results.rows.item(i).tipo+'</td><td>'+results.rows.item(i).size+'</td><td><img src="'+results.rows.item(i).estado+'.png"></td></tr><tr>');
					   $( '#columns-' + th ).html(items.join( "" ));
				  }
				  $( '#thread-img-' + th ).css('display', 'none');
				  callback();
				  /*
				   $( "#tables" ).html(items.join( "" ));
				   $('#filter').prop('disabled', false);
				   $('#noempty').prop('disabled', false);
				   $('#reset').prop('disabled', false);*/
				});
			});
		}
	}
}
