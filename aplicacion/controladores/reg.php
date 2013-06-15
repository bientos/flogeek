<?php
class Reg_Controlador extends Fexem_Controlador
{
	const LOTE_ACTUAL = 2;
	
	function index_accion() {
		$this->vista->llenar('registro/registro.html');
	}
	
	function genrecup_accion() {
		$p =& $this->post;
		$db = new Fexem_Database();
		if($p->correo and ($u=get_usuario_por_correo($p->correo))) {
			$ll1 = llave_unica();
			$ll2 = llave_unica();
			$db->Insertar('usuarios_recuperaciones', array(
				'id_usuario' => $u['usuario_id'],
				'fecha'      => date('Y-m-d H:i:s'),
				'token_a'    => $ll1,
				'token_b'    => $ll2,
				'status'     => 1
			));
			// Generar el correo
			$url = "http://flogeek.com/reg/confires/?t1={$ll1}&t2={$ll2}&uid={$u['usuario_id']}"; 
			$cuerpo = "
				Has solicitado reiniciar la contraseña de Flogeek.<br>
				Sigue el link para continuar.<br><br>
				<a href=\"{$url}\">{$url}</a><br><br>
				--------<br>
				Este correo de notificación fue enviado por la aplicación web flogeek.com.
			";
			mail($u['usuario_email'],
				"Recuperación de contraseña",$cuerpo,
				"From: Flogeek<notif@flogeek.com>\r\n".
				"Content-Type: text/html; charset=utf-8\r\n".
				"MIME-Version: 1.0\r\n",
				"-odb");
			$st = 'OK';
		} else {
			$st = 'NO_CORREO';
		}
		$this->json->status = $st;
	}

	/**
	 * Valida si los tokens son válidos, y si lo son
	 * muestra la interfaz de recuperación.
	 */
	function confires_accion() {
		$g =& $this->get;
		if(($rec = $this->get_recuperacion($g->t1, $g->t2))
		and $rec['id_usuario'] == $g->uid) {
			$this->vista->llenar('registro/contras.html');
		} else {
			$this->vista->imprimir('No se puede recuperar la contraseña');
		}
	}
	
	/**
	 * Lee y devuelve la información sobre una recuperación
	 * de usuario, se necesitan dos tokens para poder obtenerla.
	 * Y que su estado sea 1.
	 */
	function get_recuperacion($t1,$t2) {
		global $db;
		if(!$db) $db = new Fexem_Database();
		return $db->crearConsulta()->de('usuarios_recuperaciones')
			->seleccionar('id','id_usuario')
			->si('token_a = ?', $t1)
			->si('token_b = ?', $t2)
			->si('status = 1')
			->leerUno();
	}
	
	/**
	 * Confirma el cambio de contraseña 
	 */
	function cambpass_accion() {
		global $db;
		if(!$db) $db = new Fexem_Database();
		$p =& $this->post;
		if(($rec = $this->get_recuperacion($p->t1, $p->t2))
		and $rec['id_usuario'] == $p->uid) {
			if(!trim($p->pass1)) {
				$st = 'NO_PASS';
			} elseif($p->pass1 != $p->pass2) {
				$st = 'NO_MATCH';
			} else {
				// hacer el cambio de contraseña
				$db->crearConsulta()->actualizar('usuarios')
					->poner('usuario_password', md5($p->pass1))
					->si('usuario_id = ?', $rec['id_usuario'])->limite(1)
					->ejecutar();
				// cambiar el estado de la recuperación
				$db->crearConsulta()->actualizar('usuarios_recuperaciones')
					->poner('status','0')->limite(1)
					->si('token_a = ?', $p->t1)
					->si('token_b = ?', $p->t2)
					->ejecutar();
				$st = 'OK';
			}
		} else {
			$st = 'NO_RECU';
		}
		$this->json->status = $st;
	}
	
	function hazlo_accion() {
		$db = new Fexem_Database();
		if( !preg_match('/^[a-z0-9_.-]{4,16}$/i',$this->post->usuario) ) {
			// El usuario no coincide con el formato
			// especificado en la expresión regular.
			$status = 'NO_VALIDO';
		} else if( $db->crearConsulta()->limite(1)
		->de('usuarios')->donde('usuario_usuario = ?',$this->post->usuario)
		->leerModelo() ) {
			// Ya existe alguien con el mismo nombre de usuario
			$status = 'USUARIO_EXISTE';
		} else if( !preg_match('/^[a-z0-9_.#*$%&!-]{4,24}/i',$this->post->password_a) ) {
			// La contraseña no es válida
			$status = 'PASS_NO_VALIDO';
		} else if( $this->post->password_a != $this->post->password_b ) {
			// Las contraseñas son diferentes
			$status = 'PASS_DIF';
		} else if( !preg_match( '/^[a-z0-9]([-a-z0-9_.]*[a-z0-9])*\@[a-z0-9][-a-z0-9-]*[a-z0-9]*(\.[a-z0-9][-a-z0-9-]*[a-z0-9]*)+$/i', $this->post->mail ) ) {
			// El correo no es válido
			$status = 'CORREO_NO_VALIDO';
		} else if(  $db->crearConsulta()->limite(1)->de('usuarios')
		->donde('usuario_email = ?',$this->post->mail )->leerModelo() ) {
			// Otro usuario ya está usando este correo
			$status = 'CORREO_EXISTE';
		} else if( $this->post->captcha != $this->sesion->catpcha ) {
			// El captcha no coincide
			$status = 'CAPTCHA_INVALIDO';
		} else {
			$lote = self::LOTE_ACTUAL;
			
			// Hacer la inserción en la base de datos
			$usuarioID = $db->insertar('usuarios',array(
				'usuario_usuario'	=> $this->post->usuario,
				'usuario_password'	=> md5($this->post->password_a),
				'usuario_email'		=> $this->post->mail,
				'usuario_estilo'	=> 'thinkpro',
				'usuario_titulo'	=> 'Flogeek de ' . $this->post->usuario,
				'usuario_lote'		=> $lote,
				'fecha_alta'        => date('Y-m-d H:i:s')
			));
			
			if($usuarioID) {
				$status = 'OK';
			} else {
				$status = 'ERROR_DESCONOCIDO';
			}
			
			// También copiar la imagen default al directorio
			// de imágenes de usuario.
			copy('../img/perf_s.jpg',"../img/perfil/$lote/{$usuarioID}_s.jpg");
			copy('../img/perf_t.jpg',"../img/perfil/$lote/{$usuarioID}_t.jpg");
		}
		
		$this->json->status = $status;
	}

	/**
	 * Muestra el proceso de recuperación de contraseña.
	 * Se pide el correo de la persona al cual será enviado.
	 */
	function recuperar_accion() {
		$this->vista->llenar('registro/recuperar.html');
	}

	function generarClaveConfirmacion()
	{
		
	}
}