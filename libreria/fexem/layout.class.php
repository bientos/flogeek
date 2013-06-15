<?php

/**
 * Representa el Layout principal de la p�gina
 * al que se le puede agregar el contenido.
 */
class Fexem_Layout extends Fexem_Vista
{
	public $archivo_contenido;
	public static $archivo_interfaz;
	public static $menu;

	function Fexem_Layout($archivo = null)
	{
		parent::Fexem_Vista();
		if( $archivo )
			$this->archivo_contenido = $archivo;
	}
	
	/**
	 * Prepara el archivo principal del Layout.
	 */
	static function preparar($archivo)
	{
		self::$archivo_interfaz = $archivo;
	}
	
	/**
	 * Mostrar la página junto al layout.
	 */
	function mostrar()
	{
		if( $this->archivo_contenido )
			$this->aplicar('contenido', $this->fetch($this->archivo_contenido));
		if( self::$menu ):
			$this->aplicar('menu_principal', self::$menu);
		endif;
		// Aplicar el layout
		parent::mostrar( self::$archivo_interfaz );
	}
	
	/**
	 * Llenar la parte del conteido del layout.
	 * @param $archivo
	 * @param $valores
	 * @return unknown_type
	 */
	function &llenar($archivo, $valores = null)
	{
		$this->archivo_contenido = $archivo;
		if( is_array($valores) )
			foreach($valores as $k => $v)
				$this->aplicar($k,$v);
		return $this;
	}
	
	function &imprimir($contenido)
	{
		$this->aplicar('contenido', $contenido);
		return $this;
	}
}

?>