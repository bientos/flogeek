<?php

require_once 'libreria/smarty/Smarty.class.php';

/**
 * Representa el Layout principal de la p�gina
 * al que se le puede agregar el contenido.
 */
class Fexem_Vista extends Smarty
{
	private $contenido = null;
	private static $directorio_vistas;
	private static $directorio_compilados;
	
	function __construct()
	{
		$this->template_dir = self::$directorio_vistas;
		$this->compile_dir = self::$directorio_compilados;
	}
	
	/**
	 * Prepara los directorios necesarios para las vistas
	 * y los compilados.
	 * 
	 * @param $vistas Directorio de vitas.
	 * @param $compilados Directorio de compilados (php).
	 */
	static function preparar($vistas, $compilados)
	{
		self::$directorio_vistas = $vistas;
		self::$directorio_compilados = $compilados;
	}
	
	/**
	 * Finalmente muestra la vista en pantalla.
	 */
	function mostrar($archivo = null)
	{
		$this->display($archivo);
	}
	
	/**
	 * Aplicar el contenido de un archivo a una etiqueta Smarty.
	 * 
	 * @param $a Nombre del tag.
	 * @param $b Valor que tomará.
	 * 
	 * @return Vista
	 */
	function &aplicarVista($a,$b)
	{
		$this->assign($a,$this->fetch($b));
		return $this;
	}
	
	/**
	 * Aplicar un valor a una etiqueta Smarty.
	 * 
	 * @param $a Nombre de la etiqueta.
	 * @param $b Valor a aplicar.
	 * 
	 * @return Vista
	 */
	function &aplicar($a,$b=null)
	{
		$this->assign($a,$b);
		return $this;
	}
}

?>