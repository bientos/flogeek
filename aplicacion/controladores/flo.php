<?php
class Flo_Controlador extends Fexem_Controlador
{
	public $foto;
	public $usuario;
	public $comentarios;
	public $comentario;
	public $fotos;
	public $etiquetas;
	public $otrasFotos;
	public $amigos;
	public $notas;
	
	private static $_db;
	
	function index_accion() {
		// El usuario debe existir
		if( $this->getUsuario($this->parametros[1]) ) {
			// Crear la consulta de la foto
			$cfoto = self::_db()->crearConsulta();
			$cfoto -> de('fotos')->limite(1)
				-> donde('usuario_id = ?', $this -> usuario->usuario_id);
			// Obtener la información de la foto
			if( isset($this->parametros[2]) )
				$cfoto->donde('foto_id = ?', $this->parametros[2]);
			else
				$cfoto->ordenar('foto_fecha desc');
				
			// Realizar la consulta
			if( $this->foto = $cfoto->leerModelo() ) {
				if( trim($this->foto->foto_titulo) )
					$tituloHTML = $this->foto->foto_titulo;
				if( trim($this->foto->foto_descripcion) )
					$descripcionHTML = $this->foto->foto_descripcion;
				
				// Obtener la lista de etiquetas
				$this->etiquetas = preg_split('/\s*\,\s*/',$this->foto->foto_tags);
				
				// Ponerle la fecha en formato fecha
				$this->foto->fecha = new Fexem_Fecha($this->foto->foto_fecha);
				
				// leer las notas
				$this->notas = $this->db->crearConsulta()
					->seleccionar('nota','id','x','y')->de('fotos_notas')
					->si('id_foto = ?', $this->foto->foto_id)
					->leerTodos();
				
				// Obtener lista de comentarios
				$this -> comentarios = self::_db()->crearConsulta()
					-> seleccionar('c.*','u.usuario_usuario usuario',
						'u.usuario_foto_ultima_id id_foto','u.usuario_lote')
					-> de('comentarios c')
					-> donde('c.foto_id = ?', $this->foto->foto_id)
					-> ligarI('usuarios u', 'c.usuario_id', 'u.usuario_id')
					-> leerTodos();
					
				// Obtener la lista de fotos más nueva excepto la actual
				$this -> fotos = self::_db()->crearConsulta()
					-> de('fotos f')
					-> donde('f.usuario_id = ?', $this -> usuario -> usuario_id)
					-> limite(6)
					-> donde('f.foto_id != ?', $this -> foto -> foto_id)
					-> ligarI('usuarios u', 'f.usuario_id','u.usuario_id')
					-> ordenar('f.foto_fecha desc')
					-> leerTodos();
					
				if( !isset($_SESSION['visitas'][$this->foto->foto_id]) ) {
					$_SESSION['visitas'][intval($this->foto->foto_id)] = true;
					// Incrementar el contador de visitas de la foto
					self::_db()->crearConsulta()
						->actualizar('fotos')
						->poner('foto_visitas = foto_visitas + 1')
						->donde('foto_id = ?', $this->foto->foto_id)
						->ejecutar();
				}
					
				if( $this->foto->foto_video_tipo == 0 ) {
					$this->foto->video_url = 'http://youtube.com/v/'.
						$this->foto->foto_video_id;
				}
				
				// Llenar la vista
				$this -> vista -> llenar ('flog.html', array(
					'Titulo' => isset($tituloHTML) ? $tituloHTML : null,
					'Descripcion' => isset($descripcionHTML) ? $descripcionHTML : null,
					'imagenOG' => 'http://i1.flogeek.com/fotos/' . $this->foto->foto_lote . '/' . $this->foto->foto_id . '_s.jpg'
				));
			} else {
				$this->vista->imprimir('<div style="padding:50px; font-size: 30px; text-align: center;">El usuario no ha agregado su primera foto.'
					.($this->sesion->usuario && $this->usuario->id == $this->sesion->usuario->id
						? '<br><a href="pub">Agregar Una</a>' : '') . '</div>');
			}	
		} else {
			$this -> vista -> llenar('no_encontrado.html');
		} 	
	}
	
	/**
	 * Agrega un comentario para una foto.
	 * Estos son los posibles resultados:
	 * 
	 * 1	Debes registrarte para poder comentar.
	 * 2	Debes poner un comentario.
	 * 3	No existe la foto que deseas comentar.
	 * 777	Comentario agregado correctamente.
	 */
	function comentar_accion() {
		if( !$this->sesion->usuario )
			$this->json->res = 1;
		elseif( !$this->post->comentario )
			$this->json->res = 2;
		elseif( !$this->getFoto($this->post->id_foto) )
			$this->json->res = 3;
		else {
			// Obtener la fecha de inseción
			$fecha = new Fexem_Fecha();
			
			// Agregar a la base de datos
			$id_comentario = self::_db()->insertar('comentarios',array(
				'usuario_id'			=> $this->sesion->usuario->id,
				'foto_id'				=> $this->post->id_foto,
				'comentario_comentario'	=> $this->post->comentario,
				'comentario_fecha'		=> $fecha
			));
			
			// Resultado correcto, comentario agregado correctamente
			$this->json->res = 777;
			$this->json->comentario = array(
				'id'			=> $id_comentario,
				'texto'			=> $this->post->comentario,
				'fecha'			=> $fecha,
				'fecha_partes'	=> array(
					'dia'	=> $fecha->getDia(),
					'mes'	=> $fecha->getMes(),
					'ano'	=> $fecha->getAno(),
					'hora'	=> $fecha->getHora(),
					'minuto'=> $fecha->getMinuto()
				)
			);
			
			$this->usuario = self::_db()->crearConsulta()
				->seleccionar('usuario.*')->limite(1)
				->de('usuarios usuario')
				->donde('usuario.usuario_id = ?', $this->foto->usuario_id)
				->leerModelo();
			
			// Enviamos un correo al dueño de la foto
			mail($this->usuario->usuario_email,
				"Comentaron tu foto",
				"<strong>{$this->sesion->usuario->usuario_usuario}</strong> ".
				"comentó tu publicación <strong>{$this->foto->foto_titulo}</strong>:<br><br>".
				str_replace(array('<','>'),array('&lt;','&gt;'), $this->post->comentario),
				"From: Flogeek<notif@flogeek.com>\r\n".
				"Content-Type: text/html; charset=utf-8\r\n".
				"MIME-Version: 1.0\r\n",
				"-odb");
			
			// Incrementar el contador de comentarios de la foto
			mysql_query("
				UPDATE fotos
				SET foto_comentarios = foto_comentarios + 1
				WHERE foto_id = {$this->post->id_foto}
			");
		
		}
	}
	
	function eliminar_etiqueta_accion() {
		if( !$this->sesion->usuario ) {
			// No hay sesión
			$this->json->estado = 1;
		} else if( $this->getFoto($this->post->getInt('id_foto'))
		&& $this->foto->usuario_id == $this->sesion->usuario->id ) {
			if( $k = array_search($this->post->nombre, $this->etiquetas) ) {
				unset($this->etiquetas[$k]);
				// Actualizar la base de datos
				self::_db()->crearConsulta()
					->actualizar('fotos')->limite(1)
					->donde('foto_id = ?', $this->foto->foto_id)
					->poner('foto_tags', implode(',',$this->etiquetas))
					->ejecutar();
			}
			$this->json->estado = 777;
		} else {
			// No tiene permiso para editar
			$this->json->estado = 2;
		}
	}
	
	/**
	 * Agrega una nota a una foto, dada la posición X y Y.
	 */
	function anotar_accion() {
		$p =& $this->post;
		// Leer la información de la foto que se desea anotar.
		$f = $this->db->un('fotos')
			->si('foto_id = ?', $p->fid)->leerUno();
		if($f) {
			$u = $this->db->un('usuarios')
				->si('usuario_id = ?', $this->sesion->usuario->id)
				->leerUno();
			if($u['usuario_id'] == $f['usuario_id']) {
				// agregar el comentario a la base de datos
				if(trim($p->texto)) {
					$nota_id = md5(uniqid(mt_rand(), true));
					$this->db->insertar('fotos_notas', array(
						'id'      => $nota_id,
						'id_foto' => $f['foto_id'],
						'x'       => $p->x,
						'y'       => $p->y,
						'ancho'   => $p->ancho,
						'alto'    => $p->alto,
						'nota'    => $p->texto
					));
					$s = 'OK';
				} else {
					$s = 'NO_TEXTO';
				}
			} else {
				$s = 'NO_PERMISO';
			}
		} else {
			$s = 'NO_EXISTE';
		}
		$this->json->status = $s;
	}
	
	function agregar_etiqueta_accion() {
		if( !$this->sesion->usuario ) {
			// No hay sesión
			$this->json->estado = 1;
		}
		else if( $this->getFoto($this->post->getInt('id_foto'))
		&& $this->foto->usuario_id == $this->sesion->usuario->id ) {
			if( trim($this->post->nombre) ) {
				$this->foto->foto_tags .= ($this->foto->foto_tags
					? ',' : '') . trim($this->post->nombre);

				// Cambiar en la base de datos
				self::_db()->crearConsulta()
					->actualizar('fotos')->limite(1)
					->donde('foto_id = ?', $this->foto->foto_id)
					->poner('foto_tags', $this->foto->foto_tags)
					->ejecutar();
					
				$this->json->estado = 777;
			} else {
				// No se pueden meter etiquetas vacías
				$this->json->estado = 3;
			}
		} else {
			// No tiene permiso para editar
			$this->json->estado = 2;
		}
	}
	
	function descomentar_accion() {
		if( $this->getComentario($this->get->id) && $this->sesion->usuario
		&&  $this->comentario->usuario_id == $this->sesion->usuario->id  ) {
			// Eliminarlo
			self::_db()->crearConsulta()
				->eliminar('comentarios')
				->donde('comentario_id=?',$this->get->id)
				->ejecutar();
			$this->json->res = 777;
		} else {
			$this->json->res = 1;
		}
	}
	
	function fotos_accion() {
		// Obtener el usuario
		if( $this->getUsuario($this->parametros[1]) ) {
			// Obtener la lista de fotos
			$this->fotos = self::_db()->seleccionar('fotos')
				->donde('usuario_id=?',$this->usuario->usuario_id)
				->ordenar('foto_fecha desc')->limite(30)
				->leerModelos();
			
			// Mostrar la lista de las fotos
			$this->vista->llenar('flog/fotos.html');
		} else {
			$this->vista->imprimir("No existe el usuario.");
		}
	}
	
	function resolver_accion() {
		if( $this->getFoto( $this->get->id ) ) {
			$this->irA( strtolower($this->foto->usuario) . '/' . $this->foto->foto_id );
		} else {
			$this->irA('');
		}
	}
	
	function foto_accion() {
		if( $this->getFoto($this->post->getInt('id')) )
			$this->json->foto = array(
				'titulo' 		=> $this->foto->foto_titulo,
				'descripcion' 	=> $this->foto->foto_descripcion
			);
		else
			$this->json->foto = null;
	}
	
	function guardar_accion() {
		if( $this->sesion->usuario ) {
			if( $this->getFoto($this->post->getInt('id'))
			&&  $this->foto->usuario_id == $this->sesion->usuario->id ) {
				// Hacer el cambio en la base de datos
				self::_db()->crearConsulta()
					->actualizar('fotos')
					->donde('foto_id = ?', $this->foto->foto_id)
					->poner('foto_descripcion', $this->post->descripcion)
					->poner('foto_titulo', $this->post->titulo)
					->ejecutar();
					
				// Guardado correctamente
				$this->json->estado = 777;
				$this->json->foto = array(
					'titulo' 		=> $this->post->titulo,
					'descripcion'	=> $this->post->descripcion
				);
			} else {
				// No se tiene permiso
				// para editar la foto.
				$this->json->estado = 2;
			}
		} else {
			// No se ha iniciado sesión
			$this->json->estado = 1;
		}
	}
	
	function &getFoto($id) {
		// Crear la consulta de la foto
		$c = self::_db()->crearConsulta();
		$this->foto = $c->de('fotos')
		  ->seleccionar('fotos.*','u.usuario_usuario usuario')
		  ->donde('foto_id=?',$id)
		  ->ligarL('usuarios u','fotos.usuario_id','u.usuario_id')
		  ->limite(1)
		  ->leerModelo();

		if( $this->foto ) {
			// Obtener la lista de etiquetas
			$this->etiquetas = preg_split('/\s*\,\s*/',$this->foto->foto_tags);
			// Convertir la fecha a formato fecha
			$this->foto->fecha = new Fexem_Fecha($this->foto->foto_fecha);
		}
		return $this->foto;
	}
	
	function &getOtrasFotos($id_usuario) {
		$this->otrasFotos = self::_db()
			->seleccionar('fotos',array('usuario_id id','usuario_lote lote'))
			->donde('usuario_id = ?', $id_usuario)
			->ordenar('foto_fecha desc')
			->limite(8)
			->leerModelos();
	}
	
	function &getComentario($id) {
		// Crear la consulta de la foto
		$c = self::_db()->crearConsulta();
		$this->comentario = $c->de('comentarios')
		  ->donde('comentario_id=?',$id)
		  ->limite(1)
		  ->leerModelo();
		return $this->comentario;
	}
	
	function &getUsuario($usuario) {
		$this->usuario = self::_db()->crearConsulta()
			->seleccionar('usuario.*','usuario.usuario_id id',
				'usuario.usuario_lote lote')
			->de('usuarios usuario')
			->donde('usuario.usuario_usuario=?',$usuario)
			->leerModelo();
		return $this->usuario;
	}
	
	static function &_db() {
		if( !isset(self::$_db) )
			self::$_db = new Fexem_Database();
		return self::$_db;
	}
}