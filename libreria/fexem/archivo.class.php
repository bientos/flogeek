<?php
class Fexem_Archivo
{
	static function imprimirContenido($archivo)
	{
		$contenido = file_get_contents($archivo, FILE_BINARY);
		echo $contenido;
	}
	
	static function obtenerExtension($archivo)
	{
		return preg_replace('@.*?\.([a-z0-9]+)$@i','$1',$archivo);
	}
}