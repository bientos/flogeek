<?php

class Fexem_Post extends Fexem_Modelo
{
	function Fexem_Post()
	{
		parent::Fexem_Modelo();
		// Filtrar los valores
		if( get_magic_quotes_gpc() ):
			foreach( $_POST as &$p )
				$p = stripslashes($p);
		endif;
		$this->valores =& $_POST;
	}	
}