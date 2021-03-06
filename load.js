// JavaScript Document
var load = {
	threads:{
		max:4,
		counter:0,
		hash:0,
		process:Array(),
		memory:[],
		count: function(){ return load.threads.process.length },
		init: function(){
			console.info('Cargador de hilos.');
			var id = $('#tables tr[data-status=process]').first().attr('data-id');
			if(id != undefined){
				load.columns(id);
				setTimeout(function(){ load.threads.init(); }, 1000);
			}
		},
		add: function(id, database, table){
			if(load.threads.process.indexOf(id) < 0 && load.threads.counter < load.threads.max){
				load.threads.memory[parseInt(id)] = {
					'database': database,
					'table': table,
					'status': 'info',
					'total': 0,
					'current': 0
				};
				load.threads.process.push(id);
				var th = load.threads.hash = parseInt(load.threads.hash) + 1;
				load.threads.counter = parseInt(load.threads.counter) + 1;
				console.info("Agregando thread: " + th + " para tabla " + id);
				$('#tabs-title').append('<li role="presentation" id="li-thread-'+th+'"><a href="#thread-' + th + '" aria-controls="thread-' + th + '" role="tab" data-toggle="tab">'+table+' [' + th + ']</a></li>');
				
				$('#tabs-content').append('<div role="tabpanel" class="tab-pane" id="thread-' + th + '"><h2>'+table+'</h2><table class="table"><thead><tr><th>Campo</th><th>Tipo de dato</th><th>Tamaño máximo</th><th>Coincidencias</th><th>Estado</th></tr></thead><tbody id="columns-' + th + '"></tbody></table></div>');
				
				$('#columns-' + th ).html('<td colspan="5" class="wait"><img src="loading.gif" height="32px"></td>');
				db.populate.columns(id, th, function(){
					console.log('Running');
					load.threads.run(id, th);
					load.threads.memory[parseInt(id)].total = $('#columns-' + th + ' tr[data-status=process]').length;
				});
			}else{
				console.warn('No se puede agregar el Thread, debido a que ya está en ejecución o está al límite de número de threads corriendo.');
			}
			
		},
		run: function(id, th){
			// Empieza a correr el hilo.
			$('#tables tr[data-id=' + id + ']').find('img').attr('src','loading.gif');
			$('#tables tr[data-id=' + id + ']').attr('data-status','loading');
			// Lista de columnas
			var procesos = $('#columns-' + th + ' tr[data-status=process]');
			var row = procesos.first();
			var memory = load.threads.memory[parseInt(id)];
			if(procesos.length > 0){
				row.find('img').attr('src','loading.gif');
				$.post( 'load.php?type=row&database=' +memory.database+'&table='+memory.table+'&row='+row.find('td').first().text()+'&val='+$('#buscar').val(), function(data) {
				  // row.find('img').attr('src','ok.png');
				 // console.log(data);
				})
				  .done(function(data) {
					 var coincidencias = parseInt(data.coincidencias);
					 memory.current++;
					 var progress = Math.trunc((memory.current*100)/memory.total);
					 $('#tables tr[data-id=' + id + ']').find('td.text-muted').text(progress + '\t%');
					 if(coincidencias > 0){
						 row.attr('data-status', 'ok');
						 row.find('img').attr('src','ok.png');
						 memory.status = 'ok';
						 db.load.founds(memory.table, row.find('td').first().text(), row.find('td[data=tipo]').text(), parseInt(row.find('td[data=size]').text()), coincidencias);
						 db.populate.founds();
					 }else{
						 row.attr('data-status', 'info');
						 row.find('img').attr('src','info.png');
						 //console.warn(load.threads.counter);
					 }
					 row.find('td.text-success').text(coincidencias);
					 
					load.threads.run(id, th);
				  })
				  .fail(function() {
					console.info( "error" );
					row.find('img').attr('src','error.png');
				  })
				  .always(function() {
					console.info( "finished" );
				});	
			}else{
				$('#tables tr[data-id=' + id + ']').find('img').attr('src',memory.status+'.png');
				$('#li-thread-' + th).remove();
				$('#thread-' + th).remove();
				load.threads.counter = parseInt(load.threads.counter) - 1;
			}
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
		if(load.threads.counter < load.threads.max){
			db.instance.transaction(function (tx) {
				tx.executeSql('SELECT * FROM tables WHERE id = ?', [id], function (tx, results) {
					var database = $('#database').val();
					if(load.threads.process.indexOf(id) < 0){
						$.getJSON( 'load.php?type=column&database=' + database + '&table=' + results.rows.item(0).tabla, function(data) {
							$.each( data, function( key, val ) {
								db.load.columns(val.TABLE_NAME, id, val.COLUMN_NAME, val.COLUMN_TYPE, val.DATA_TYPE, val.MAX_LEN); // WebSQL
							});
						}).done(function() {
							load.threads.add(id, database, results.rows.item(0).tabla);
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
		}
	}
};