<?php

class Fexem_Modelo
{
	protected $valores;
	
	function __construct($valores=null)
	{
		if( is_array($valores) )
			$this->valores = $valores;
	}
	
	static function __crear($valores=null)
	{
		return new Fexem_Modelo($valores);
	}
	
	public function __get($nombre)
	{
		if( isset($this->valores[$nombre]) )
			return $this->valores[$nombre];
		return '';
	}
	
	public function __set($nombre,$valor)
	{
		$this->valores[$nombre] = $valor;
	}
	
	public function __call($nombre, $valores)
	{
		if( count($valores) == 0 )
			return $this->valores[$nombre];
		$this->valores[$nombre] = $valores[0];
		return $this;
	}
	
	public function __quitarElemento($nombre)
	{
		unset($this->valores[$nombre]);
	}
	
	function __quitar($nombre)
	{
		if( $this->__existe($nombre) )
			unset( $this->valores[$nombre] );
	}
	
	function &__obtenerRef($nombre)
	{
		return $this->valores[$nombre];
	}
	
	function &__setRef($nombre, &$valor)
	{
		$this->valores[$nombre] = $valor;
	}
	
	function &__agregar($nombre)
	{
		$this->valores[$nombre] = new Fexem_Modelo();
		return $this->valores[$nombre]; 
	}
	
	function __existe($nombre)
	{
		return isset($this->valores[$nombre]);
	}
	
	public function toArray()
	{
		return $this->valores;
	}
	
	public function getInt($nombre)
	{
		return is_numeric($this->$nombre)
			? intval($this->$nombre) : 0;
	}
	
	function &__obtenerValores()
	{		
		return $this->valores;
	}
	
	function __pop($nombre)
	{
		$valor = null;
		if( $this->__existe($nombre) )
			$valor = $this->valores[$nombre];
		unset($this->valores[$nombre]);
		return $valor;
	}
	
	function __conteo()
	{
		return isset($this->valores) && is_array($this->valores)
			? count($this->valores) : 0;
	}
}

?>