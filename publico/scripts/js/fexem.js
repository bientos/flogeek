var Fexem = {
	c_iframes: 0,
	// Convierte el código BCode al estilo ateneo
	// a HTML para ser mostrado.
	obtenerBCode: function(texto){
		return texto
			.replace(/\[negritas\](.*?)\[\/negritas\]/i, '<strong>$1</strong>')
			.replace(/\[rojo\](.*?)\[\/rojo\]/i, '<span style="color: #f00;">$1</span>');
	},
	// Función para enviar un formulario con todo y espera,
	// y sin tener que cambiar de página.
	enviarArchivo: function(f, funcion) {
		var form = document.forms[f];
		var iframe = document.createElement('iframe');
		var id = this.c_iframes++;
		var nombre = "_ateneo_enviarArchivo_iframe_"+id;
		// Preparar el iframe
		iframe.name = nombre;
		iframe.id = nombre;
		iframe.src = "about:blank";
		iframe.style.border="none";
		iframe.width = iframe.height = 0;
		iframe.style.display = "none";
		$(iframe).load( function(){
			var ifr = window.frames[nombre];
			if (typeof(funcion) == 'function') {
				funcion(ifr.document.body.innerHTML);
			}
			$('#'+nombre).remove();
		});
		// Preparar el formulario para desembocar
		// en el iframe especificado
		form.target = nombre;
		// Agregar el iframe al body
		document.body.appendChild(iframe);
		
		form.submit();
	},
	enviarArchivoJSON: function(f,funcion)
	{
		this.enviarArchivo(f,function(r){
			var json = eval('('+r+')');
			funcion( json );
		});
	},
	UI: {
		// Genera el HTML de un botón con el formato
		// a.boton.
		htmlBoton: function(texto,enlace,icono){
			var html = '';
			html += '<a href="'+enlace+'" class="fexem_ui_boton"';
			// Cerrar el inicio de la etiqueta
			html += '>';
			if( icono != null )
				html += '<img src="'+icono+'" />';
			html += texto;
			html += '</a>';
			
			return html;
		},
		// Imprime un botón estándar
		impBoton: function(texto,enlace,icono){
			document.write(this.htmlBoton(texto,enlace,icono));
		}
	},
	Formato: {
		TEXTO: 0,
		FECHA: 1,
		HORA:  2,
		meses: {
			1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4:'Abril',
			5: 'Mayo',  6: 'Junio',   7: 'Julio' ,8:'Agosto',
			9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'  
		},
		fechaTexto: function(fecha,tipo) {
			var f = fecha.split(/-|\s|\:/);
			// No funciona todavía
			if( false )
				return fecha;
			else
				return f[2] + ' de ' + this.meses[eval(f[1])] + ' de ' + f[0] +
					(f[3] && eval(f[3]) > 0 ? ' a las ' + f[3] + ':' + f[4] : ''); 
		}
	}
};
