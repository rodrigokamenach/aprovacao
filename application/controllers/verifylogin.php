<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class VerifyLogin extends CI_Controller {
	
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'user', '', TRUE );
	}
	
	function index() {
		// This method will have the credentials validation
		$this->load->library ( 'form_validation' );
		$this->load->helper ( 'security' );
		error_reporting(0);
		ini_set('display_errors', 0);
		$this->form_validation->set_message ( 'required', '<div class="alert alert-danger">O campo %s é obrigatório</div>' );
		
		$this->form_validation->set_rules ( 'username', 'Usuario', 'trim|required|xss_clean' );
		$this->form_validation->set_rules ( 'password', 'Senha', 'trim|required|xss_clean|callback_check_database' );
		
		if ($this->form_validation->run () == FALSE) {
			// Field validation failed. User redirected to login page
			$this->load->view ( 'login_view' );
		} else {
			// Go to private area
			redirect ( 'home', 'refresh' );
		}
	}
	
	
	function check_database($password) {
		// Field validation succeeded. Validate against database
		// VALIDAÇAO DO LDAP DESTIVADO 17/07/2019
		$username = $this->input->post( 'username' );
		// $userldap = htmlentities ( $this->input->post ( 'username' ) ) . '@jboc.local';
		//$password = htmlentities($password);		
		// $ldap_serv = 'ldap://193.169.0.5';
		// $ldap_port = '389';
		// $lc = ldap_connect ( $ldap_serv, $ldap_port );
		// ldap_set_option ( $lc, LDAP_OPT_REFERRALS, 0 );
		// ldap_set_option ( $lc, LDAP_OPT_PROTOCOL_VERSION, 3 );
		// $ldapbind = ldap_bind ( $lc, $userldap, $password );
		// var_dump($ldapbind);
		// exit();
		// if (!($ldapbind)) {			
		// 	$this->form_validation->set_message ( 'check_database', '<div class="alert alert-danger">Usuário ou senha inválidos!</div>' );
		// 	return false;
		// } else {
			$password = md5($password);			
			// query the database
			$result = $this->user->login($username);
			// var_dump($result);
			// exit();
			$codusu = $result[0]['CODUSU'];
			$nomusu = $result[0]['NOMUSU'];
			$verifyusu = $result[0]['USU_NOMUSU'];
			$mailusu = $nomusu . '@jotabasso.com.br';
			
			if ($verifyusu == null) {
				
				//echo 'cadastra';
				$dados_usu = array (
						$codusu,
						$nomusu,
						$mailusu,						
						'APR',																		
						'S;N;N;N',
						$password
				);
				
				$sql = "INSERT INTO usu_tadtusu (USU_CODUSU ,USU_NOMUSU ,USU_MAILUS ,USU_SISTEM ,USU_PERMIS, USU_PASS) VALUES (?, ?, ?, ?, ?, ?)";
				
				$cadastro = $this->user->inserir( $sql, $dados_usu );
				
				if ($cadastro) {
					$getPerm = $this->user->getPermissao ( $nomusu );
					if ($getPerm) {
						$sess_array = array ();
						foreach ( $getPerm as $row ) {
							$sess_array = array (
									'usuario' => $row ['USU_NOMUSU'],
									'usu_permissoes' => $row ['USU_PERMIS'],									
									'usu_email' => $row ['USU_MAILUS'],
									'usu_codigo' => $row ['USU_CODUSU'],									
							);
							$this->session->set_userdata ( 'newadt', $sess_array );
						}
						return TRUE;
					}
				}
			} else {

				$getPerm = $this->user->getPermissao ( $nomusu );
				if (strpos ( $getPerm [0] ['USU_SISTEM'], 'APR' ) !== false) {
					$sess_array = array ();
					foreach ( $getPerm as $row ) {
						$sess_array = array (
								'usuario' => $row ['USU_NOMUSU'],
								'usu_permissoes' => $row ['USU_PERMIS'],								
								'usu_email' => $row ['USU_MAILUS'],
								'usu_codigo' => $row ['USU_CODUSU']								
						);
						$this->session->set_userdata ( 'newadt', $sess_array );
					}
					return TRUE;
				} else {
					$this->form_validation->set_message ( 'check_database', '<div class="alert alert-danger">Usuario não tem acesso ao sistema de Aprovação!</div>' );
					return false;
				}
			}
		// }
	}
}
?>