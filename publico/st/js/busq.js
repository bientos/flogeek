var cols, col;
var i_col = 0;
var line_ft = 3;
var ft_width = 220;
var ft_margen = 105, ft_margen_y = 50;
var status = 'none';
var tiempo = 0;
var fecha_actual = '';
var win, doc, fofs;
var pila = [];
var search_cont, search, status_search = 'normal';

$(window).on('load',function() {
	search_cont = $('#super-search-container');
	win = $(this);
	doc = $(document);
	fofs = $('#fotos').offset();
	// Definir el número de columnas
	cols = new Array(line_ft);
	for(var i = 0; i < line_ft; i++)
		cols[i] = {'x' : i * ft_width, 'y': fofs.top};
	col = cols[0];
	loadFotos(fts);
	// Agregar la escuchar de cargas por llegar al final.
	win.on('scroll', function() {
		if(status_search === 'normal' && ($(window).scrollTop() > search_cont.offset().top)) {
			status_search = 'fixed';
			$('#super-search').css('top',0).css('width','100%').css('position','fixed')
				.css('box-shadow','0 1px 3px #c0c0c0').css('width','940px')
				.css('z-index', '10000');
		} else if(status_search === 'fixed' && ($(window).scrollTop() <= search_cont.offset().top) ) {
			status_search = 'normal';
			$('#super-search').css('top',0).css('width','auto').css('position','static')
				.css('box-shadow','none');
		}
	}).scroll();
});

function loadFotos(fts) {
	$.each(fts, function(k,v) {
		var ft = crearFoto(v).css('width',ft_width+'px');
		for(var i = 0; i < line_ft; i++) {
			if(cols[i].y <= col.y) {
				i_col = i;
				col = cols[i];
			}
		}
		ft.css('margin-left', (col.x+ft_margen*i_col)+'px').css('top',(col.y+ft_margen_y)+'px');
		$('#fotos').append(ft);
		col.y += parseInt(ft.outerHeight()) + ft_margen_y;
		if(tiempo == 0 || parseInt(v.tiempo) < tiempo) {
			tiempo = parseInt(v.tiempo);
			fecha_actual = v.fecha;
		}
	});
	$('#fotos').css('width', ft_width*line_ft+line_ft*ft_margen).css('height',(col.y)+'px');
}

function crearFoto(f) {
	var tit = $('<div>').addClass('foto-titulo').text(f.titulo);
	var prop = f.alto / f.ancho;
	var us = $('<div>').addClass('foto-usuario')
		.append($('<a>').attr('href','http://flogeek.com/'+f.usuario).text(f.usuario));
	var im = $('<img>').attr('src','img/fotos/'+f.lote+'/'+f.id+'_s.jpg')
		.css('width',ft_width+'px').css('height',Math.round(ft_width*prop)+'px');
	/*im[0].onload = function() {
		//alert('correcto');
		$(this).animate({'opacity':1});
		this.onload = null;
	};*/
	var lk = $('<a>').addClass('foto-foto').attr('href','http://flogeek.com/'+f.usuario+'/'+f.id).append(im);
	var ff = $('<div>').append([lk,tit,us]).addClass('foto');
	return ff;
}
