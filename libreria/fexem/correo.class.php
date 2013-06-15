<?php

class Fexem_Correo
{
	private $para;
	private $asunto;
	private $de;
	private $vista;
	private $cuerpo;
	
	public function Correo($para,$asunto)
	{
		$this->para = $para;
		$this->asunto = $asunto;
	}
	
	public function de($de=null)
	{
		if( $de == null )
			return $this->de;
		$this->de = $de;
		return $this;
	}
	
	public function cargarVista($plantilla, $valores=null)
	{
		$vista = new Vista();
		if( is_array($valores) )
			$vista->assign('correo', $valores);
		$this->cuerpo = $vista->fetch($plantilla);
		return $this;
	}
	
	public function enviar()
	{
		$header  = "Content-Type: text/html\r\n";
		
		// Agregar DE en caso de haber
		if( isset($this->de) && strlen($this->de) > 0 )
			$header .= 'From: ' . $this->de . "\r\n";
			
		// Enviar el correo
		@mail($this->para, $this->asunto, $this->cuerpo,$header);
	}
}

?>