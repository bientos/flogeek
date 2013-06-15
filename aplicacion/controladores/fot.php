<?php

class Fot_Controlador extends Fexem_Controlador
{
	function get_muchas_accion() {
		$db = new Fexem_Database();
		$p =& $this->post;
		if($p->fecha > 0) {
			// Consultar la lista de fotos por tiempo mínimo
			$this->json->pics = $db->crearConsulta()
				-> seleccionar('f.foto_id id', 'u.usuario_usuario usuario',
					'f.foto_id id','u.usuario_usuario usuario', 'f.foto_dia id_dia',
					'f.ancho','f.alto','UNIX_TIMESTAMP(f.foto_fecha) tiempo',
					'f.foto_fecha fecha','f.foto_lote lote')
				-> de('fotos f')
				-> donde('f.foto_fecha < ?', $p->fecha)
				-> ligarI('usuarios u', 'f.usuario_id', 'u.usuario_id')
				-> ordenar('f.foto_id desc')
				-> limite(30)
				-> leerTodos();
			$st = 'OK';
		} else {
			$st = 'NO_TIEMPO';
		}
		$this->json->status = $st;
	}
}
