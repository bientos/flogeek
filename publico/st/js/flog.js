var imagen;
var canvas;
var filtros = [];
var el_filtro = null;
var cx;
/* {literal} */
var msgs = {
	1: 'No has iniciado sesión.',
	2: 'No dejes comentarios vacíos.',
	3: 'Ya eliminaron esta foto.',
	777: 'Gracias por tu comentario.'
};
function add_nota() {
	//crear_nota();
	$('#la-foto').append(crear_nota());
}
function crear_nota(p) {
	var txt_ed = $('<textarea>').attr('type', 'text');
	var txt  = $('<div>').addClass('nota-txt').append(txt_ed);
	var nota = $('<div>').addClass('nota');
	var btg  = $('<input>').attr('type','button').val('Guardar');
	if(p._nuevo) {
		btg.click(function(){
			var nota_ofs = nota.offset();
			var foto_ofs = $('#la-foto').offset();
			var x = nota_ofs.left - foto_ofs.left,
				y = nota_ofs.top - foto_ofs.top;
			var str_post = 'fid='+encodeURIComponent(id_foto)
				+'&texto='+encodeURIComponent(txt_ed.val());
			str_post += '&x='+x+'&y='+y;
			// id_foto sale de flog.html
			$.post('flo/anotar',str_post, function(r) {
				if(r.status === 'OK') {
					nota.remove();
				}
			},'json');
		});
	} else {
		
	}

	var lafo = $('#la-foto').offset();
	nota.append(txt, btg);
	nota.css('position','absolute').css('left',lafo.left+'px').css('top',lafo.top+'px');
	nota.draggable({containment: 'parent'});
	//nota.resizable();
	return nota;
}
function descomentar(id) {
	$.getJSON('flo/descomentar?id='+id, function(r){
		if(r.res == 777){
			$('#comentario_'+id).slideUp('fast',function(){
				$(this).remove();
			});
		}
	});
}
function comentar() {
	var strfrm = $('#f_comentario').serialize();
	$('#f_comentario textarea').val('');
	$.post('flo/comentar',
		strfrm, function(r){
			if (r.res == 777) {
				var html = $('#comentario_plantilla').html()
					.replace(/\[\%id\]/g,r.comentario.id)
					.replace(/\[\%usuario\]/g, usuario)
					.replace(/\[\%usuario_lote]/g, usuario_lote)
					.replace(/\[\%usuario_id]/g, id_usuario)
					.replace(/\[\%comentario_fecha]/g, r.comentario.fecha.toString())
					.replace(/\[\%comentario]/g, r.comentario.texto
						.replace(/</,'&lt;')
						.replace(/>/,'&gt;')
					);
				
				// Agregar a la lista de comentarios
				$('#comentarios').append(html);
				$('#comentario_'+r.comentario.id).slideDown('fast');
			} else {
				$('#comentario-msg').html(msgs[r.res])
					.hide()
					.attr('class', 'mensaje_error')
					.fadeIn('fast');
			}
		},
	'json');
}
function eliminar(id){
	if( confirm('¿Seguro que quieres eliminar la foto?') ){
		$.post('../../pub/despublicar','id='+id, function(r) {
			if(r.status === 'OK') {
				location.href = 'http://flogeek.com/'+usuario;
			}
		},'json');
	}
}
function editar() {
	var id = $('#idFoto').val();
	$('#muestra').hide();
	$.post('flo/foto','id='+id,function(r){
		$('#campoTitulo').val( r.foto.titulo );
		$('#campoDescripcion').val( r.foto.descripcion );
		$('#edicion-cont').show();	
	},'json');
}
function cancelarEditar(){
	$('#edicion-cont').hide();
	$('#muestra').show();
}
function guardar(){
	$.post('flo/guardar',$('#formEdicion').serialize(),
		function(r){
			if (r.estado == 777) {
				// Cambiar el texto por el nuevo
				$('#titulo').text( r.foto.titulo );
				$('#descripcion').html( r.foto.descripcion
					.replace('<','&lt;')
					.replace('>','&gt;')
					.replace(/\n/g,'<br />') );
				// Ocultar y mostrar
				cancelarEditar();
				// Mostrar el mensaje
				$.mensaje({
					texto: 'Se guardaron los cambios correctamente.',
					tipo: 'exito'
				});
			} else {
				$.mensaje({
					texto: 'Error: ' + r.estado,
					tipo: 'error'
				});
			}
		}
	,'json');
}

function agregarEtiqueta(nombre){
	var et = document.createElement('div');
	var lnk = document.createElement('a');
	var lne = document.createElement('a'); 
	$(et).attr('class','etiqueta')
		.css('float','left')
		.attr('nombre',nombre)
		.append( $(lnk).attr('href','#').text(nombre));
	if( editable )
		$(et).append( $(lne).text('x')
			.attr('href','javascript:;')
			.attr('class','eliminado')
			.click(function(){eliminarEtiqueta(nombre)}) );
	$('#etiquetas').append( et );
}

function eliminarEtiqueta(nombre){
	$.post('flo/eliminar_etiqueta','nombre='+nombre+'&id_foto='+id_foto,
		function(r){
			if (r.estado == 777) {
				$('div[nombre='+nombre+']').fadeOut('fast');
			}
		}
	,'json');
}

function crearEtiqueta(){
	var nombre = $('#etiquetaNombre').val();
	$.post('flo/agregar_etiqueta',$('#formNuevaEtiqueta').serialize(),
		function(r){
			if (r.estado == 777) {
				agregarEtiqueta(nombre);
				$('#etiquetaNombre').val('');
			}
		}
	,'json');
}

function enfocarSesion() {
	mostrarLogin();
	$.scrollTo('#cuadroLogin');
}
function filtrar() {
	canvas = document.getElementById('foto-edicion');
	var tras = document.getElementById('trasfondo');
	var edicion = document.getElementById('edicion');
	var foto = document.getElementById('la-foto');
	
	imagen = new Image();
	imagen.src = foto_url;
	
	cx = canvas.getContext('2d');
	$(tras).fadeIn(250);
	$(edicion).fadeIn(250);
	canvas.width = foto.offsetWidth;
	canvas.height = foto.offsetHeight;
	canvas.style.width = foto.offsetWidth+'px';
	canvas.style.height = foto.offsetHeight+'px';
	//edicion.appendChild(canvas);
	
	// imprimir la imagen
	cx.drawImage(imagen,0,0,foto.offsetWidth, foto.offsetHeight);
}
function aplicar_ef() {
	if( el_filtro ) {
		$.post('fil/aplicar', 'id='+id_foto+'&filtro='+el_filtro, function(r){
			if(r.status=='OK'){
				location.reload(true);
			}
		},'json');
	}
}
function cancelar_ef() {
	var tras = document.getElementById('trasfondo');
	var edicion = document.getElementById('edicion');
	$(tras).fadeOut(250);
	$(edicion).fadeOut(250);
}
function filtro_byn() {
	cx.drawImage(imagen, 0, 0);
	var dt = cx.getImageData(0,0,canvas.width,canvas.height);
	var px = dt.data;
	var sz = dt.width * dt.height;
	var co;
	for(var i = 0; i < sz; i++) {
		co = (px[i*4]+px[i*4+1]+px[i*4+2])/3;
		px[i*4] = px[i*4+1] = px[i*4+2] = co;
	}
	cx.clearRect(0,0,dt.width, dt.height);
	cx.putImageData(dt,0,0);
	el_filtro = 'gsclp';
}
function filtro_sepia() {
	cx.drawImage(imagen, 0, 0);
	var dt = cx.getImageData(0,0,canvas.width,canvas.height);
	var px = dt.data;
	var sz = dt.width * dt.height;
	var co;
	for(var i = 0; i < sz; i++) {
		co = (px[i*4]+px[i*4+1]+px[i*4+2])/3;
		px[i*4] = px[i*4+1] = px[i*4+2] = co;
		// agregar el amarillo
		//px[i*4+2] = 150; //b
		px[i*4] *= 1.5; //r
		px[i*4+1] *= 1.25; //g
	}
	cx.clearRect(0,0,dt.width, dt.height);
	cx.putImageData(dt,0,0);
	el_filtro = 'sepia1';
}
function filtro_ashuta() {
	cx.drawImage(imagen, 0, 0);
	var dt = cx.getImageData(0,0,canvas.width,canvas.height);
	var px = dt.data;
	var sz = dt.width * dt.height;
	var co;
	for(var i = 0; i < sz; i++) {
		co = (px[i*4]+px[i*4+1]+px[i*4+2])/3;
		//px[i*4] = px[i*4+1] = px[i*4+2] = co;
		// agregar el amarillo
		px[i*4+2] = 130; //b
		px[i*4] *= 1.15; //r
		px[i*4+1] *= 1.15; //g
	}
	cx.clearRect(0,0,dt.width, dt.height);
	cx.putImageData(dt,0,0);
	el_filtro = 'ashuta1';
}