<?php

/**
 * Almacena, genera y ejecuta consultas
 * SQL de manera resumida sin utilizar
 * directamente c�digo SQL.
 * 
 * @author Gama
 * @package Fexem
 */
class Fexem_Consulta
{	
	/**
	 * Equivale a la palabra SELECT de SQL.
	 * 
	 * @var int
	 */
	const TIPO_SELECT = 0;
	
	/**
	 * Equivale a la palabra INSERT de SQL.
	 * 
	 * @var int
	 */
	const TIPO_INSERT = 1;
	
	/**
	 * Equivale a la palabra DELETE de SQL.
	 * 
	 * @var int
	 */
	const TIPO_DELETE = 2;
	
	/**
	 * Equivale a la palabra UPDATE de SQL.
	 * 
	 * @var int
	 */
	const TIPO_UPDATE = 3;
	
	/**
	 * Equivale a una relaci�n SQL del tipo INNER JOIN.
	 * 
	 * @var int
	 */
	const RELACION_INNER = 0;
	
	/**
	 * Equivale a una relaci�n SQL del tipo LEFT JOIN.
	 * 
	 * @var int
	 */
	const RELACION_LEFT = 1;
	
	/**
	 * Equivale a una relación SQL del tipo OUTER JOIN.
	 * 
	 * @var int
	 */
	const RELACION_OUTER = 2;
	
	
	const CONDICION_Y = 0;
	const CONDICION_O = 1;
	
	/**
	 * En una consulta de selecci�n guarda el n�mero
	 * de registros devueltos. Mientras que en una
	 * de actualizaci�n, alta o baja, hace referencia
	 * al n�mero de registros afectados/eliminados.
	 * 
	 * @var int
	 */
	public  $cantidad;
	
	/**
	 * Guarda el controlador nativo de consultas
	 * a la base de datos.
	 * 
	 * @var mixed
	 */
	private $consulta;
	
	/**
	 * Almacena el tipo de consulta, la cual puede ser
	 * de selecci�n, actualizaci�n, eliminaci�n o inserci�n.
	 * 
	 * El tipo por default es de selecci�n.
	 * 
	 * @var int
	 */
	private $tipo = self::TIPO_SELECT;
	
	/**
	 * Almacena el codigo SQL generado desde la
	 * funci�n Consulta->Generar.
	 * 
	 * @var string
	 */
	private $sql;
	
	/**
	 * Arreglo que contiene la lista de tablas
	 * con las que se trabajar�.
	 * 
	 * @var array
	 */
	private $de;
	
	/**
	 * Lista de tablas de las que se tomar�n los registros
	 * de la consulta, o en su defecto, en la cual se actualizar�n,
	 * insertar�n o eliminar�n registros.
	 * 
	 * @var array
	 */
	private $tablas;
	
	/**
	 * Lista de campos que se desea obtener su valor
	 * en una consulta de selecci�n.
	 * 
	 * @var array
	 */
	private $campos;
	
	/**
	 * Lista de condiciones que aplican para cualquier
	 * consulta, excepto las de inserci�n.
	 * 
	 * @var array
	 */
	private $condiciones;
	
	/**
	 * Puede determinar la posici�n inicial dentro
	 * de una tabla para comenzar a leer. O en defecto,
	 * si $limite_final no fue especificado, ser�
	 * considerado como la cantidad m�xima de registros
	 * tomados desde el primer registro encontrado
	 * en la consulta actual.
	 * 
	 * @var int
	 */
	private $limite_inicial;
	
	/**
	 * Determina el n�mero de registros obtenidos
	 * a partir de la posici�n guardada en $limite_inicial.
	 * 
	 * @var int
	 */
	private $limite_final;
	
	/**
	 * Lista de relaciones entre tablas.
	 * 
	 * @var array
	 */
	private $relaciones;
	
	/**
	 * Lista de valores que se establecer�n en una consulta
	 * de actualizaci�n o inserci�n.
	 * 
	 * @var array
	 */
	private $valores;
	
	/**
	 * Lista de campos que se ordenar�n y si son
	 * ascendenter o descendetes.
	 * 
	 * @var array
	 */
	private $orden;
	
	/**
	 * Guarda la lista de agrupaciones de la consulta.
	 * 
	 * @var array 
	 */
	private $agrupacion;
	
	/**
	 * Referencia a la base de datos establecida
	 * desde la funci�n aplicarDb.
	 * 
	 * @var Database
	 */
	private $db;
	
	/**
	 * Número de registros por lote.
	 * 
	 * @var int
	 */
	private $tamano_lote = 1;
	
	/**
	 * Relación del tipo de consulta y la palabra SQL
	 * que le corresponde.
	 * 
	 * @var array Se puede obtener la palabra clave
	 * especificando TIPO_SELECT, TIPO_UPDATE, TIPO_DELETE, TIPO_INSERT.
	 */
	private static $__sentencias = array(
		self::TIPO_SELECT	=>	'SELECT',
		self::TIPO_UPDATE	=>	'UPDATE',
		self::TIPO_DELETE	=>	'DELETE',
		self::TIPO_INSERT	=>	'INSERT INTO'
	);
	
	/**
	 * Relación del tipo de consulta y la palabra SQL
	 * en cuanto a relaciones entre tablas.
	 * 
	 * @var array Se puede obtener la palabra clave de las relaciones.
	 * especificando RELACION_INNER, RELACION_LEFT, RELACION_OUTER.
	 */
	private static $__sentencias_relacion = array(
		self::RELACION_INNER	=>	'INNER',
		self::RELACION_LEFT		=>	'LEFT',
		self::RELACION_OUTER	=>	'OUTER'
	);
	
	/**
	 * Relación del tipo de consulta y la palabra SQL
	 * en cuanto a las condiciones.
	 * 
	 * @var array Se puede obtener la palabra clave de las condiciones.
	 * especificando CONDICION_Y, CONDICION_O.
	 */
	private static $__sentencias_condicion = array(
		self::CONDICION_Y	=>	'AND',
		self::CONDICION_O	=>	'OR'
	);
	
	/**
	 * Agrega una tabla a la lista de tablas de selecci�n.
	 * 
	 * @param $tabla Nombre de la tabla.
	 * 
	 * @return Consulta Regresa la referencia de la misma consulta.
	 */
	function &de($tabla) {
		$this->tabla = is_array($tabla)
			? $tabla
			: array($tabla);
		
		return $this;
	}
	
	/**
	 * Genera la sentencia en formato SQL.
	 * 
	 * @return string
	 */
	function generar()
	{
		// 1. Seleccionar la palabra clave de la sentencia
		$this->sql = self::$__sentencias[$this->tipo];

		// 2. Crear la lista de campos (SELECT)
		if( $this->tipo == self::TIPO_SELECT ):
			// Crear la lista de campos
			$this->sql .= is_array($this->campos)
				? ' '.implode(',',$this->campos)
				: ' *';
		endif;
		
		// 3. Poner las tablas de origen (SELECT, DELETE, UPDATE)
		if( isset($this->tabla) ):
			$this->sql .= (
				$this->tipo == self::TIPO_SELECT || $this->tipo == self::TIPO_DELETE
				? ' FROM'
				: '' );
			$this->sql .= is_array($this->tabla)
				? ' ' . implode(',', $this->tabla)
				: " {$this->tabla}";
		endif;
		
		// 4. Crear las asignaciones
		if( $this->tipo == self::TIPO_UPDATE && isset($this->valores) ):
			$this->sql .= ' SET';
			foreach($this->valores as $k => $v):
				$this->sql .= " {$k}";
				if( $v != null )
					$this->sql .= " = '" . mysql_real_escape_string($v) . "'";
				$this->sql .= ",";
			endforeach;
			// Quitar la �ltima coma
			$this->sql = substr($this->sql,0,strlen($this->sql) - 1);
		endif;
				
		// 4. Crear las relaciones (SELECT)
		if( $this->tipo == self::TIPO_SELECT && isset($this->relaciones) ):
			foreach($this->relaciones as $rel):
				$this->sql .= ' ' .
					self::$__sentencias_relacion[$rel[0]] .
					" JOIN {$rel[1]}" .
					" ON {$rel[2]} = {$rel[3]}";
			endforeach;
		endif;
				
		// Tomar las condiciones
		$sql_condiciones = '';
		if( is_array($this->condiciones) ):
			foreach($this->condiciones as $k => $v):
				if( $sql_condiciones )
					$sql_condiciones .= ' ' . self::$__sentencias_condicion[$v['tipo']];
				// La condición es de igualdad
				$sql_condiciones .= ' ' . preg_replace('@\?@i', " '" . addslashes($v['valor']) . "'", $v['condicion'] ) ;
			endforeach;
			$this->sql .= " WHERE {$sql_condiciones}";
		endif;
		
		// Agregar las agrupaciones a la consulta de selecci�n
		if( isset($this->agrupacion) )
			$this->sql .= ' GROUP BY ' . implode(',',$this->agrupacion);
		
		// Poner los �rdenes
		if( isset($this->orden) )
			$this->sql .= ' ORDER BY ' . implode(',',$this->orden);
			
		// Poner los l�mites de registros
		if( isset($this->limite_inicial) ):
			$this->sql .= " LIMIT {$this->limite_inicial}";
			if( isset($this->limite_final) )
				$this->sql .= ", {$this->limite_final}";
		endif;
		//echo '<br /><br />'.$this->sql;
		return $this->sql;
	}
	
	/**
	 * Ejecuta la consulta en la base de datos
	 * seleccionada.
	 * 
	 * @return Consulta Retorna la consulta misma en modo de cadena.
	 */
	function &ejecutar()
	{
		if( isset($this->db) ):
			$this->consulta = $this->db->ejecutar($this->generar());
			// Obtener los datos de la consulta
			if( $this->tipo == self::TIPO_SELECT )
				$this->cantidad = $this->db->contarRegistros($this->consulta);
			else
				$this->cantidad = $this->db->registrosAfectados();
		endif;
		
		return $this;
	}
	
	/**
	 * Convierte la consulta de tipo actualizaci�n.
	 * 
	 * @param $tablas Nombre de la tabla a actualizar.
	 * @return Consulta
	 */
	function &actualizar($tablas)
	{
		$this->tipo = self::TIPO_UPDATE;
		return $this->de($tablas);
	}
	
	/**
	 * Convierte la consulta de tipo eliminaci�n.
	 * 
	 * @param $tablas Nombre o lista de las tablas
	 * de las que se eliminar�n.
	 * 
	 * @return Consulta
	 */
	function &eliminar($tablas=null)
	{
		$this->tipo = self::TIPO_DELETE;
		if( $tablas )
			return $this->de($tablas);
		return $this;
	}
	
	/**
	 * Establece un valor para la inserci�n
	 * y actualizaci�n de registros en la base de datos.
	 * 
	 * @param string $campo Nombre del campo.
	 * @param mixed $valor Valor a aplicar.
	 * 
	 * @return Consulta
	 */
	function &poner($campo,$valor = null)
	{
		$this->valores[$campo] = $valor;
		return $this;
	}
	
	/**
	 * Agrega una condici�n a la consulta.
	 * 
	 * @param $condicion Sentencia de la condici�n.
	 * @param $valor Valor de comparaci�n de la condici�n.
	 * @param $es_or [No usado]
	 * 
	 * @return Consulta
	 */
	function &donde($condicion, $valor, $es_or = false)
	{
		$this->condiciones[] = array(
			'tipo' => $es_or
				? self::CONDICION_O
				: self::CONDICION_Y,
			'condicion' => $condicion,
			'valor' => $valor
		);
		return $this;
	}
	
	function &dondeO($condicion, $valor)
	{
		return $this->donde($condicion, $valor, true);
	}
	
	/**
	 * Define los l�mites de registros para la consulta.
	 * 
	 * Si solo se especifica $inicio, se tomar� esa cantidad
	 * de registros a partir del primero registro encontrado.
	 * 
	 * Pero si se especifica $fin, $inicio ser� en qu�
	 * n�mero de registro se comenzar� a leer y $fin ser�
	 * la cantidad de registros leidos.
	 *  
	 * @param $inicio Cantidad o inicio de lectura de registros.
	 * @param $fin Cantidad de regisgtros le�dos.
	 * 
	 * @return Consulta
	 */
	function &limite($inicio, $fin = null)
	{
		if( is_numeric($inicio) ):
			$this->limite_inicial = $inicio;
			if( is_numeric($fin) )
				$this->limite_final = $fin;
		endif;
		
		return $this;
	}
	
	/**
	 * Lee el pr�ximo registro despu�s de haberse
	 * ejecutado la consulta.
	 * 
	 * @return array Lista de campos-valores.
	 */
	function leerUno()
	{
		if( !$this->consulta )
			$this->ejecutar();
		return $this->db->leer($this->consulta);		
	}
	
	/**
	 * Lee un registro de una consulta.
	 * @param $campo Se especifica cuando se
	 * quiere obtener solo ese campo.
	 */
	function leer($campo=null) {
		if( !$this->consulta )
			$this->ejecutar();
		if($this->consulta instanceOf mysqli_result) {
			$reg = $this->consulta->fetch_assoc();
			return ($campo != null and isset($reg[$campo]))
				? $reg[$campo] : $reg;
		}
		return null;
	}
	
	/**
	 * Retorn la lista de todos los registros
	 * devueltos por la consulta.
	 * 
	 * @param $llave
	 */
	function &leerTodos($llave = null)
	{
		// Si se trata de leer y no se ha ejecutado,
		// ejecutaremos para preparar la consulta.
		if( !isset($this->consulta) )
			$this->ejecutar();
			
		$items = array();
		while( $item = $this->leerUno() ):
			if( is_string($llave) )
				$items[$item[$llave]] = $item;
			else
				$items[] = $item; 
		endwhile;
		
		return $items;
	}
	
	function &lote($n)
	{
		if( !$n ) $n = 1;
		
		// Si no se ha ejecutado la consulta,
		// la realizamos para obtener.
		if( !$this->consulta )
			$this->ejecutar();
		
		// Volver a ejecutar la consulta
		// con los límites ya establecidos.
		$this->limite(($n-1)*$this->tamano_lote,$this->tamano_lote);
		
		// Realizar la consulta
		$this->ejecutar();
		
		// Devolver los límites
		$this->limite_inicial = 0;
		$this->limite_final = 0;
		
		return $this;
	}
	
	function &si($condicion,$valor,$es_or=false)
	{
		return $this->donde($condicion,$valor,$es_or);
	}
	
	function &setTamanoLote($n)
	{
		$this->tamano_lote = $n;
		return $this;
	}
	
	function &o($condicion,$valor)
	{
		return $this->donde($condicion,$valor,true);
	}
	
	function &ligarI($tabla,$campo1,$campo2)
	{
		return $this->ligar(self::RELACION_INNER, $tabla, $campo1, $campo2);
	}
	
	function &ligarL($tabla, $campo1, $campo2)
	{
		return $this->ligar(self::RELACION_LEFT, $tabla, $campo1, $campo2);
	}
	
	function &ligarO($tabla, $campo1, $campo2)
	{
		return $this->ligar(self::RELACION_OUTER, $tabla, $campo1, $campo2);
	}
	
	function &ligar($tipo, $tabla, $campo1, $campo2)
	{
		$this->relaciones[] = array($tipo, $tabla, $campo1, $campo2);
		return $this;
	}
	
	function &leerModelos($llave = null)
	{
		// Si se trata de leer y no se ha ejecutado,
		// ejecutaremos para preparar la consulta.
		if( !isset($this->consulta) )
			$this->ejecutar();
			
		$items = array();
		while( $item = $this->leerModelo() ):
			if( is_string($llave) )
				$items[$item->$llave] = $item;
			else
				$items[] = $item; 
		endwhile;
		
		return $items;
	}

	function leerModelo()
	{
		if( !$this->consulta )
			$this->ejecutar();
		
		return $this->db->leerModelo($this->consulta);
	}
	
	function aplicarDb(&$db)
	{
		if( $db instanceOf Fexem_Database )
			$this->db = $db;
	}
	
	function &__call($nombre,$param)
	{
		switch($nombre):
			case 'seleccionar':
			case 'sel':
				if( count($param) )
					$this->campos = $param;
				else
					$this->campos = array('*');
				return $this;
				break;
			case 'ordenar':
				if( count($param) )
					$this->orden = $param;
				return $this;
				break;
			case 'agrupar':
				if( count($param) )
					$this->agrupacion = $param;
				return $this;
		endswitch;
	}
}