<?php
class Foto_Controlador extends Fexem_Controlador
{
	function index_accion()
	{
		// Llamar a la vista de publicación
		$this->vista->llenar('publicar.html');
	}
}