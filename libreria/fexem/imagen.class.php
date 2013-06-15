<?php

class Fexem_Imagen
{
	private $imagen;
	private $ancho;
	private $alto;
	private $tipo;
	
	const JPEG	= 1;
	const GIF	= 2;
	const PNG	= 3;
	const BMP	= 4;
	
	public function Fexem_Imagen( &$imagen = null )
	{
		if( !is_null($imagen) )
			$this->ponerImagen( $imagen );
	}
	
	/**
	 * Carga una imagen en memoria.
	 * 
	 * @param string $archivo Nombre del archivo.
	 * @param mixed $mime Formato de la imagen leída.
	 * 
	 * @throws Exception
	 */
	public function cargar($archivo, $mime='image/jpeg')
	{		
		// Obtener las medidas de la imagen
		if( file_exists($archivo) )
		{
			if( $mime == 'image/png' || $mime == self::PNG )
			{
				$this->tipo = self::PNG;		
				$img = imagecreatefrompng($archivo);
			}
			elseif( $mime == 'image/gif' || $mime == self::GIF )
			{
				$this->tipo = self::GIF;
				$img = @imagecreatefromgif($archivo);
			}
			elseif( $mime == 'image/jpeg' || $mime == 'image/pjpeg' || $mime == self::JPEG )
			{
				$this->tipo = self::JPEG;
				$img = @imagecreatefromjpeg($archivo);
			}
			else
			{
				throw new Exception("Formato de Imagen no soportado.");
			}
			
			if( $img ) $this->ponerImagen ($img);
		}
	}
	
	public function ponerImagen(&$imagen)
	{
		$this->imagen =& $imagen;
		$this->ancho = imagesx($this->imagen);
		$this->alto = imagesy($this->imagen);
	}
	
	public function esValida()
	{
		return $this->imagen;
	}

	public function guardar($archivo, $calidad = 80, $liberar = false)
	{
		imagejpeg( $this->imagen, $archivo, $calidad );
			
		// Decidir si se libera la memoria
		if( $liberar )
			$this->liberar();
	}
	
	function liberar()
	{
		if( $this->imagen )
			imagedestroy( $this->imagen );
	}
	
	public function &recortarCuadro($tamano=null)
	{
		if( $this->ancho > $this->alto )
			$tam = $this->alto;
		elseif( $this->alto > $this->ancho )
			$tam = $this->ancho;
		else
			$tam = $this->alto;
			
		// Obtener las posiciones
		$x = ($this->ancho / 2) - ($tam / 2);
		$y = ($this->alto / 2) - ($tam / 2);
		
		// Se define el tama�o al que se forzar�
		if( is_null($tamano) || !is_numeric($tamano) || $tamano == 0 )
			$tamano = $tam;
		
		// Crear la imagen recortada
		$img = imagecreatetruecolor( $tamano, $tamano );
		imagecopyresampled( $img, $this->imagen, 0, 0, $x, $y, $tamano, $tamano, $tam, $tam );		
		
		return new Fexem_Imagen( $img );
	}
	
	public function &cambiarTamano($w,$h=0, $m_prop = true)
	{
		if( $m_prop ):
			if( ($this->ancho >= $this->alto) || ($w > 0 && $h == 0) ):
				$prop = $this->alto / $this->ancho;
				$ancho = $w;
				$alto = $ancho * $prop;
			else:
				$prop = $this->ancho / $this->alto;
				$alto = $h;
				$ancho = $alto * $prop;
			endif;
		else:
			list( $ancho, $alto ) =	array( $w, $h );
		endif;
		
		// Crear la nueva imagen
		$img = imagecreatetruecolor($ancho,$alto);
		imagecopyresampled( $img, $this->imagen, 0, 0, 0, 0, $ancho,$alto, $this->ancho, $this->alto);
		return new Fexem_Imagen( $img );
	}
	
	public function __get($k)
	{
		if( $k == 'ancho' || $k == 	'width' )
			return $this->ancho;
		elseif( $k == 'alto' || $k == 'height' )
			return $this->alto;
	}
}

?>