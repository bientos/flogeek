<?php
class Fexem_Get extends Fexem_Modelo
{
	function __construct()
	{
		parent::__construct($_GET);
		if( get_magic_quotes_gpc() ):
			foreach( $this->valores as &$v )
				$v = stripslashes($v);
		endif;
	}	
}