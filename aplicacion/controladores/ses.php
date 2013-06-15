<?php
class Ses_Controlador extends Fexem_Controlador
{
	function index_accion($error = null) {
		$this->vista->llenar('sesion.html',array(
			'mensaje'	=> $error['mensaje']
		));
	}
	
	function iniciar_accion() {
		$db = new Fexem_Database();
		// Obtener la información del usuario
		$usuario = $db->crearConsulta()
			-> seleccionar('usuario_usuario usuario', 'usuario_id id',
				'usuario_estilo tema', 'usuario_email mail',
				'usuario_lote lote', 'usuario_tema_ajeno tema',
				'usuario_foto_ultima_id ultima_foto_id' )
			-> de('usuarios')
			-> donde('usuario_usuario = ?', $this->post->usuario)
			-> donde('usuario_password = MD5(?)', $this->post->password)
			-> limite(1)
			-> leerModelo();
		
		if( $usuario ) {
			// Guardar los datos del usuario en usuario
			$_SESSION['usuario'] = $usuario;
			
			// Ir a la página indicada en caso de haberse dado;
			// de lo contrario se enviará a la página principal.
			$this->json->status = 'OK';
			$this->json->usuario = array(
				'usuario' => $this->sesion->usuario->usuario );
		} else {
			// Mostrar le formulario de nuevo
			$this->json->status = 'NO_VALIDO';
		}
	}
	
	function terminar_accion() {
		// Eliminar solo la variable del usuario
		$this->sesion->usuario = null;

		// Ir a la página principal
		$this->irA('');
	}
}