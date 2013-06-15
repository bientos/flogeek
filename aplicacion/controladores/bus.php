<?php 
class Bus_Controlador extends Fexem_Controlador
{
	public $fotos;
	
	public $FOTOS_POR_PAGINA = 20;
	public $hayMasFotos = false;
	public $numeroPagina = 1;
	public $urlSiguientePagina = '';
	public $urlAnteriorPagina = '';
	
	function index_accion() {
		// Tomar el parámetro de búsqueda
		// y el de orden de registros
		$b = trim($this->get->b);
		$o = $this->get->o;
		$u = $this->get->u;
		
		// Inicializar la consulta principal
		$db = new Fexem_Database();
		$c = $db->seleccionar('fotos f');

		// Definir el órden de la búsqueda
		switch( strtolower($o) ) {
			case 'fecha': 	  $c->ordenar('f.foto_fecha desc'); break;
			case 'comentado': $c->ordenar('f.foto_comentarios desc'); break;
			case 'visto':	  $c->ordenar('f.foto_visitas DESC'); break;
			default:		  $c->ordenar('rank DESC');
		}
		
		// Preparar $b para expresiones regulares
		$bexp = preg_quote($b);
		
		// Llenar la consulta
		$c->seleccionar('f.foto_id id','f.foto_titulo titulo',
				'u.usuario_usuario usuario', 'f.foto_lote lote',
				'(f.foto_voto_no * 5 + f.foto_voto_si * 50 + f.foto_comentarios) rank',
				'f.foto_fecha fecha',/*'f.foto_descripcion descripcion',*/
				'f.foto_comentarios contador_comentarios',
				'f.foto_visitas contador_vistas','f.alto','f.ancho'
			)
		  ->limite(30)
		  ->ligarI('usuarios u','f.usuario_id','u.usuario_id');
		  
		// Agregar el criterio de búsqueda
		if(  preg_match('/^\@([a-z0-9_-]+)$/i',$b,$coin) === 1 ) {
			$us = $db->crearConsulta()->seleccionar('usuario_id')
				->de('usuarios')
				->si('usuario_usuario = ?', $coin[1])
				->limite(1)->leer();
			if($us) {
				$c->si('f.usuario_id = ?', $us['usuario_id']);
			} else {
				$c->si('0 = 0');
			}
		} elseif( preg_match('/^\#([a-z0-9_-]+)$/i',$b,$coin) === 1 ) {
			$c->si("(f.foto_tags LIKE '{$coin[1]}' ".
				"or f.foto_tags LIKE '%,{$coin[1]},%' ".
				"or f.foto_tags LIKE '{$coin[1]},%' ".
				"or f.foto_tags LIKE '%,{$coin[1]}' )");
		}elseif( $b )
			$c->si('f.foto_titulo REGEXP ?',"{$bexp}")
			   ->o('f.foto_tags REGEXP ?', "{$bexp}" )
			   ->o('f.foto_descripcion REGEXP ?', "{$bexp}" );
		  
		// Guardar la lista
		$this->fotos = $c->leerTodos();
		
		// Mostrar la lista
		$this->vista->llenar('busqueda/resultados.html');
	}

	function queda() {
		
	}
}