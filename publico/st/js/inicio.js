var cols, col;
var i_col = 0;
var ft_width = 220;
var ft_margen = 16;
var status = 'none';
var tiempo = 0;
var fecha_actual = '';
var win, doc, fofs;
var pila = [];
var state = {ncols: null};

$(window).on('resize', function(evt) {
	
});

$(window).on('load',function() {
	if (state.ncols === null) {
		state.ncols = Math.floor(window.innerWidth / (ft_width + ft_margen));
	}
	win = $(this);
	doc = $(document);
	fofs = $('#fotitos').offset();
	// Definir el número de columnas
	cols = new Array(state.ncols);
	for(var i = 0; i < state.ncols; i++)
		cols[i] = {'x' : i * ft_width, 'y': fofs.top};
	col = cols[0];
	loadFotitos(fts);
	// Agregar la escuchar de cargas por llegar al final.
	win.on('scroll', function() {
		if(status != 'downloading' && (win.innerHeight()+win.scrollTop()) > (doc.height()-10)) {
			status = 'downloading';
			nextFotitos();
		}
	});
});

function nextFotitos() {
	$.post('fot/get_muchas','fecha='+fecha_actual, function(r) {
		//pila.push(function(){loadFotitos(r.pics)});
		loadFotitos(r.pics);
		status = 'none';
	},'json');
}

function loadFotitos(fts) {
	$.each(fts, function(k,v) {
		var ft = $(crearFotito(v)).css('width',ft_width+'px');
		for(var i = 0; i < state.ncols; i++) {
			if(cols[i].y <= col.y) {
				i_col = i;
				col = cols[i];
			}
		}
		ft.css('margin-left', (col.x+ft_margen*i_col)+'px').css('top',(col.y+ft_margen)+'px');
		$('#fotitos').append(ft);
		col.y += parseInt(ft.outerHeight()) + ft_margen;
		if(tiempo == 0 || parseInt(v.tiempo) < tiempo) {
			tiempo = parseInt(v.tiempo);
			fecha_actual = v.fecha;
		}
	});
	$('#fotitos').css('width', ft_width*state.ncols+state.ncols*ft_margen).css('height',(col.y)+'px');
}

function crearFotito(f) {
	var prop = f.alto / f.ancho;
	var main = document.createElement('div');
	var photoLink = document.createElement('a');
	var img = document.createElement('img');
	var userLink = document.createElement('a');
	var user = document.createElement('div');

	userLink.href = 'https://flogeek.com/'+f.usuario+'/'+f.id;
	userLink.innerHTML = f.usuario;

	user.className = 'fotito-usuario';
	user.appendChild(userLink);

	img.src = 'img/fotos/' + f.lote + '/' + f.id + '_s.jpg';
	img.style.width = ft_width + 'px';
	img.style.height = Math.round(ft_width*prop) + 'px';
	img.style.opacity = 0.01;
	img.onload = function() {
		$(this).animate({'opacity': 1});
		this.onload = null;
	}

	photoLink.href = 'http://flogeek.com/' + f.usuario + '/' + f.id;
	photoLink.className = 'foto';
	photoLink.appendChild(img);

	main.className = 'fotito';
	main.appendChild(photoLink);
	main.appendChild(user);

	return main;

	/*
	var prop = f.alto / f.ancho;
	var us = $('<div>').addClass('fotito-usuario')
		.append($('<a>').attr('href','http://flogeek.com/'+f.usuario).text(f.usuario));
	var im = $('<img>').attr('src','img/fotos/'+f.lote+'/'+f.id+'_s.jpg')
		.css('width',ft_width+'px').css('height',Math.round(ft_width*prop)+'px')
		.css('opacity','.01');
	im[0].onload = function() {
		//alert('correcto');
		$(this).animate({'opacity':1});
		this.onload = null;
	};
	var lk = $('<a>').addClass('foto').attr('href','http://flogeek.com/'+f.usuario+'/'+f.id).append(im);
	var f = $('<div>').append([lk,us]).addClass('fotito');
	return f;*/
}
