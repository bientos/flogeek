<?php

function get_usuario_por_id($id) {
	global $db; if(!$db) $db = new Fexem_Database();
	if(!$id) throw new Exeption('NO_ID');
	return $db->crearConsulta()->de('usuarios')->limite(1)
		->si('usuario_id = ?', $id)->leerUno();
}

function get_usuario_por_usuario($u) {
	global $db; if(!$db) $db = new Fexem_Database();
	if(!trim($u)) throw new Exeption('NO_USER');
	return $db->crearConsulta()->de('usuarios')->limite(1)
		->si('usuario_usuario = ?', trim($u))->leerUno();
}

function get_usuario_por_correo($mail) {
	global $db; if(!$db) $db = new Fexem_Database();
	if(!trim($mail)) throw new Exeption('NO_MAIL');
	return $db->crearConsulta()->de('usuarios')->limite(1)
		->si('usuario_email = ?', trim($mail))->leerUno();
}

function llave_unica() {
	return md5(uniqid(mt_rand(),true));
}