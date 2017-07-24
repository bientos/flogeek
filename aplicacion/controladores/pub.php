<?php
class Pub_Controlador extends Fexem_Controlador
{
	public $foto;
	
	private static $_db;
	
	function index_accion() {
		$this->vista->llenar('publicar.html',array(
			'session_id' => session_id()
		));
	}
	
	/*function dimensionar_accion() {
		global $BASE_DIR;
		$c = self::_db()->crearConsulta()
			->seleccionar('foto_lote','foto_id')
			->de('fotos')->ejecutar();
		while($foto = $c->leerUno()) {
			$file = 'img/fotos/'.$foto['foto_lote'].'/'.$foto['foto_id'].'_n.jpg';
			if(file_exists($file)) {
				if($img = imagecreatefromjpeg($file)) {
					$w = imagesx($img);
					$h = imagesy($img);
					self::_db()->crearConsulta()
						->limite(1)
						->actualizar('fotos')
						->donde('foto_id = ?', $foto['foto_id'])
						->poner('ancho', $w)
						->poner('alto',  $h)
						->ejecutar();
				}
				imagedestroy($img);
				unset($img);
			} else {
				//echo 'no existe';
			}
		}
		//$this->vista->imprimir('Hola');
	}*/
	
	function editar_accion() {
		// Obtener la información de la foto
		if( $this->getFoto($this->get->id) ) {
			if( $this->foto->usuario_id == $this->sesion->usuario->id )
				$this->vista->llenar('editarFoto.html');
			else
				$this->irA('');
		} else {
			$this->irA('');
		}
	}
	
	function guardar_accion()
	{
		if( $this->getFoto($this->get->id) )
		{
			self::_db()->crearConsulta()
				->seleccionar('usuarios');
		}
	}
	
	function anotar() {
		
	}
	
	function publicar_accion()
	{
		if( !$this->sesion->usuario )
		{
			// No se ha iniciado sesión
			echo 'NO_SESION';
			return;
		}
		$es_video = 0;
		$video_url = '';
		$video_tipo = 0;
		
		$imagen = new Fexem_Imagen();
		
		// Validar si se recibe un archivo
		if( $_FILES['archivo'] && $_FILES['archivo']['size'] > 0 )
		{
			$archivo = new Fexem_Modelo($_FILES['archivo']);
			// Abrir la imagen
			$imagen->cargar($archivo->tmp_name);
			
		}
		
		// Si la imagen no es válida, es posible
		// tomar un video desde URL.
		elseif( preg_match('@(https?://)?(www[.])?youtube[.]com/watch[?]v=([a-z0-9_-]+)@i', $this->post->url, $coin) )
		{
			// Tomar la imagen default del video
			$url = 'https://i3.ytimg.com/vi/'.$coin[3].'/default.jpg';
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$contenido = curl_exec($curl);
			$imagen->ponerImagen( imagecreatefromstring($contenido));
			$es_video = 1;
			$video_url = $coin[3];
			$video_tipo = 0;
			curl_close($curl);
		}
		
		
		// DESPLIEGUE -----------------
		
		// Validar que la imagen sea válida
		if( !$imagen->esValida() )
		{
			// La imagen no es válida
			$this->json->status = 'IMAGEN_NO_VALIDA';
		}
		else
		{
			$lote = rand(11,25);
			
			$db = new Fexem_Database();
			// Agrega la imagen a la base de datos
			$id_foto = $db->insertar('fotos',array(
				'usuario_id'      =>    $this->sesion->usuario->id,
				'foto_titulo'     =>    $this->post->titulo,
				'foto_descripcion'=>    $this->post->descripcion,
				'foto_fecha'      =>    new Fexem_Fecha(),
				'foto_tags'       =>    $this->post->tags,
				'foto_lote'       =>    $lote,
				'foto_es_video'	  =>    $es_video,
				'foto_video_id'   =>    $video_url,
				'foto_video_tipo' =>    $video_tipo
			));

		
			// Crear la imagen que se guardará
			if( $imagen->ancho > 700 || $imagen->alto > 640 )
				$imagen_o = $imagen->cambiarTamano(700,640,true);
			else
				$imagen_o =& $imagen;
			
			// Actualizar el ancho y el alto
			$db->crearConsulta()->actualizar('fotos')
				->donde('foto_id = ?', $id_foto)
				->poner('ancho', $imagen_o->ancho)
				->poner('alto' , $imagen_o->alto)
				->ejecutar();				
				
			// Recortar y guardar la imagen chica y la tiny
			$imagen_s = $imagen->cambiarTamano(220, null, true);
			$imagen_s->guardar("img/fotos/{$lote}/{$id_foto}_s.jpg",88,true);
			$imagen_t = $imagen->recortarCuadro(48);
			$imagen_t->guardar("img/fotos/{$lote}/{$id_foto}_t.jpg",84,true);
			// Guardar la original y liberar la memoria
			$imagen_o->guardar("img/fotos/{$lote}/{$id_foto}_n.jpg",92,true);
			
			// Redireccionar a la foto insertada
			$this->json->status = 'OK';
			$this->json->fotoId = $id_foto;
		}
		if( $this->get->__resp == 'redir' )	{
			if( $this->json->status == 'OK' )
				header('Location: https://flogeek.com/'.strtolower($this->sesion->usuario->usuario).'/'.$id_foto);
			else
				header('Location: https://flogeek.com/pub/?err='+$this->json->status);
			$db->close();
			exit;
		}
	}
	
	function despublicar_accion()
	{
		if( $this->getFoto($this->post->id)
		&&  $this->foto->usuario_id == $this->sesion->usuario->id)
		{
			// Borrar la foto de la base de datos
			self::_db()->crearConsulta()
				->eliminar('fotos')
				->donde('foto_id=?',$this->foto->foto_id)
				->limite(1)
				->ejecutar();
			// Resultado satisfactorio
			$this->json->status = 'OK';
		}
		else
		{
			$this->json->status = 'SIN_PERMISO';			
		}
		
	}
	
	function &getFoto($id)
	{
		$this->foto = self::_db()->crearConsulta()
			->de('fotos')
			->donde('foto_id=?',$id)
			->limite(1)
			->leerModelo();
		return $this->foto;
	}
	
	private static function &_db()
	{
		if( !isset(self::$_db) )
			self::$_db = new Fexem_Database();
		return self::$_db;
	}
}