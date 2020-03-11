<?php 

class Fexem_Fecha
{
	private $dia = 1;
	private $mes = 1;
	private $ano = 2010;
	private $hora = 0;
	private $minuto = 0;
	private $segundo = 0;
	
	const FECHA = 'FECHA';
	const FECHA_HORA = 'FECHA_HORA';
	const TIEMPO = 'TIEMPO';
	
	public static $meses = array(
		1  => 'Enero',		2  => 'Febrero',	3  => 'Marzo',
		4  => 'Abril',		5  => 'Mayo',		6  => 'Junio',
		7  => 'Julio',		8  => 'Agosto',		9  => 'Septiembre',
		10 => 'Octubre',	11 => 'Noviembre',	12 => 'Diciembre' 
	);
	
	private static $formato_entrada =
		'/^(\d{4,4})-(\d{1,2})-(\d{1,2})(\s(\d{1,2}):(\d{1,2}):(\d{1,2}))?/';
	
	function __construct($valor = null)
	{
		$this->setValor($valor
			? $valor : date('Y-m-d H:i:s') );
	}
	
	/**
	 * Establece la fecha y/o hora a partir
	 * de una cadena dada.
	 * @param $cadena
	 */
	function setValor($cadena)
	{
		// Validar que la fecha sea correcta
		if( preg_match(self::$formato_entrada, $cadena, $f ) )
		{
			if( checkdate($f[2], $f[3], $f[1]) )
			{
				// Guardar la fecha ya dividida
				$this->dia = intval($f[3]);
				$this->mes = intval($f[2]);
				$this->ano = intval($f[1]);

				// Poner la hora
				if( isset($f[5]) )
				{
					if( $f[5] >= 0 && $f[5] < 25)
						$this->hora = $f[5] == 24 ? 0 : intval($f[5]);
					$this->minuto = $f[6] >= 0 && $f[6] < 60
						? intval($f[6]) : 0;
					$this->segundo = $f[7] >= 0 && $f[7] < 60
						? intval($f[7]) : 0;
				}
			}
		}
	}
	
	/**
	 * Formatear la fecha.
	 * @param $formato
	 */
	function formato($formato, $divisor = '-')
	{
		if( $formato == self::FECHA )
			return "{$this->ano}{$divisor}{$this->mes}{$divisor}{$this->dia}";
		if( $formato == self::FECHA_HORA )
			return "{$this->ano}{$divisor}{$this->mes}{$divisor}{$this->dia} ".
					"{$this->hora}:{$this->minuto}:{$this->segundo}";
		if( $formato == self::TIEMPO )
			return "{$this->hora}:{$this->minuto}:{$this->segundo}";
	}
		
	function __toString()
	{
		return $this->formato(self::FECHA_HORA);
	}
	
	function getTiempo($considerar = self::FECHA_HORA)
	{
		if( $considerar == self::FECHA )
			return mktime(0, 0, 0,
				$this->mes, $this->dia, $this->ano);
		// Si no se dio formato, por default se tomará este.
		return mktime($this->hora, $this->minuto, $this->segundo,
				$this->mes, $this->dia, $this->ano);
	}

	function getHora() { return $this->hora; }
	function getMinuto() { return $this->minuto; }
	function getSegundo() { return $this->segundo; }
	function getAno() { return $this->ano; }
	function getMes() { return $this->mes; }
	function getMesNombre() { return self::$meses[$this->mes]; }
	function getDia() { return $this->dia; }
	
	static function getHoy()
	{
		return new Fecha(date('Y-m-d'));
	}
	
	static function getAhora()
	{
		return new Fecha();
	}
}