<?php

if( isset($_GET['PHPSESSID']) )
	session_id($_GET['PHPSESSID']);
else if( isset($_POST['PHPSESSID']) )
	session_id($_POST['PHPSESSID']);
session_start();

set_include_path('../');

global $db;
global $BASE_DIR;
$BASE_DIR = dirname(dirname(__FILE__));

spl_autoload_register(function($class) {
	$c = str_replace('_','/',strtolower($class).'.class.php');
	require_once file_exists('../libreria/' . $c )
		? '../libreria/' . $c
		:  '../aplicacion/modelos/' . $c;
});

require_once 'aplicacion/config.php';
require_once 'libreria/fexem/aplicacion.class.php';
require_once 'libreria/fexem/layout.class.php';
require_once 'libreria/fexem/vista.class.php';
require_once 'aplicacion/functions.php';

// Preparar el directorio de las vistas y el layout principal
Fexem_Layout::preparar( 'interfaz.html' );
Fexem_Vista::preparar( '../aplicacion/vistas', '../compilados' );

// Crear e iniciar la aplicaci�n
$aplicacion = new Fexem_Aplicacion(
	'../aplicacion/controladores',
	'../aplicacion/vistas',
	array(
		'servidor'	=> DB_HOST,
		'usuario'	=> DB_USER,
		'nombre'	=> DB_NAME,
		'password'	=> DB_PASS	
	)
);
$aplicacion -> agregarRuta('@^([a-z0-9_.-]{4,16})/(\d+)$@i', 'flo', 'index');
$aplicacion -> agregarRuta('@^([a-z0-9_.-]{4,16})/fotos$@i', 'flo', 'fotos');
$aplicacion -> agregarRuta('@^([a-z0-9_.-]{4,16})$@i', 'flo', 'index');
$aplicacion->lanzar();