<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Conoc extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('globals','',TRUE);
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
			$dados['fornec'] = $this->globals->lista_fornec();
                        $dados['users'] = $this->globals->lista_users();
	
			$this->load->view('header_view', $data);
			$this->load->view('conoc_view', $dados);
			$this->load->view('footer_view');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	
	function carreg_oc() {
                ini_set('max_execution_time', 0);
		$this->load->library('form_validation');
	
		$dtini =  $this->input->post('dtini');
		$dtfim =  $this->input->post('dtfim');		
		$contapr =  $this->input->post('contapr');
		$filial =  $this->input->post('filial[]');
		$pedido = $this->input->post('pedido');
                $situacao = $this->input->post('situacao');
                $aprovador = $this->input->post('aprovador');
		$codfor = $this->input->post('fornecedor[]');
		$session_data = $this->session->userdata('newadt');
		$coduser = $session_data['usu_codigo'];
	
	
		if (!empty($filial)) {
			$filial_list = rtrim(implode(',', $filial), ',');
		} else {
			$filial_list = '';
		}
			
		if (!empty($codfor)) {
			$codfor_list = rtrim(implode("','", $codfor), ',');
		} else {
			$codfor_list = '';
		}
                                	
		$codemp = $this->ocs->getEmp($filial_list);
	
		//var_dump($codemp);
					
		$result = $this->ocs->buscacon($dtini, $dtfim, $contapr, $filial_list, $pedido, $codfor_list, $codemp, $situacao, $aprovador);
                                
                 
		//var_dump($result);
		//exit();
	
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
				
			$this->CI->table->set_heading('OC', 'Data', 'Status', 'Fornecedor', 'Valor', 'Tipo Pgto', 'Obs', 'Comprador', 'Vencimento', 'Aprovações', 'Pendentes');
				
			$tot_fil = 0;
			$tot_ped = 0;
			$sub_fil_ped = 0;
			$ped_atual = 0;
			$cont = 0;
                        $pendente = '';
			//var_dump($statusapr);
			//echo $statusapr[0]->CODNAP;
			foreach ($result as $row) {
				//echo $id;
				//var_dump($row);
				foreach ($row as $ped) {
					//var_dump($ped);
					if ($ped) {
						//var_dump($row);
						//echo $row[$i]->CODFIL;
						if($ped->CODFIL !== $tot_fil) {
							if ($cont > 0) {
								$totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 4);
								$espaço = array('data' => '', 'colspan' => 7);
								$this->table->add_row(
										$totalun,
										'<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.').'</strong></p>',
										$espaço
										);
									
								$sub_fil_ped = 0;
							}
								
							$filial = array('data' => '<strong>'.$ped->CODFIL.' - '.$ped->SIGFIL.' '.$ped->USU_INSTAN.'</strong>', 'class' => 'info text-left', 'colspan' => 16);
							$this->table->add_row($filial);
						}
						
						$dtatual = date('d-m-Y');
						list($diaat, $mesat, $anoat) = explode('-', $dtatual);
						$timeat = mktime(0, 0, 0, $mesat, $diaat + 2, $anoat);
						$dtatual = strftime('%Y-%m-%d', $timeat);
						
						if ($ped->TEMPAR == 'S') {
							if ($ped->VCTPAR != '31/12/1900') {
								$dtvenc = $ped->VCTPAR;
								//echo $ped->VCTPAR;
								list($pdia, $pmes, $pano) = array_pad(explode('/', $ped->VCTPAR), 3, null);
								$venc = $pano.'-'.$pmes.'-'.$pdia;
								//echo $venc;
								if (strtotime($dtatual) > strtotime($venc)) {
									$venclass = '<span class="label label-danger">'.$dtvenc.'</span>';
																			
								} else {
									$venclass = $dtvenc;									
								}
							} else {
								list($dia, $mes, $ano) = explode('/', $ped->DATEMI);
								$time = mktime(0, 0, 0, $mes, $dia + $ped->DIAPAR, $ano);
						
								$dtvenc = strftime('%d/%m/%Y', $time);
								$venc = strftime('%Y-%m-%d', $time);
									
								if (strtotime($dtatual) > strtotime($venc)) {
									$venclass = '<p class="label label-danger">'.$dtvenc.'</p>';
																			
								} else {
									$venclass = $dtvenc;									
								}
							}
						} elseif ($ped->TEMPAR == 'N') {
							list($dia, $mes, $ano) = explode('/', $ped->DATEMI);
							$time = mktime(0, 0, 0, $mes, $dia + $ped->DIAPAR, $ano);
							$dtvenc = strftime('%d/%m/%Y', $time);
							$venc = strftime('%Y-%m-%d', $time);
						
							if (strtotime($dtatual) > strtotime($venc)) {
								$venclass = '<p class="label label-danger">'.$dtvenc.'</p>';
						
								$timepro = mktime(0, 0, 0, $mesat, $diaat + $ped->DIAPAR, $anoat);
								$dtpro = strftime('%d/%m/%Y', $timepro);
								$prorroga = array(
										'name' 	=> 'id['.$ped->CODEMP.$ped->CODFIL.$ped->NUMOCP.'][dtpro]',
										'id'   	=> 'dtpro'.$ped->CODEMP.$ped->CODFIL.$ped->NUMOCP,
										'type'	=> 'text',
										'class' => 'dtprorroga form-control input-sm',
										'disabled' => 'disabled',
										'value' => 	$dtpro
								);
						
								$input = form_input($prorroga);
							} else {
								$venclass = $dtvenc;
								$input = null;
							}
						
						}
                                                
                                                if ($ped->SITOCP == 1) {
                                                    $status = 'Aberto Normal';
                                                    $pendente = '';
                                                } elseif ($ped->SITOCP == 2) {
                                                    $status = 'Aberto Parcial';                                                
                                                    $pendente = '';
                                                } elseif ($ped->SITOCP == 4) {
                                                    $status = 'Liquidado';                                                
                                                    $pendente = '';
                                                } elseif ($ped->SITOCP == 5) {
                                                    $status = 'Cancelado';                                                
                                                    $pendente = '';
                                                } elseif ($ped->SITOCP == 9) {
                                                    $status = 'Não Fechado';                                                
                                                    $pendente = '<a href="javascript:;" onclick="jVePendente('.$ped->CODEMP.','.$ped->NUMAPR.',12,'.$ped->NUMOCP.','.$ped->CODFIL.')"><i class="fa fa-exclamation-triangle fa-2x"></i></a>';
                                                }
																														
						$this->CI->table->add_row(								
								'<a href="javascript:;" onclick="jVeItem('.$ped->NUMOCP.','.$ped->CODFIL.')">'.$ped->NUMOCP.'</a>',
								$ped->DATEMI,
                                                                $status,
								'<p class="text-left">'.$ped->CODFOR.' - '.$ped->APEFOR.'</p>',
								'<p class="text-right">'.number_format(str_replace("," , "." , $ped->VLRLIQ), 2, ',', '.').'</p>',
								$ped->CODCPG.' - '.$ped->DESCPG,
								'<p class="text-left"><a href="#" data-toggle="tooltip" data-placement="left" title="'.$ped->OBSOCP.'">'.substr_replace($ped->OBSOCP, '...', 30).'</a></p>',
								$ped->CODUSU.'-'.$ped->NOMUSU,
								$venclass,
								'<a href="javascript:;" onclick="jVeAprovacao('.$ped->CODEMP.','.$ped->NUMAPR.',12)"><i class="fas fa-clipboard-check fa-2x"></i></a>',
								$pendente
								);
	
						$cont++;
						$sub_fil_ped += str_replace("," , "." , $ped->VLRLIQ);
						$tot_fil = $ped->CODFIL;
						if($ped->NUMOCP !== $ped_atual) {
							//$sub_fil_ped += str_replace("," , "." , $ped->VLRLIQ);
							$tot_ped += str_replace("," , "." , $ped->VLRLIQ);
						} else {
							//$sub_fil_ped = $sub_fil_ped;
							$tot_ped = $tot_ped;
						}
						$ped_atual = $ped->NUMOCP;
	
					} else {
						echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Nenhum resultado foi encontrado!</div>';
					}
						
	
				}
					
	
			}
			$totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 4);
			$espaço = array('data' => '', 'colspan' => 7);
			$this->table->add_row(
					$totalun,
					'<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.').'</strong></p>',
					$espaço
					);
				
			$totalge = array('data' => '<strong>Total Geral</strong>', 'class' => 'text-left', 'colspan' => 4);
			$espaço = array('data' => '', 'colspan' => 7);
			$this->table->add_row(
					$totalge,
					'<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_ped), 2, ',', '.').'</strong></p>',
					$espaço
					);
				
				
			$tabela = array (
					'tabela'	=> $this->CI->table->generate(),
					'user' 	 	=> $coduser					
			);
			                        
		}
                
                $resccu = $this->ocs->resumo_ccu($dtini, $dtfim, $filial_list);                
                        
                if($resccu) {
                    foreach ($resccu as $rcc) {
                        $dadosccu['result'][] = array(
                            "FILIAL"    => $rcc->CODFIL.' - '.$rcc->USU_INSTAN,
                            "AREA"      => $rcc->ABRCCU,
                            "REA"       => $rcc->VLRREAL,
                            "ORC"       => $rcc->VLRORC
                        );
                    }			

                    //echo json_encode($dadosccu);
                    //$dadosccu['result'] = $dadosccu;
                    $this->load->view('ccu_view', $dadosccu);
                }
                
                $rescta = $this->ocs->resumo_cta($dtini, $dtfim, $filial_list);
                
                if($rescta) {
                    foreach ($rescta as $rcta) {
                        $dadoscta['result'][] = array(
                            "FILIAL"    => $rcta->CODFIL.' - '.$rcta->USU_INSTAN,
                            "AREA"      => $rcta->ABRCTA,
                            "REA"       => $rcta->VLRREAL,
                            "ORC"       => $rcta->VLRORC
                        );
                    }			

                    //echo json_encode($dadosccu);
                    //$dadosccu['result'] = $dadosccu;
                    $this->load->view('cta_view', $dadoscta);
                } 
                        
                        
                //$this->load->view('cta_view');
                $this->load->view('conoctable_view', $tabela);
	
	}

}
