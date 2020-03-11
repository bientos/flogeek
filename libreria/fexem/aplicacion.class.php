<?php

/**
 * Da entrada a cada una de las partes
 * que conforman la aplicación web.
 * 
 * @author Gama
 * @package Fexem
 */
class Fexem_Aplicacion
{
	/**
	 * Almacena la instancia de la conexi�n a la base
	 * de datos actual.
	 * 
	 * @var Database
	 */
	private $_db;

	/**
	 * Controlador al que dirige la aplicaci�n.
	 * @var Controlador
	 */
	private $controlador;
	
	/**
	 * Directorio en que se encuentran los controladores.
	 * 
	 * @var string
	 */
	private $dir_cont;
	
	/**
	 * Directorio donde se almacenan las vistas.
	 * @var string
	 */
	private $dir_vistas;
	
	/**
	 * Guarda el objeto ACL
	 */
	private $acl;
	
	/**
	 * Lista de rutas de direcciones.
	 * @var unknown_type
	 */
	private $rutas;
	 
	/**
	 * Instancia la aplicaci�n.
	 */
	function __construct($dir_cont = null, $dir_vistas = null, $basedatos = null)
	{
		// Componer la url completa
		$this->url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		if( is_string($dir_cont) )
			$this -> dir_cont = $dir_cont;
		if( is_string($dir_vistas) )
			$this -> dir_vistas = $dir_vistas;
		if( is_array($basedatos) )
			Fexem_Database::preparar(
				$basedatos['servidor'],
				$basedatos['usuario'],
				$basedatos['nombre'],
				$basedatos['password']
			);
	}
	
	/**
	 * Lanza la aplicaci�n.
	 */
	function lanzar()
	{
		// Filtrar la dirección para dejarla ready to use:
		//    1. Remover lo que esté después del primer signo de interrogación.
		//    2. Quitar las diagonales duplicadas.
		//    3. Eliminar las del fin.
		//    4. Y las del principio.
		//    5. Convertir el texto a minúsculas.
		$_url = strtolower(preg_replace(array('/[?].*/','/\/{2,}/','/\/+$/','/^\/+/'),array('','/','',''),$_SERVER['REQUEST_URI']));
		
		// Parsear la dirección ya limpia
		$_URL = explode('/', $_url);
		
		try
		{
			// Opción 1. Ver si hay rutas
			if( $ruta = $this->obtenerRuta($_url) )
			{
				$cont = $ruta['controlador'];
				$accion = $ruta['accion'];
				$param = $ruta['parametros'];
				
			}
			// Opción 2. Si es el index principal
			elseif( !$_URL[0] )
			{
				list($cont,$accion,$param) = 
					array('index','index', array());
			}
			// Opción 3. O si es algún otro controlador
			elseif( preg_match('/[a-z0-9_]/i', $_URL[0]) )
			{
				$cont = $_URL[0];
				$accion = isset($_URL[1]) && preg_match('/[a-z0-9_]/i', $_URL[1])
					? $_URL[1]
					: 'index';
				$param = $accion != 'index' && isset($_URL[2])
					? array_slice($_URL,2)
					: array();
			}
			else throw new Exception();
		
			// Validar que exista el controlador
			$e_cont = file_exists("{$this->dir_cont}/{$cont}.php");
			
			if( $e_cont )
			{
				require_once "{$this->dir_cont}/{$cont}.php";
				$nomcont = ucfirst($cont).'_Controlador';
				$this->controlador = new $nomcont($accion, $param);
			}
			else throw new Exception();
		}
		catch(Exception $ex)
		{
			require_once "{$this->dir_cont}/error.php";
			$this -> controlador = new Error_Controlador('index',array(404));
		}
		
		// Lanzar el controlador
		$this -> controlador -> lanzar();
	}
	
	function agregarRuta($expresion,$controlador,$accion)
	{
		$this -> rutas [$expresion] = array(
			'expresion'		=> $expresion,
			'controlador'	=> $controlador,
			'accion'		=> $accion
		);
	}
	
	private function obtenerRuta($url)
	{
		if( isset($this->rutas) && is_array($this->rutas) )
		{
			foreach($this->rutas as $expresion => $ruta)
			{
				if( preg_match($expresion, $url, $coin ) )
				{
					$ruta['parametros'] = $coin;
					return $ruta;
				}
			}
		}
		return null;
	}
	
	function __get($nombre)
	{
		if( $nombre == 'db' ):
			if( !isset($this->_db) )
				$this->_db = new Fexem_Database();
			return $this->_db; 
		endif;
	}
}