<?php

class Index_Controlador extends Fexem_Controlador
{
	function index_accion()
	{
		$db = new Fexem_Database();
	
		// Obtener la lista de las fotos/videos más nuevas
		$nuevas = $db->crearConsulta()
			-> seleccionar('f.foto_id id', 'u.usuario_usuario usuario',
				'f.foto_id id','u.usuario_usuario usuario', 'f.foto_dia id_dia',
				'f.ancho','f.alto','UNIX_TIMESTAMP(f.foto_fecha) tiempo',
				'f.foto_fecha fecha','f.foto_lote lote')
			-> de('fotos f')
			-> ligarI('usuarios u', 'f.usuario_id', 'u.usuario_id')
			-> ordenar('f.foto_id desc')
			-> limite(16)
			-> leerTodos();

		// Enviar las listas a la vista
		$this->vista->llenar('index.html',array(
			'nuevas_fotos'	=> $nuevas
		));
	}
	
	function __call($nombre,$valor)
	{
		echo 'si';
		$db = new Fexem_Database();
		
		// Validar si es un nombre de usuario
		if( preg_match('@^[a-z0-9_.-]{4,16}$@i', $nombre) )
		{
			// Obtener la información del usuario y su primera foto
			$usuario = $db->crearConsulta()
				-> de('usuarios u')
				-> donde('u.usuario_usuario = ?', $nombre)
				-> ligarL('fotos f','u.usuario_id','f.usuario_id')
				-> limite(1)
				-> leerModelo();
			
			if( $usuario )
			{
				// Obtener la información de la foto correspondiente
				
				// Obtener la lista de fotos más nuevas aparte de la actual
				/*$fotos = $db -> crearConsulta()
					-> de('fotos f')
					-> donde('f.usuario_id = ?', $usuario->usuario_id)
					-> donde('f.foto_id != ?', )
					-> ordenar('f.foto_fecha desc')*/
				$this->vista->llenar('flog.html',array(
					'usuario' => $usuario
				));
			}
		}
		else
		{
			$this->vista->imprimir('No existe el flog solicitado.');
		}
	}
	
	function mant()
	{
		echo 'Flogeek esta en reparacion';
	}
}