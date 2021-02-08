<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//we need to call PHP's session object to access it through CI
class Oc extends CI_Controller {

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

			$this->load->view('header_view', $data);
			$this->load->view('oc_view', $dados);
			$this->load->view('footer_view');
		} else {
			//If no session, redirect to login page
			redirect('login', 'refresh');
		}
	}
	
	function carreg_oc() {
		ini_set('max_execution_time', 0);
		$this->load->library('form_validation');
		
		$dtini          =  $this->input->post('dtini');
		$dtfim          =  $this->input->post('dtfim');
		$operacao       =  $this->input->post('operacao');
		$contapr        =  $this->input->post('contapr');
		$filial         =  $this->input->post('filial[]');		
		$pedido         = $this->input->post('pedido');		
		$codfor         = $this->input->post('fornecedor[]');
		$session_data   = $this->session->userdata('newadt');
		//$coduser = $session_data['usu_codigo'];
		$coduser        = 556;
				
		
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
		
		if (!empty($codemp)) {
			$codemp_list = rtrim(implode(',', $codemp), ',');
		} else {
			$codemp_list = '';
		}
		
		$statusapr = $this->ocs->getApr($codemp_list, $coduser);
		//var_dump($statusapr);
		
		if ($statusapr) {
			$checkccu = $this->ocs->getCcu($statusapr, $coduser);
		} else {
			echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Você não tem permissão de aprovador</div>';
			exit();
		}
		//var_dump($coduser);
		//var_dump($statusapr);
		//var_dump($checkccu);
		//exit();
		//var_dump($filial);
		//var_dump($codemp_list);
		//var_dump($dtini);
		//var_dump($dtfim);
		//var_dump($operacao);
		//var_dump($contapr);
		//var_dump($filial_list);
		//var_dump($pedido);
		//var_dump($codfor_list);
		//var_dump($coduser);
				
		$result = $this->ocs->busca($dtini, $dtfim, $contapr, $filial_list, $pedido, $codfor_list, $coduser, $codemp_list, $statusapr, $checkccu, $operacao);
		
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
			
			$this->CI->table->set_heading('<a href="javascript:;" class="btn-sm btn-info" onclick="tudo()" id="selecionarTodos"><i class="fa fa-check-square-o fa-lg"></i></a>','OC', 'Data', 'Fornecedor', 'Valor', 'Tipo Pgto', 'Obs', 'Comprador', 'Vencimento', 'Prorrogação','Negociação', 'Aprovações', 'Pendentes');
			
			$tot_fil = 0;
			$tot_ped = 0;
			$sub_fil_ped = 0;
			$ped_atual = 0;
			$cont = 0;
			//$tam = count($statusapr);
			//var_dump($statusapr);			
			//echo $statusapr[$codemp-1]->CODNAP;
                        
                        foreach ($result as $row) {

                            if ($row->SEQOBS == null) {
                                $seqobs = 1;                                
                            } else {
                                $seqobs = $row->SEQOBS;
                            }
                            //var_dump($row);
                            if ($row->CODFIL !== $tot_fil) {
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
                                $filial = array('data' => '<strong>'.$row->CODFIL.' - '.$row->SIGFIL.' '.$row->USU_INSTAN.'</strong>', 'class' => 'info text-left', 'colspan' => 16);
                                $this->table->add_row($filial);
                            }
                            
                            $check = array(
                                'name'  => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][id]',
                                'id'    => 'checkcol',
                                'value' => $row->CODEMP.'|'.$row->CODFIL.'|'.$row->NUMOCP,
                                'title' => $row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                'rel'	=> $row->VLRLIQ
                            );
						
                            $apr = array(
                                'name'      => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][apr]',
                                'id'        => 'apr'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                'disabled'  => 'disabled',
                                'type'      => 'hidden',
                                'class'     => 'campo',
                                'value'     => $row->NUMAPR,
                                'title'     => $row->NUMAPR							
                            );
						
                            $dtemi = array(
                                'name'      => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][dtemi]',
                                'id'        => 'dtemi'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                'type'      => 'hidden',
                                'disabled'  => 'disabled',
                                'class'     => 'campo',
                                'value'     => $row->DATEMI,
                                'title'     => $row->DATEMI							
                            );
												
						
                            $nap = array(
                                'name'      => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][nap]',
                                'id'        => 'nap'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                'type'      => 'hidden',
                                'disabled'  => 'disabled',
                                'class'     => 'campo',
                                'value'     => $row->CODNAPAPR,
                                'title'     => $row->CODNAPAPR
                            );
						
                            $niv = array(
                                'name'      => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][nivel]',
                                'id'        => 'niv'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                'type'      => 'hidden',
                                'disabled'  => 'disabled',
                                'class'     => 'campo',
                                'value'     => $row->NIVEXI,
                                'title'     => $row->NIVEXI
                            );
						
                            $par = array(
                                'name'      => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][par]',
                                'id'        => 'par'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                'type'      => 'hidden',
                                'disabled'  => 'disabled',
                                'class'     => 'campo',
                                'value'     => $row->TEMPAR,
                                'title'     => $row->TEMPAR
                            );
						
                            $vlrpar = array(
                                'name'      => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][vlrpar]',
                                'id'        => 'vlpar'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                'type'      => 'hidden',
                                'disabled'  => 'disabled',
                                'class'     => 'campo',
                                'value'     => $row->VLRPAR,
                                'title'     => $row->VLRPAR
                            );
                                                
                            $seqobs = array(
                                'name'      => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][seqobs]',
                                'id'        => 'seqobs'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                'type'      => 'hidden',
                                'disabled'  => 'disabled',
                                'class'     => 'campo',
                                'value'     => $seqobs,
                                'title'     => $seqobs
                            );
                            
                            $dtatual = date('d-m-Y');
                            list($diaat, $mesat, $anoat) = explode('-', $dtatual);
                            $timeat = mktime(0, 0, 0, $mesat, $diaat + 3, $anoat);
                            $dtatual = strftime('%Y-%m-%d', $timeat);
                            //echo $dtatual;
                            if ($row->TEMPAR == 'S') {
                                $tempar = 1;
                                if ($row->VCTPAR != '31/12/1900') {
                                    $dtvenc = $row->VCTPAR;
                                    //echo $ped->VCTPAR;
                                    list($pdia, $pmes, $pano) = array_pad(explode('/', $row->VCTPAR), 3, null);
                                    $venc = $pano.'-'.$pmes.'-'.$pdia;
                                    //echo $venc;
                                    if (strtotime($dtatual) >= strtotime($venc)) {
                                        $venclass = '<span class="label label-danger">'.$dtvenc.'</span>';
                                        //var_dump($diaat);
                                        //var_dump($row->DIAPAR);
                                        $timepro = mktime(0, 0, 0, $mesat, $diaat + $row->DIAPAR, $anoat);
                                        $dtpro = strftime('%d/%m/%Y', $timepro);
                                        $prorroga = array(
                                            'name' 	=> 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][dtpro]',
                                            'id'   	=> 'dtpro'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                            'type'	=> 'text',
                                            'disabled'  => 'disabled',
                                            'class'     => 'dtprorroga form-control input-sm',
                                            'value'     => $dtpro										
                                        );
                                        //var_dump($timepro);
                                        //echo $dtpro;
                                        $input = form_input($prorroga);
                                    } else {
                                        $venclass = $dtvenc;
                                        $input = null;
                                    }
                                                                                                            
                                } else {
                                    list($dia, $mes, $ano) = explode('/', $row->DATEMI);
                                    $time = mktime(0, 0, 0, $mes, $dia + $row->DIAPAR, $ano);

                                    $dtvenc = strftime('%d/%m/%Y', $time);
                                    $venc = strftime('%Y-%m-%d', $time);

                                    if (strtotime($dtatual) > strtotime($venc)) {
                                        $venclass = '<p class="label label-danger">'.$dtvenc.'</p>';

                                        $timepro = mktime(0, 0, 0, $mesat, $diaat + $row->DIAPAR, $anoat);
                                        $dtpro = strftime('%d/%m/%Y', $timepro);
                                        $prorroga = array(
                                            'name' 	=> 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][dtpro]',
                                            'id'   	=> 'dtpro'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                            'type'	=> 'text',
                                            'disabled'  => 'disabled',
                                            'class'     => 'dtprorroga form-control input-sm',
                                            'value'     => $dtpro
                                        );

                                        $input = form_input($prorroga);
                                    } else {
                                        $venclass = $dtvenc;
                                        $input = null;
                                    }
                                }                               
                            } elseif ($row->TEMPAR == 'N') {
                                $tempar = 0;
                                list($dia, $mes, $ano) = explode('/', $row->DATEMI);
                                $time = mktime(0, 0, 0, $mes, $dia + $row->DIAPAR, $ano);
                                $dtvenc = strftime('%d/%m/%Y', $time);
                                $venc = strftime('%Y-%m-%d', $time);

                                if (strtotime($dtatual) > strtotime($venc)) {
                                    $venclass = '<p class="label label-danger">'.$dtvenc.'</p>';

                                    $timepro = mktime(0, 0, 0, $mesat, $diaat + $row->DIAPAR, $anoat);
                                    $dtpro = strftime('%d/%m/%Y', $timepro);
                                    $prorroga = array(
                                        'name'      => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.'][dtpro]',
                                        'id'        => 'dtpro'.$row->CODEMP.$row->CODFIL.$row->NUMOCP,
                                        'type'      => 'text',
                                        'class'     => 'dtprorroga form-control input-sm',
                                        'disabled'  => 'disabled',
                                        'value'     => 	$dtpro
                                    );

                                    $input = form_input($prorroga);
                                } else {
                                    $venclass = $dtvenc;
                                    $input = null;
                                }

                            }
                            
                            if ($row->NUMPCT <> 0) {
                                $numpct = '<a href="http://grupofarias.comlink.com.br/www/#/negociacao/'.$row->NUMPCT.'" target="blanck" ><i class="far fa-handshake fa-2x"></i></a>';
                                //$numpct = '<a href="javascript:;" onclick="jVeNegocia('.$row->CODEMP.','.$row->NUMOCP.')"><i class="far fa-handshake fa-2x"></i></a>'.$row->NUMPCT;
                            } else {
                                $numpct = '';
                            }
                            
                            $this->CI->table->add_row(
                                form_checkbox($check).form_input($dtemi).form_input($nap).form_input($niv).form_input($par).form_input($vlrpar).form_input($apr).form_input($seqobs),
                                '<a href="javascript:;" onclick="jVeItem('.$row->NUMOCP.','.$row->CODFIL.','.$row->CODNAPAPR.','.$tempar.')">'.$row->NUMOCP.'</a>',
                                $row->DATEMI,
                                '<p class="text-left">'.$row->CODFOR.' - '.$row->APEFOR.'</p>',
                                '<p class="text-right">'.number_format(str_replace("," , "." , $row->VLRLIQ), 2, ',', '.').'</p>',
                                $row->CODCPG.' - '.$row->DESCPG,
                                '<p class="text-left"><a href="#" data-toggle="tooltip" data-placement="left" title="'.$row->OBSOCP.'">'.substr_replace($row->OBSOCP, '...', 30).'</a></p>',
                                $row->CODUSU.'-'.$row->NOMUSU,
                                $venclass,
                                $input,
                                $numpct,                                                                
                                '<a href="javascript:;" onclick="jVeAprovacao('.$row->CODEMP.','.$row->NUMAPR.',12)"><i class="fas fa-clipboard-check fa-2x"></i></a>',
                                '<a href="javascript:;" onclick="jVePendente('.$row->CODEMP.','.$row->NUMAPR.',12,'.$row->NUMOCP.','.$row->CODFIL.')"><i class="fa fa-exclamation-triangle fa-2x"></i></a>'
                            );
                            
                            $cont++;
                            $sub_fil_ped += str_replace("," , "." , $row->VLRLIQ);
                            $tot_fil = $row->CODFIL;
                            if($row->NUMOCP !== $ped_atual) {
                                    //$sub_fil_ped += str_replace("," , "." , $ped->VLRLIQ);
                                    $tot_ped += str_replace("," , "." , $row->VLRLIQ);
                            } else {
                                    //$sub_fil_ped = $sub_fil_ped;
                                    $tot_ped = $tot_ped;
                            }
                            $ped_atual = $row->NUMOCP;
                            
                        }
                        
                        $totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 4);
			$espaço = array('data' => '', 'colspan' => 7);
			$this->table->add_row(
                            $totalun,
                            '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.').'</strong></p>',
                            $espaço
                        );
			
			$totalge = array('data' => '<strong>Total Geral</strong>', 'class' => 'text-left', 'colspan' => 4);
			//$espaço = array('data' => '', 'colspan' => 7);
			$this->table->add_row(
                            $totalge,
                            '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_ped), 2, ',', '.').'</strong></p>',					
                            $espaço
			);
                        
                        $tabela = array (
                            'tabela'	=> $this->CI->table->generate(),
                            'user' 	=> $coduser,
                            'operacao' 	=> $operacao
                        );
	     	
                        $this->load->view('octable_view', $tabela);
			//exit();
                } else {
                    echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Nenhum resultado foi encontrado!</div>';
                }                        																				
	}
	
	function busca_peditem($pedido, $filial) {
		//list($pedido, $filial) = explode('-' , $codigo);
		$dados['result'] = $this->ocs->getItemPed($pedido, $filial);
		$this->load->view('itens_view', $dados);
	}
        
        function busca_peditemalt($pedido, $filial, $codnap, $tempar) {
		//list($pedido, $filial) = explode('-' , $codigo);
		$dados['result'] = $this->ocs->getItemPed($pedido, $filial);
                $dados['codnap'] = $codnap;
                $dados['tempar'] = $tempar;
		$this->load->view('itens_view_alt', $dados);
	}
	
	function busca_aprovador($emp, $numapr, $rotnap) {
		//list($pedido, $filial) = explode('-' , $codigo);
		$dados['result'] = $this->ocs->getAprovador($emp, $numapr, $rotnap);		
		$this->load->view('aprovador_view', $dados);
	}
        
        function busca_negocia($emp, $oc) {
		//list($pedido, $filial) = explode('-' , $codigo);
		$result = $this->ocs->getNegocia($emp, $oc);
                if($result) {
			foreach ($result as $row) {
				$dados['result'][] = array(
                                    "Item"          => $row->ITEM.' - '.$row->DESCRICAO,
                                    "Fornecedor"    => $row->CODFOR.' - '.$row->APEFOR,
                                    "Qtde"          => $row->QTDCOT,
                                    "PRECOT"        => $row->PRECOT
				);
			}			
			//echo json_encode($data);
		} else {
			return false;
		}
		$this->load->view('negocia_view', $dados);
	}
	
	function busca_pendente($emp, $numapr, $rotnap, $numocp, $codfil) {
		//list($pedido, $filial) = explode('-' , $codigo);
		$dados['result'] = $this->ocs->getPendente($emp, $numapr, $rotnap, $numocp, $codfil);
		$this->load->view('pendente_view', $dados);
	}
	
	public function insereapr($emp, $numapr, $coduser, $codnap, $seqapr) {
		
		$insere_apr = array(
				intval($emp),
				0,
				12,
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
        
        public function deleteapr($emp, $numapr) {
            
            $deleteapr = array(
                $emp,
                12,
                $numapr
            );
            
            $sql_del = "DELETE FROM E614USU WHERE CODEMP = ? AND ROTNAP = ? AND NUMAPR = ?";
            
            $result_del = $this->ocs->crud($sql_del, $deleteapr);
		
		if ($result_del) {			
			return  true; 
		} else {
			return false;
		}
        }

        public function insereobs($emp, $fil, $oc, $seq, $obs, $codusu) {
            $insere_obs = array(
                $emp,
                $fil,
                $oc,
                $seq,
                'A',
                13,
                $obs,
                $codusu,
                '',
                0,
                '',
                0,
                'R'
            );
            
            $sql_obs = "INSERT INTO E420OBS VALUES (?,?,?,?,?,?,?,?,TO_CHAR(SYSDATE,'DD/MM/YYYY'),((TO_CHAR(SYSDATE,'hh24')*60)+TO_CHAR(SYSDATE,'mi')),?,?,?,?,?)";
            
            $result_obs = $this->ocs->crud($sql_obs, $insere_obs);
		
            if ($result_obs) {			
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
            }
		
	}
        
        public function cancelaoc($emp, $fil, $oc) {

            $update_oc = array(                            
                            $emp,
                            $fil,
                            $oc
            );
            //var_dump($update_apr); 
            $sql_oc = "UPDATE E420OCP SET SITOCP = 5 WHERE CODEMP = ? AND CODFIL = ? AND NUMOCP = ?";

            $result_oc = $this->ocs->crud($sql_oc, $update_oc);

            if ($result_oc) {
                    return  true;
            } else {
                    return false;
            }
		
	}
	
	public function fechaoc($emp, $fil, $oc) {
		
		$checkoc = $this->ocs->checkoc($emp, $fil, $oc);
		
		if ($checkoc) {
			
			$data_up = array(
					'A',
					$emp,
					$fil,
					$oc
			);
				
			$sql_up = "UPDATE USU_TOCPFEC SET USU_TSITOCP = ? WHERE USU_TCOEMP = ? AND USU_TCODFIL = ? AND USU_TNUMOCP = ?";
			
			$result_fec = $this->ocs->crud($sql_up, $data_up);
			
		} else {
		// insere na tabela para execuçao do processo automatico
			$data_fec = array(
					$emp,
					$fil,
					$oc,
					'A'
			);
			
			$sql_fec = "INSERT INTO USU_TOCPFEC VALUES (?, ?, ?, ?)";
			
			$result_fec = $this->ocs->crud($sql_fec, $data_fec);
			
			// atualiza a tabela da oc para realizar o fechamento			
			
		}
		
		$data_oc = array(
				'APR',
				$emp,
				$fil,
				$oc
		);
			
		$sql_oc = "UPDATE E420OCP SET SITAPR = ? WHERE CODEMP = ? AND CODFIL = ? AND NUMOCP = ?";
		
		$result_oc = $this->ocs->crud($sql_oc, $data_oc);
					
		if ($result_fec and $result_oc) {
			return  true;
		} else {
			return false;
		}		
		
	}
        
        function recalcular() {
            $id = array_filter($this->input->post('id', TRUE));
            var_dump($id);
        }
                
	
	function aprovar() {
		$id = array_filter($this->input->post('id', TRUE));
		//$prorroga = array_filter($this->input->post('pro[]'));
		$session_data = $this->session->userdata('newadt');
		//$coduser = $session_data['usu_codigo'];
		$coduser = 556;
		//var_dump($id);		
		//var_dump($coduser);
                //exit();
		$erro = 0;
		$sucess = 0;
		$msg_erro = '';
		$msg_sucess = '';
		$continua = 1;
		
		if ($id) {
			foreach ($id as $key => $row) {
				list($emp, $fil, $oc) = explode('|',$row['id']);
				//var_dump($key);
				//var_dump($row);
				//exit();
				$tempar = $row['par'];
				//if ($row['dtpro']) {
				
				if (isset($row['dtpro'])) {
					$dtpro 	= $row['dtpro'];
					$vencida = 'S';					
				} else {
					$dtpro = null;
					$vencida = 'N';
				}
				//}
				$vlrpar	= $row['vlrpar'];
				$codnap = $row['nap'];
				$numapr = $row['apr'];
				$nivel 	= $row['nivel'];
				
				//var_dump($row[0]->par);
				//var_dump($dtpro);
				$atual = date('Y-m-d');
				$dtpr = str_replace("/", "-", $dtpro);
   				$dtpr = date('Y-m-d', strtotime($dtpr));
   				list($y, $m, $d) = explode('-', $atual);
   				$t = mktime(0, 0, 0, $m, $d + 2, $y);
   				$atual = strftime('%Y-%m-%d', $t);
   				//var_dump($dtpr);
				//var_dump($atual);
				
				//echo $vencida;
				//exit();
				if ($vencida == 'S') {
                                    if (strtotime($dtpr) > strtotime($atual)) {
                                        if ($tempar == 'S') {
                                            $data_par = array(
                                                    $dtpro,
                                                    $emp,
                                                    $fil,
                                                    $oc
                                            );

                                            $sql_par = "UPDATE E420PAR SET VCTPAR = ?, DIAPAR = 0 WHERE CODEMP = ? AND CODFIL = ? AND NUMOCP = ?";

                                            $result_par = $this->ocs->crud($sql_par, $data_par);

                                            if ($result_par) {
                                                    $continua = 1;
                                            } else {
                                                    //return false;
                                                    $erro += 1;
                                                    $continua = 0;
                                                    $msg_erro .= '<div class="alert alert-danger small">Erro ao prorrogar OC: '.$oc.' da Filial: '.$fil.'</div>';
                                            }
                                        } else {
                                            $data_par = array(
                                                            $emp,
                                                            $fil,
                                                            $oc,
                                                            1,
                                                            null,
                                                            '01',
                                                            null,
                                                            0,							
                                                            $dtpro,
                                                            100,
                                                            $vlrpar,
                                                            0, 
                                                            '',
                                                            0,
                                                            $coduser,
                                                            null
                                            );

                                            $sql_par = "INSERT INTO E420PAR VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TO_CHAR(SYSDATE,'DD/MM/YYYY'),((TO_CHAR(SYSDATE,'hh24')*60)+TO_CHAR(SYSDATE,'mi')),?)";

                                            $result_par = $this->ocs->crud($sql_par, $data_par);
						
                                            if ($result_par) { 						
                                                    $data_mudapar = array(
                                                                    'S',
                                                                    $emp,
                                                                    $fil,
                                                                    $oc
                                                    );

                                                    $sql_mudapar = "UPDATE E420OCP SET TEMPAR = ? WHERE CODEMP = ? AND CODFIL = ? AND NUMOCP = ?";

                                                    $result_mudapar = $this->ocs->crud($sql_mudapar, $data_mudapar);
                                                    if ($result_mudapar) {
                                                            $continua = 1;
                                                    } else {
                                                            //return false;
                                                            $erro += 1;
                                                            $continua = 0;
                                                            $msg_erro .= '<div class="alert alert-danger small">Erro ao prorrogar OC: '.$oc.' da Filial: '.$fil.'</div>';
                                                    }
                                            } else {
                                                    //return false;
                                                    $erro += 1;
                                                    $continua = 0;
                                                    $msg_erro .= '<div class="alert alert-danger small">Erro ao prorrogar OC: '.$oc.' da Filial: '.$fil.'</div>';
                                            }
                                        }
                                    } else {
                                            $erro += 1;
                                            $continua = 0;
                                            $msg_erro .= '<div class="alert alert-danger small">Erro a data de prorrogação deve ser maior que '.date('d/m/Y', strtotime($atual)).'. OC: '.$oc.' da Filial: '.$fil.'</div>';
                                    }
				}
 				//exit();
 				//var_dump($continua);
 				//var_dump($codnap);
				//$resultpar = $this->ocs->verparcela($key);
				if ($continua == 1) {
				//var_dump($resultpar);
                                
                                if ($codnap == 25) {
                                    
                                    $seqapr = 1;
 					$sqlinsert = $this->insereapr($emp, $numapr, $coduser, $codnap, $seqapr);
 					//var_dump($sqlinsert);
 					//exit();
 					if ($sqlinsert == true) {
 						
                                            $sucess += 1;
                                            $msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
 						
 					} else {
 						$erro += 1;
 						$msg_erro .= '<div class="alert alert-danger small">Erro ao inserir aprovação da OC: '.$oc.' da Filial: '.$fil.'</div>';
 					}
                                    
                                } elseif ($codnap == 30) {
 						
 					$seqapr = 2;
 					$sqlinsert = $this->insereapr($emp, $numapr, $coduser, $codnap, $seqapr);
 					//var_dump($sqlinsert);
 					//exit();
 					if ($sqlinsert == true) {
 						if ($nivel == '(25*30)+70' || $nivel == '25 * 30' || $nivel == '25*30' || $nivel == '(25*30)+70+80'){
 							$sql_altera = $this->atualizaapr($numapr, $nivel, $emp);
 							//var_dump($sql_altera);
 							//exit();
 							if ($sql_altera == true) {
 								$fecha = $this->fechaoc($emp, $fil, $oc);
 								//var_dump($fecha);
 								if($fecha == true) {
 									$sucess += 1;
 									$msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
 								}
 							} else {
 								$erro += 1;
 								$msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar OC: '.$oc.' da Filial: '.$fil.'</div>';
 							}
 						} else {
 							$sucess += 1;
 							$msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
 						}
 					} else {
 						$erro += 1;
 						$msg_erro .= '<div class="alert alert-danger small">Erro ao inserir aprovação da OC: '.$oc.' da Filial: '.$fil.'</div>';
 					}
 						
 				} elseif ($codnap == 40) {
				
                                    $seqapr = 3;
                                    $sqlinsert = $this->insereapr($emp, $numapr, $coduser, $codnap, $seqapr);
				
                                    if ($sqlinsert == true) {
					
					if ($nivel == '(25*30*40)+70' || $nivel == '25*30*40' || $nivel == '(25*30*40)+70+80'){
						$sql_altera = $this->atualizaapr($numapr, $nivel, $emp);
						if ($sql_altera == true) {
							$fecha = $this->fechaoc($emp, $fil, $oc);
							if($fecha == true) {
								$sucess += 1;
								$msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
							}
						} else {
							$erro += 1;
							$msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar OC: '.$oc.' da Filial: '.$fil.'</div>';								
						}
					} else {
						$sucess += 1;
						$msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovado com sucesso! Porém necessita de aprovação de um Diretor</div>';
					}
				} else {
					$erro += 1;
					$msg_erro .= '<div class="alert alert-danger small">Erro ao inserir aprovação da OC: '.$oc.' da Filial: '.$fil.'</div>';
				}
			
                            } elseif ($codnap == 50) {
 				
 				$seqapr = 4;
                                
 				$sqlinsert = $this->insereapr($emp, $numapr, $coduser, $codnap, $seqapr);
 				
 				if ($sqlinsert == true) {
 						
 					if ($nivel == '(25*30*40*50)+70' || $nivel == '25*30*40*50' || $nivel == '(25*30*40*50)+70+80'){
 						$sql_altera = $this->atualizaapr($numapr, $nivel, $emp);
 						if ($sql_altera == true) {
 							$fecha = $this->fechaoc($emp, $fil, $oc);
 							if($fecha == true) {
 								$sucess += 1;
 								$msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
 							}
 						} else {
 							$erro += 1;
 							$msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar OC: '.$oc.' da Filial: '.$fil.'</div>';
 						}
 					} 
 				} else {
 					$erro += 1;
 					$msg_erro .= '<div class="alert alert-danger small">Erro ao inserir aprovação da OC: '.$oc.' da Filial: '.$fil.'</div>';
 				}
 				
                            } elseif ($codnap == 70) {
                                
                                if ($nivel == '(25*30)+70' || $nivel == '(25*30)+70+80') {
                                    $seqapr = 3;
                                } elseif ($nivel == '(25*30*40)+70' || $nivel == '(25*30*40)+70+80') {
                                    $seqapr = 4;
                                } elseif ($nivel == '(25*30*40*50)+70' || $nivel == '(25*30*40*50)+70+80') {
                                    $seqapr = 5;
                                }
 				$sqlinsert = $this->insereapr($emp, $numapr, $coduser, $codnap, $seqapr);
 				
 				if ($sqlinsert == true) {
 						
 					//if ($nivel == '(70)' || $nivel == '70'){
                                    $sql_altera = $this->atualizaapr($numapr, $nivel, $emp);
                                    if ($sql_altera == true) {
                                            $fecha = $this->fechaoc($emp, $fil, $oc);
                                            if($fecha == true) {
                                                    $sucess += 1;
                                                    $msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
                                            }
                                    } else {
                                            $erro += 1;
                                            $msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar OC: '.$oc.' da Filial: '.$fil.'</div>';
                                    }
 					//} 
 				} else {
 					$erro += 1;
 					$msg_erro .= '<div class="alert alert-danger small">Erro ao inserir aprovação da OC: '.$oc.' da Filial: '.$fil.'</div>';
 				}
                            } elseif ($codnap == 80) {
                                
                                if ($nivel == '(25*30)+70+80') {
                                    $seqapr = 4;
                                } elseif ($nivel == '(25*30*40)+70+80') {
                                    $seqapr = 5;
                                } elseif ($nivel == '(25*30*40*50)+70+80') {
                                    $seqapr = 6;
                                }
 				$sqlinsert = $this->insereapr($emp, $numapr, $coduser, $codnap, $seqapr);
 				
 				if ($sqlinsert == true) {
 						
 					//if ($nivel == '(70)' || $nivel == '70'){
                                    $sql_altera = $this->atualizaapr($numapr, $nivel, $emp);
                                    if ($sql_altera == true) {
                                            $fecha = $this->fechaoc($emp, $fil, $oc);
                                            if($fecha == true) {
                                                    $sucess += 1;
                                                    $msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovado com sucesso!</div>';
                                            }
                                    } else {
                                            $erro += 1;
                                            $msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar OC: '.$oc.' da Filial: '.$fil.'</div>';
                                    }
 					//} 
 				} else {
 					$erro += 1;
 					$msg_erro .= '<div class="alert alert-danger small">Erro ao inserir aprovação da OC: '.$oc.' da Filial: '.$fil.'</div>';
 				}
                            }                                                     
 				 				 				 				 				
                            }
			}
		}							
		if ($erro > 0) {
			echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
			echo $msg_erro.'<br>';
		}
		echo '<legend>Foram processados '.$sucess.' pedido(s)!</legend>';
		if ($sucess > 0) {
			echo $msg_sucess;
		}
	}
        
        function cancelar() {
            $id = array_filter($this->input->post('id', TRUE));
            $obs = $this->input->post('obs');
            $session_data   = $this->session->userdata('newadt');
            $coduser = $session_data['usu_codigo'];
            //$nomuser = $session_data['usuario'];
            //$coduser        = 423;
                       
            $erro = 0;
            $sucess = 0;
            $msg_erro = '';
            $msg_sucess = '';
            $continua = 1;
            //var_dump($id);
            //exit();
            if ($obs == null) {
               $erro = 1;
               echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
               echo $msg_erro = 'É necessário informar a justificativa no campo observação!';
               exit();
            }
            
            if ($id) {
                foreach ($id as $key => $row) {
                    list($emp, $fil, $oc) = explode('|',$row['id']);
                    if ($row['seqobs']) {
                        $sequencia = intval($row['seqobs']) + 1;
                    } else {
                        $sequencia = 1;
                    }                    
                    $sqlinsert = $this->insereobs($emp, $fil, $oc, $sequencia, $obs, $coduser);
                    
                    if ($sqlinsert == true) {
                        $sqlcancela = $this->cancelaoc($emp, $fil, $oc);
                        
                        if ($sqlcancela == true) {
                            $sucess += 1;
                            $msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Cancelado com sucesso!</div>';
                        } else {
                            $erro += 1;
                            $msg_erro .= '<div class="alert alert-danger small">Erro ao cancelar OC: '.$oc.' da Filial: '.$fil.'</div>';
                        }
                    } else {
                        $erro += 1;
                        $msg_erro .= '<div class="alert alert-danger small">Erro ao inserir motivo de cancelamento da OC: '.$oc.' da Filial: '.$fil.'</div>';
                    }
                    //var_dump($sequencia);
                    //var_dump($key);
                    //var_dump($row);
                    //var_dump($obs);
                    
                    
                }
            }
            
            if ($erro > 0) {
                echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
                echo $msg_erro.'<br>';
            }
            echo '<legend>Foram processados '.$sucess.' pedido(s)!</legend>';
            if ($sucess > 0) {
                echo $msg_sucess;
            }
        }
        
        
        function cancelarapr() {
            $id = array_filter($this->input->post('id', TRUE));
            //$session_data   = $this->session->userdata('newadt');
            //$coduser = $session_data['usu_codigo'];
            
            //var_dump($id);
            
            $erro = 0;
            $sucess = 0;
            $msg_erro = '';
            $msg_sucess = '';
            $continua = 1;
            
            if ($id) {
                foreach ($id as $key => $row) {
                    list($emp, $fil, $oc) = explode('|',$row['id']);                    
                    $sqldelete = $this->deleteapr($emp, $row['apr']);
                    
                    if ($sqldelete == true) {
                        $sucess += 1;
                        $msg_sucess .= '<div class="alert alert-success small">OC: '.$oc.' da Filial: '.$fil.' Aprovação cancelada com sucesso!</div>';
                    } else {
                        $erro += 1;
                        $msg_erro .= '<div class="alert alert-danger small">Erro ao cancelar aprovação da OC: '.$oc.' da Filial: '.$fil.'</div>';
                    }
                }
            }
            
            if ($erro > 0) {
                echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
                echo $msg_erro.'<br>';
            }
            echo '<legend>Foram processados '.$sucess.' pedido(s)!</legend>';
            if ($sucess > 0) {
                echo $msg_sucess;
            }
        }
	
}
