<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Home extends CI_Controller {

	 function __construct() {
	   parent::__construct();
	 }

	 function index() {
	   if($this->session->userdata('newadt')) {
	     $session_data = $this->session->userdata('newadt');	     
	     $data['usuario'] = $session_data['usuario'];
<<<<<<< HEAD
	     $data['usu_permissoes'] = $session_data['usu_permissoes'];
	     $data['usu_filial'] = $session_data['usu_filial'];
	     $data['usu_email'] = $session_data['usu_email'];
	     $data['usu_codigo'] = $session_data['usu_codigo'];
	     $data['usu_area'] = $session_data['usu_area'];
=======
	     $data['usu_permissoes'] = $session_data['usu_permissoes'];	     
	     $data['usu_email'] = $session_data['usu_email'];
	     $data['usu_codigo'] = $session_data['usu_codigo'];	     
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
	     	     
	     $this->load->view('header_view', $data);
	     $this->load->view('home_view');
	     $this->load->view('footer_view');
	     //print_r($session_data);
	     //echo $data['usuario'];
	     //echo $data['perfil'];
	   } else {
	     //If no session, redirect to login page
	     redirect('login', 'refresh');
	   }
	 }
	 
	 function logout() {
	   $this->session->unset_userdata('newadt');
	   session_destroy();
	   redirect('login', 'refresh');
	 }
}
?>
