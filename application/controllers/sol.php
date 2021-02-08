<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Sol extends CI_Controller {
	
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
			$this->load->view('sol_view', $dados);
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
		$operacao = $this->input->post('operacao');
		$session_data = $this->session->userdata('newadt');
		$coduser = $session_data['usu_codigo'];
                //$coduser = 4;
	
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
			//$checkccu = $this->scm->getCcuSolCom($codemp, $coduser);
			echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Você não tem permissão de aprovador</div>';
			exit();
		}
	
		//var_dump($codemp);
		//var_dump($statusapr);
		//var_dump($checkccu);
	
		$result = $this->scm->conbusca($tipo, $contapr, $filial_list, $produto, $servico, $coduser, $codemp_list, $statusapr, $checkccu, $sol);
	
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
				
			$this->CI->table->set_heading('<a href="javascript:;" class="btn-sm btn-info" onclick="tudo()" id="selecionarTodos"><i class="fa fa-check-square-o fa-lg"></i></a>', '', 'Transação', 'Solicitação', 'Usuário', 'Seq.', 'Produto/Serviço', 'Un.', 'Qtd Solic', 'Qtd Apr', 'Qtd Can', 'Vlr Aprox', 'Apr Solicitante', 'Data', 'Família', 'Cen. Custo', 'Sit Apr', 'Projeto', 'Fase', 'Cta Fin', 'Cta Cont', 'Obs');
				
			$tot_fil = 0;
			$tot_sol = 0;
			$sub_fil_sol = 0;
			$sol_atual = 0;
			$cont = 0;
				
			foreach ($result as $sol) {
				//($row);
				//foreach ($row as $sol) {
					//var_dump($statusapr[$sol->CODEMP]["CODNAP"]);
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
	
							$filial = array('data' => '<strong>'.$sol->FILSOL.' - '.$sol->SIGFIL.' '.$sol->USU_INSTAN.'</strong>', 'class' => 'info text-left', 'colspan' => 22);
							$this->table->add_row($filial);
						}
	
						if ($sol->PROSER == 'P') {
							$proser = '<p class="text-left">'.$sol->CODPRO.' - '.$sol->CPLPRO.'</p>';
						} else {
							$proser = '<p class="text-left">'.$sol->CODSER.' - '.$sol->CPLPRO.'</p>';
						}
						
						$check = array(
								'name'      => 'id['.$sol->CODEMP.$sol->FILSOL.$sol->NUMSOL.$sol->SEQSOL.'][id]',
								'id'        => 'checkcol',
								'value'     => $sol->CODEMP.'|'.$sol->FILSOL.'|'.$sol->NUMSOL.'|'.$sol->SEQSOL,
								'title'     => $sol->CODEMP.$sol->FILSOL.$sol->NUMSOL.$sol->SEQSOL,
								'rel'       => $sol->PRCSOL
						);
						
						$apr = array(
								'name'      => 'id['.$sol->CODEMP.$sol->FILSOL.$sol->NUMSOL.$sol->SEQSOL.'][apr]',
								'id'        => 'apr'.$sol->CODEMP.$sol->FILSOL.$sol->NUMSOL.$sol->SEQSOL,
								'disabled'  => 'disabled',
								'type'      => 'hidden',
								'class'     => 'campo',
								'value'     => $sol->NUMAPR,
								'title'     => $sol->NUMAPR
						);
						
						$nap = array(
								'name'      => 'id['.$sol->CODEMP.$sol->FILSOL.$sol->NUMSOL.$sol->SEQSOL.'][nap]',
								'id'        => 'nap'.$sol->CODEMP.$sol->FILSOL.$sol->NUMSOL.$sol->SEQSOL,
								'type'      => 'hidden',
								'disabled'  => 'disabled',
								'class'     => 'campo',
								'value'     => $statusapr[$sol->CODEMP]["CODNAP"],
								'title'     => $statusapr[$sol->CODEMP]["CODNAP"]
						);
						
						$niv = array(
								'name'      => 'id['.$sol->CODEMP.$sol->FILSOL.$sol->NUMSOL.$sol->SEQSOL.'][nivel]',
								'id'        => 'niv'.$sol->CODEMP.$sol->FILSOL.$sol->NUMSOL.$sol->SEQSOL,
								'type'      => 'hidden',
								'disabled'  => 'disabled',
								'class'     => 'campo',
								'value'     => $sol->NIVEXI,
								'title'     => $sol->NIVEXI
						);
	
						$this->CI->table->add_row(
								form_checkbox($check).form_input($nap).form_input($niv).form_input($apr),
								'<a href="javascript:;" onclick="jVePendente('.$sol->CODEMP.','.$sol->NUMAPR.',6,'.$sol->NUMSOL.','.$sol->FILSOL.','.$sol->SEQSOL.')"><i class="fa fa-exclamation-triangle fa-2x"></i></a>',
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
			$espaço = array('data' => '', 'colspan' => 12);
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
					'user' 	 	=> $coduser,
					'operacao'	=> $operacao
			);
				
			$this->load->view('soltable_view', $tabela);
		}
	}
	
	function busca_pendente($emp, $numapr, $rotnap, $numsol, $codfil, $seqsol) {
		//list($pedido, $filial) = explode('-' , $codigo);
		$dados['result'] = $this->scm->getPendente($emp, $numapr, $rotnap, $numsol, $codfil, $seqsol);
		$this->load->view('pendente_view', $dados);
	}
	
	public function insereapr($emp, $numapr, $coduser, $codnap, $seqapr) {
	
		$insere_apr = array(
				intval($emp),
				0,
				6,
				intval($numapr),
				intval($seqapr),
				intval($coduser),
				'',
				intval($codnap),
				'',
				0,
				'APR',
				0,
				'',
				intval($seqapr),
				0,
				'',
				0
		);
	
		//var_dump($insere_apr);
		//exit();
		$sql_usu = "INSERT INTO E614USU VALUES (?,?,?,?,?,?,TO_CHAR(SYSDATE,'DD/MM/YYYY'),((TO_CHAR(SYSDATE,'hh24')*60)+TO_CHAR(SYSDATE,'mi')),?,?,?,?,?,?,?,?,?,?,?)";
			
		$result_usu = $this->ocs->crud($sql_usu, $insere_apr);
	
		if ($result_usu) {
			return  true;
		} else {
			return false;
		}
	
	}
	
	public function atualizaapr($numapr, $nivel, $emp) {
	
		$update_apr = array(
				'ANA',
				intval($numapr),
				$nivel,
				intval($emp)
		);
		//var_dump($update_apr);
		$sql_apr = "UPDATE E614APR SET SITAPR = 'APR' WHERE SITAPR = ? AND NUMAPR = ? AND NIVEXI = ? AND CODEMP = ?";
	
		$result_apr = $this->ocs->crud($sql_apr, $update_apr);	 	
		if ($result_apr) {
			return  true;			
		} else {
			return false;
		//	return $result_apr;
		}
	
	}
	
	
	public function aprsol($emp, $fil, $oc, $seq) {
					
		// atualiza a tabela da oc para realizar o fechamento
		$data_oc = array(
				'APR',
				$emp,
				$fil,
				$oc,
				$seq
		);
	
		$sql_oc = "UPDATE E405SOL SET SITAPR = ? WHERE CODEMP = ? AND FILSOL = ? AND NUMSOL = ? AND SEQSOL = ?";
	
		$result_oc = $this->ocs->crud($sql_oc, $data_oc);
			
		if ($result_oc) {
			return  true;
		} else {
			return false;
		}
	
	}
	
	function aprovar() {
		$id = array_filter($this->input->post('id', TRUE));
		//$prorroga = array_filter($this->input->post('pro[]'));
		$session_data = $this->session->userdata('newadt');
		$coduser = $session_data['usu_codigo'];
		//var_dump($id);
		//exit();
		//var_dump($coduser);
		
		$erro = 0;
		$sucess = 0;
		$msg_erro = '';
		$msg_sucess = '';		
		
		if ($id) {
			foreach ($id as $key => $row) {
				list($emp, $fil, $oc, $seq) = explode('|',$row['id']);
				
				//var_dump($key);
				//var_dump($row);				
				$codnap = $row['nap'];
				$numapr = $row['apr'];
				$nivel 	= $row['nivel'];
				
				if ($codnap == 30) {
						
					if ($nivel == '10+20+30+40') {
                                            $seqapr = 3;
					} elseif ($nivel == '20+30') {
                                            $seqapr = 2;
                                        } else {
                                            $seqapr = 1;
					}
					
					$sqlinsert = $this->insereapr($emp, $numapr, $coduser, $codnap, $seqapr);
					//var_dump($sqlinsert);
					//exit();
					if ($sqlinsert == true) {
						if ($nivel == '10+20+30+40' || $nivel == '30' || $nivel == '30+40' || $nivel == '20+30') {
							$sql_altera = $this->atualizaapr($numapr, $nivel, $emp);
							//var_dump($sql_altera);
							//exit();
							if ($sql_altera == true) {
								$fecha = $this->aprsol($emp, $fil, $oc, $seq);
								//var_dump($fecha);
								if($fecha == true) {
									$sucess += 1;
									$msg_sucess .= '<div class="alert alert-success small">Solicitação: '.$oc.' Sequência: '.$seq.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
								}
							} else {
								$erro += 1;
								$msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar Solicitação: '.$oc.' Sequência: '.$seq.' da Filial: '.$fil.'</div>';
							}
						} else {
							$erro += 1;
							$msg_erro .= '<div class="alert alert-danger small">Erro no Nivel de Aprovação: '.$nivel.' Solicitação: '.$oc.' Sequência: '.$seq.' da Filial: '.$fil.'</div>';
							$del_apr = array(
									intval($emp),
									6,
									intval($numapr)									
									
							);
							//var_dump($update_apr);
							$sql_del = "DELETE E614USU where codemp = ? and rotnap = ? and numapr = ?";
							
							$this->ocs->crud($sql_del, $del_apr);
						}
					} else {
						$erro += 1;
						$msg_erro .= '<div class="alert alert-danger small">Erro ao inserir aprovação da Solicitação: '.$oc.' Sequência: '.$seq.' da Filial: '.$fil.'</div>';
					}
						
				} elseif ($codnap == 20) {
                                    if ($nivel == '10+20+30+40') {
                                        $seqapr = 2;
                                    } elseif ($nivel == '20+30') {
                                        $seqapr = 1;
                                    } else {
                                        $seqapr = 1;
                                    }
                                    
                                    $sqlinsert = $this->insereapr($emp, $numapr, $coduser, $codnap, $seqapr);
                                    
                                    if ($sqlinsert == true) {
                                        if ($nivel == '10+20+30+40' || $nivel == '20+30') {
                                            $sql_altera = $this->atualizaapr($numapr, $nivel, $emp);
                                            //var_dump($sql_altera);
                                            //exit();
                                            if ($sql_altera == true) {
                                                    $fecha = $this->aprsol($emp, $fil, $oc, $seq);
                                                    //var_dump($fecha);
                                                    if($fecha == true) {
                                                            $sucess += 1;
                                                            $msg_sucess .= '<div class="alert alert-success small">Solicitação: '.$oc.' Sequência: '.$seq.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
                                                    }
                                            } else {
                                                    $erro += 1;
                                                    $msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar Solicitação: '.$oc.' Sequência: '.$seq.' da Filial: '.$fil.'</div>';
                                            }
                                        } else {
                                            $erro += 1;
                                            $msg_erro .= '<div class="alert alert-danger small">Erro no Nivel de Aprovação: '.$nivel.' Solicitação: '.$oc.' Sequência: '.$seq.' da Filial: '.$fil.'</div>';
                                            $del_apr = array(
                                                            intval($emp),
                                                            6,
                                                            intval($numapr)									

                                            );
                                            //var_dump($update_apr);
                                            $sql_del = "DELETE E614USU where codemp = ? and rotnap = ? and numapr = ?";

                                            $this->ocs->crud($sql_del, $del_apr);
                                        }
                                    }
                                }
			}
		}
		
		if ($erro > 0) {
			echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
			echo $msg_erro.'<br>';
		}
		echo '<legend>Foram processados '.$sucess.' soicitações(s)!</legend>';
		if ($sucess > 0) {
			echo $msg_sucess;
		}
		
	}
}