// JavaScript Document
var load = {
	threads:{
		max:4,
		counter:0,
		process:Array(),
		count: function(){ return load.threads.process.length },
		add: function(id){
			if(load.threads.process.indexOf(id) < 0){
				load.threads.process.push(id);
				var th = load.threads.counter++;
				console.info("Agregando thread: " + th + " para tabla " + id);
				$('#tabs-title').append('<li role="presentation"><a href="#thread-' + th + '" aria-controls="thread-' + th + '" role="tab" data-toggle="tab">Thread [' + th + ']<img src="loading.gif" id="thread-img-' + th + '"></a></li>');
				
				$('#tabs-content').append('<div role="tabpanel" class="tab-pane" id="thread-' + th + '"><table class="table"><thead><tr><th>Campo</th><th>Tipo de dato</th><th>Tamaño máximo</th><th>Estado</th></tr></thead><tbody id="columns-' + th + '"></tbody></table></div>');
				
				$('#columns-' + th ).html('<td colspan="5" class="wait"><img src="loading.gif" height="32px"></td>');
				db.populate.columns(id, th, function(){
					load.threads.run(id, th);
				});
			}else{
				console.warn('No se puede agregar el Thread, debido a que ya está en ejecución.');
			}
			
		},
		run: function(id, th){
			// Empieza a correr el hilo.
			$('tr[data-id=' + id + ']').find('img').attr('src','loading.gif');
			// Lista de columnas
			$('#columns-' + th + ' tr').each(function(idx, e) {
                console.log(e);
            });
		}
	},
	tables: function(database){
		db.reset.tables(); // WebSQL
		$( "#tables" ).html('<td colspan="5" class="wait"><img src="loading.gif" height="32px"></td>');
		$.getJSON( 'load.php?type=table&database=' + database, function( data ) {
		  $.each( data, function( key, val ) {
			db.load.tables(key, val.TABLE_NAME, val.ENGINE, val.TABLE_COLLATION, val.TABLE_ROWS); // WebSQL
		  });
		}).done(function() {
			db.populate.tables('');
		});	
	},
	columns: function(id){
			db.instance.transaction(function (tx) {
				tx.executeSql('SELECT * FROM tables WHERE id = ?', [id], function (tx, results) {
					var database = $('#database').val();
					if(load.threads.process.indexOf(id) < 0){
						$.getJSON( 'load.php?type=column&database='+database+'&table='+results.rows.item(0).tabla, function(data) {
							$.each( data, function( key, val ) {
								db.load.columns(val.TABLE_NAME, id, val.COLUMN_NAME, val.COLUMN_TYPE, val.DATA_TYPE, val.MAX_LEN); // WebSQL
							});
						}).done(function() {
							load.threads.add(id);
						  }).fail(function() {
							console.info( "error" );
						  }).always(function() {
							console.info( "finished" );
						});
					}else{
						console.warn('No se puede agregar el Thread, debido a que ya está en ejecución.');
					}
				});
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