jQuery.mensaje = function(p){
	var e = document.createElement('div');
	var tipo = p.tipo ? p.tipo.toLowerCase() : '';
	$('#__panelMensajes')
	.append(
		$(e).hide().attr('class',
			tipo == 'error' ? 'mensaje_error' : (
				tipo == 'exito' ? 'mensaje_exito' : 'mensaje_info' )
		).html(p.texto)
	);
	
	// Hacer la aparición y desaparición
	$(e).slideDown('fast')
		.delay(2000)
		.slideUp('fast');
};