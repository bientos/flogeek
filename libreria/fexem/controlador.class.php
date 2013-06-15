<?php

class Fexem_Controlador
{
	private $vista;
	protected $json;
	protected $aplicacion;
	
	public $post;
	public $get;
	public $sesion;
	
	public $accion;
	public $parametros;
	
	private $db;
	
	function Fexem_Controlador($accion, $parametros = null) {
		$this->post = new Fexem_Post();
		$this->sesion = new Fexem_Sesion();
		$this->get = new Fexem_Get();
		
		$this->json = new Fexem_Modelo();
		
		if( is_string($accion) )
			$this -> accion = $accion;
		if( is_array($parametros) )
			$this -> parametros = $parametros;
	}
	
	function __call($nombre,$valores) {
		echo 'No encontrado';
	}
	
	function lanzar() {
		$accion = $this->accion.'_accion';
		if( method_exists($this,$accion) ) {
			$this -> $accion();
		}
		if( isset($this->vista) && $this->vista instanceOf Fexem_Vista )
			$this->vista->mostrar();
		elseif( $this->json->__conteo() )
			echo json_encode($this->json->__obtenerValores());
	}
	
	function setAplicacion(&$aplicacion) {
		if( $aplicacion instanceOf Fexem_Aplicacion )
			$this->aplicacion =& $aplicacion;
	}
	
	function &crearVista() {
		$this->vista = new Fexem_Layout();
		
		// Llenar con los valores default
		$this->vista->assign_by_ref('this',$this);
		
		return $this->vista;
	}
	
	function &__get($nombre) {
		if( $nombre === 'vista' ) {
			if(!isset($this->vista)) {
				$v = $this->crearVista();
				return $v;
			} else {
				return $this->vista;
			}
		} elseif( $nombre === 'db' ) {
			if( !$this->db )
				$this->db();
			return $this->db;
		}
	}
	
	function &db() {
		if( !$this->db )
			$this->db = new Fexem_Database();
		return $this->db;
	}
	
	/**
	 * Redirecciona a una ruta específica con respecto
	 * al directorio principal del sitio web.
	 * 
	 * Al ejecutar esta función, toda ejecuci�n
	 * PHP es truncada para evitar posibles
	 * ejecuciones de código background inesperadas. 
	 * 
	 * @param $url Dirección URL.
	 */
	protected function irA($url)
	{
		header("Location: http://{$_SERVER['HTTP_HOST']}/{$url}");
		exit;
	}
}