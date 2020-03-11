<?php
class Fexem_Get extends Fexem_Modelo
{
	function __construct()
	{
		parent::__construct($_GET);
	}	
}