<?php

class Fexem_Sesion extends Fexem_Modelo
{
	function __construct()
	{
		parent::__construct();
		// Aplicar la sesión por referencia
		$this->valores =& $_SESSION;
	}
	
	function &__agregarForma($nombre, $forma = null)
	{
		if( !$this->valores['formas'] )
			$this->valores['formas'] = new Fexem_Modelo();
		if( $forma == null )
			$forma = new Modelo();
		$this->valores['formas']->__setRef( $nombre, $forma );
		return $this->valores['formas']->__obtenerRef($nombre);
	}
		
	function __existeForma($nombre)
	{
		return	$this->__existe('formas') && $this->valores['formas']->__existe($nombre);
	}
	
	function __quitarForma($nombre)
	{
		if( $this->__existeForma($nombre) )
			$this->valores['formas']->__quitar($nombre);
	}
}