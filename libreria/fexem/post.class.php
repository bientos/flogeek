<?php

class Fexem_Post extends Fexem_Modelo
{
	function __construct()
	{
		parent::__construct();
		// Filtrar los valores
		$this->valores =& $_POST;
	}	
}