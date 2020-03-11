<?php

/**
 * Guarda informaci�n sobre los permisos de acceso que
 * tiene un usuario con respecto a una serie de roles.
 * @author Gamaliel
 * 
 * @package Fexem
 */
class Fexem_Acl
{
	private $accesos;
	private $rol;
	
	/**
	 * Instancia el ACL a partir de un arreglo
	 * con el formato:
	 * 
	 * Arreglo:
	 * 		categoria_1 => Arreglo:
	 * 			1, 2, 3, 4
	 * 		categoria_2 => Arreglo:
	 * 			2, 3
	 * 		categoria_3 => Arreglo:
	 * 			1, 2, 4
	 * @param $accesos Arreglo de permisos.
	 * @param $rol Rol actual.
	 */
	function __construct($accesos, $rol)
	{
		$this->rol = $rol;
		$this->accesos = is_array($accesos)
			? $accesos
			: array();
	}
	
	/**
	 * Valida si el rol actual tiene permiso de acceso
	 * a la direcci�n espec�ficada.
	 * 
	 * @param $url Direcci�n URL a validar.
	 * @return bool
	 */
	function estaPermitido($url)
	{
		// Corregir la URL para coincidir con el ACL
		$url = preg_replace('@^/?(.*?)/*@i', '$1', $url);
		
		if( !$url ):
			return true;
		else:
			foreach($this->accesos as $_url => $roles):
				if( strpos($url,$_url) === 0 ):
					return in_array($this->rol,$roles);
				endif;
			endforeach;
		endif;
	}
	
	/**
	 * Retorna la referencia al arreglo interno de los
	 * accesos.
	 * 
	 * @return array
	 */
	function &obtenerAccesos()
	{
		return $this->accesos;
	}
}