<?php

class Fexem_Database
{		
	private static $conn = NULL;
	private static $host;
	private static $usuario;
	private static $pass;
	private static $nombre;
	
	public function __construct($abrir = true) {
		// Crear la conexi�n en caso de no existir
		if( $abrir )
			$this->abrir();
	}
	
	public static function preparar( $host, $usuario, $nombre, $pass ) {
		self::$host = $host;
		self::$usuario = $usuario;
		self::$nombre = $nombre;
		self::$pass = $pass;
	}
	
	static function crear() {
		return new Fexem_Database();
	}
	
	function existe($tabla, $campo, $valor=null) {
		if( is_array($campo) )
			foreach($campo as $k => $v)
				$str_where .= " AND {$k} = '{$v}'";
		else
			$str_where = " AND {$campo} = '{$valor}'";
		$qr = $this->ejecutar("SELECT * FROM {$tabla} WHERE 0 = 0{$str_where} LIMIT 1");
		$r = (mysqli_num_rows($qr) == 1);
		mysqli_free_result($qr);
		return $r;
	}
	
	function insertar($tabla, $valores) {
		$sql_valores = '';
		$inicio = true;
		if( is_array($valores) ):
			foreach( $valores as $k => $v ):
				if( !$inicio ):
					$sql_valores .= ',';
				else:
					$inicio = false;
				endif;
				$sql_valores .= '`' . $k . '` = \'' . mysqli_real_escape_string($v) . '\'';
			endforeach;
		endif;
		
		mysqli_query( "
			INSERT INTO `{$tabla}`
			SET	{$sql_valores}", self::$conn );
		
		return mysqli_insert_id(self::$conn);
	}
	
	function obtenerParejas($tabla,$campo_clave,$campo_valor)
	{
		$arr = array();
		
		$q = $this->ejecutar("
			SELECT {$campo_clave} as id,{$campo_valor} as valor
			FROM {$tabla}
			ORDER BY {$campo_valor} ASC
		");
		
		while( $r = $this->leer($q) )
			$arr[$r['id']] = $r['valor'];
		
		return $arr;
	}
	
	public function abrir() {
		if( !self::$conn )
		{
			self::$conn = mysqli_connect( self::$host, self::$usuario, self::$pass );
			mysqli_select_db( self::$conn, self::$nombre );
		}
	}

	public function cerrar() {
		mysqli_close( self::$conn );	
	}
	
	public function unRegistro($query)
	{
		$qr = $this->ejecutar($query.' LIMIT 1');
		if(!mysqli_num_rows($qr))
			return NULL;
		$rs = $this->leer($qr);
		mysqli_free_result($qr);
		
		return $rs;
	}
	
	/**
	 * Crea una consulta de un solo registro
	 * a partir de una tabla específica.
	 */
	function un($tablas, $valid = null) {
		$c = $this->de($tablas);
		if( $valid != null )
			$c->si('id=?',$valid);
		$c->lim(1);
		return $c;
	}
	
	function contarRegistros($query)
	{
		if( $query )
			return mysqli_num_rows($query);
		return 0;
	}
	
	function registrosAfectados()
	{
		return mysqli_affected_rows(self::$conn);
	}
	
	public function ejecutar($sql)
	{ 
		try
		{
			echo mysqli_error(self::$conn);
			return mysqli_query(self::$conn, $sql);
		}
		catch(Exception $ex)
		{
			throw new Exception(mysqli_error(self::$conn));
		}	
	}
	
	/**
	 * Crea una consulta de selección especificando
	 * los campos a elegir, y la tabla.
	 * @param $tablas
	 * @param $campos
	 */
	function seleccionar($tablas,$campos = null)
	{
		$c = $this->crearConsulta();
		$c->de($tablas);
		if( is_string($campos) )
			$c->seleccionar(array($campos));
		elseif( is_array($campos) )
			$c->seleccionar($campos);
		return $c;
	}
	
	function &seleccionarUno($tablas,$campos = null)
	{
		$c = $this->seleccionar($tablas,$campos);
		$c->solo(1);
		return $c;
	}
	
	function leer($query) {
		if( $reg = mysqli_fetch_assoc($query) )
			return $reg;
		return null;
	}
	
	function leerModelo($query)
	{
		if( $mod = mysqli_fetch_assoc($query) )
			return Fexem_Modelo::__crear($mod);
		return null;
	}
	
	function crearConsulta()
	{
		$consulta = new Fexem_Consulta();
		$consulta->aplicarDb($this);
		return $consulta;
	}
	/**
	 * Crea una consulta con respecto a la base
	 * de datos actual.
	 */
	function consulta() {
		$c = $this->crearConsulta();
		return $c;
	}
	/**
	 * Crea una consulta a partir del nombre de la tabla
	 * que se realizará la inserción, actualización 
	 * o retorno de información.
	 * 
	 * @param $tablas Arreglo o nombre de tabla.
	 */
	function de($tablas) {
		$c = $this->consulta();
		$c->de($tablas);
		return $c;
	}
}