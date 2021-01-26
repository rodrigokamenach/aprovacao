<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Consc extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('globals','',TRUE);
		$this->load->model('scm','',TRUE);
		$this->load->model('ocs','',TRUE);
	}
	
	function index() {
		if($this->session->userdata('newadt')) {
			$session_data = $this->session->userdata('newadt');
			$data['usuario'] = $session_data['usuario'];
			$data['usu_permissoes'] = $session_data['usu_permissoes'];
			$data['usu_filial'] = $session_data['usu_filial'];
			$data['usu_email'] = $session_data['usu_email'];
			$data['usu_codigo'] = $session_data['usu_codigo'];
			$data['usu_area'] = $session_data['usu_area'];
	
	
			if (empty($dia)) {
				$dia = date('m/Y');
			}
	
			$dados['filiais'] = $this->globals->lista_filial();			
	
			$this->load->view('header_view', $data);
			$this->load->view('consc_view', $dados);
			$this->load->view('footer_view');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	function carreg_sc() {
		ini_set('max_execution_time', 0);
		$this->load->library('form_validation');
		
		$tipo =  $this->input->post('tipo');
		$contapr =  $this->input->post('contapr');
		$filial =  $this->input->post('filial[]');
		$produto = $this->input->post('produto');
		$servico = $this->input->post('servico');
		$sol = $this->input->post('sol');
		$session_data = $this->session->userdata('newadt');
		$coduser = $session_data['usu_codigo'];
		
		if (!empty($filial)) {
			$filial_list = rtrim(implode(',', $filial), ',');
		} else {
			$filial_list = '';
		}
		
		$codemp = $this->ocs->getEmp($filial_list);
		
		if (!empty($codemp)) {
			$codemp_list = rtrim(implode(',', $codemp), ',');
		} else {
			$codemp_list = '';
		}
		
		$statusapr = $this->scm->getAprSol($codemp_list, $coduser);
		
		if ($statusapr) {
			$checkccu = $this->scm->getCcuSol($statusapr, $coduser);
		} else {
			$checkccu = $this->scm->getCcuSolCom($codemp, $coduser);
			//echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Você não tem permissão de aprovador</div>';
			//exit();
		}
		
		//var_dump($codemp);
		//var_dump($statusapr);
		//var_dump($checkccu);
		
		$result = $this->scm->conbuscasol($tipo, $contapr, $filial_list, $produto, $servico, $coduser, $codemp_list, $statusapr, $checkccu, $sol);
		
		//var_dump($result);
		if($result) {
			$this->CI =& get_instance();
			$this->CI->load->library('table');
				
			$tmpl = array (
					'table_open'          => '<table class="table table-condensed table-hover small">',
					'thead_open'          => '<thead>',
					'thead_close'         => '</thead>',
					'heading_row_start'   => '<tr class="cabecalho">',
					'heading_row_end'     => '</tr>',
					'heading_cell_start'  => '<th class="text-center">',
					'heading_cell_end'    => '</th>',
					'tbody_open'          => '<tbody>',
					'tbody_close'         => '</tbody>',
					'row_start'           => '<tr class="text-center">',
					'row_end'             => '</tr>',
					'cell_start'          => '<td>',
					'cell_end'            => '</td>',
					'row_alt_start'       => '<tr class="text-center">',
					'row_alt_end'         => '</tr>',
					'cell_alt_start'      => '<td>',
					'cell_alt_end'        => '</td>',
					'table_close'         => '</table>'
			);
			$this->CI->table->set_template($tmpl);
			
			$this->CI->table->set_heading('Transação', 'Solicitação', 'Usuário', 'Seq.', 'Produto/Serviço', 'Un.', 'Qtd Solic', 'Qtd Apr', 'Qtd Can', 'Vlr Aprox', 'Apr Solicitante', 'Data', 'Família', 'Cen. Custo', 'Sit Apr', 'Projeto', 'Fase', 'Cta Fin', 'Cta Cont', 'Obs');
			
			$tot_fil = 0;
			$tot_sol = 0;
			$sub_fil_sol = 0;
			$sol_atual = 0;
			$cont = 0;
			
			foreach ($result as $sol) {
				//var_dump($row);
				//foreach ($row as $sol) {
					//var_dump($sol);
					if ($sol) {
						
						if($sol->FILSOL !== $tot_fil) {
							if ($cont > 0) {
								$totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 9);
								$espaço = array('data' => '', 'colspan' => 10);
								$this->table->add_row(
										$totalun,
										'<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_sol), 2, ',', '.').'</strong></p>',
										$espaço
										);
									
								$sub_fil_sol = 0;
							}
						
							$filial = array('data' => '<strong>'.$sol->FILSOL.' - '.$sol->SIGFIL.' '.$sol->USU_INSTAN.'</strong>', 'class' => 'info text-left', 'colspan' => 20);
							$this->table->add_row($filial);
						}
						
						if ($sol->PROSER == 'P') {
							$proser = '<p class="text-left">'.$sol->CODPRO.' - '.$sol->CPLPRO.'</p>';
						} else {
							$proser = '<p class="text-left">'.$sol->CODSER.' - '.$sol->CPLPRO.'</p>';
						}
						
						$this->CI->table->add_row(
								$sol->CODTNS,
								$sol->NUMSOL,
								$sol->CODUSU.' - '.$sol->NOMUSU,
								$sol->SEQSOL,
								'<p class="text-left">'.$sol->CODPRO.' - '.$sol->CPLPRO.'</p>',								
								$sol->UNIMED,
								number_format(str_replace("," , "." , $sol->QTDSOL), 5, ',', '.'),
								number_format(str_replace("," , "." , $sol->QTDAPR), 5, ',', '.'),
								number_format(str_replace("," , "." , $sol->QTDCAN), 5, ',', '.'),								
								number_format(str_replace("," , "." , $sol->PRESOL), 5, ',', '.'),
								$sol->APRSOL,
								$sol->DATSOL,
								$sol->CODFAM,
								'<p class="text-left">'.$sol->CCURES.' - '.$sol->ABRCCU.'</p>',
								$sol->SITAPR,
								$sol->NUMPRJ,
								$sol->CODFPJ,
								'<p class="text-left">'.$sol->CTAFIN.' - '.$sol->ABRCTA.'</p>',
								$sol->CTARED,															
								'<p class="text-left"><a href="#" data-toggle="tooltip" data-placement="left" title="'.$sol->OBSSOL.'">'.substr_replace($sol->OBSSOL, '...', 30).'</a></p>'								
								);
						
						$cont++;
						$sub_fil_sol += str_replace("," , "." , $sol->PRESOL);
						$tot_fil = $sol->FILSOL;
						if($sol->NUMSOL.$sol->SEQSOL !== $sol_atual) {
							//$sub_fil_ped += str_replace("," , "." , $ped->VLRLIQ);
							$tot_sol += str_replace("," , "." , $sol->PRESOL);
						} else {
							//$sub_fil_ped = $sub_fil_ped;
							$tot_sol = $tot_sol;
						}
						$sol_atual = $sol->NUMSOL.$sol->SEQSOL;
						
					} else {
						echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Nenhum resultado foi encontrado!</div>';
					}
					
				//}
			}
			
			$totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 9);
			$espaço = array('data' => '', 'colspan' => 10);
			$this->table->add_row(
					$totalun,
					'<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_sol), 2, ',', '.').'</strong></p>',
					$espaço
					);
			
			$totalge = array('data' => '<strong>Total Geral</strong>', 'class' => 'text-left', 'colspan' => 9);
			//$espaço = array('data' => '', 'colspan' => 10);
			$this->table->add_row(
					$totalge,
					'<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_sol), 2, ',', '.').'</strong></p>',
					$espaço
					);
			
			
			$tabela = array (
					'tabela'	=> $this->CI->table->generate(),
					'user' 	 	=> $coduser
			);
			
			$this->load->view('consctable_view', $tabela);
		}
	}
}