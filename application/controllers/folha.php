<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Folha extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('globals', '', TRUE);
        $this->load->model('ocs', '', TRUE);
        $this->load->model('folhas', '', TRUE);
    }

    function index() {
        if ($this->session->userdata('newadt')) {
            $session_data = $this->session->userdata('newadt');
            $data['usuario'] = $session_data['usuario'];
            $data['usu_permissoes'] = $session_data['usu_permissoes'];
            $data['usu_filial'] = $session_data['usu_filial'];
            $data['usu_email'] = $session_data['usu_email'];
            $data['usu_codigo'] = $session_data['usu_codigo'];
            $data['usu_area'] = $session_data['usu_area'];
//                if (empty($dia)) {
//                        $dia = date('m/Y');
//                }

            $dados['filiais'] = $this->globals->lista_filial();
            $dados['cif'] = $this->globals->lista_cif();

            $this->load->view('header_view', $data);
            $this->load->view('folha_view', $dados);
            $this->load->view('footer_view');
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    function carreg_folha() {
        ini_set('max_execution_time', 0);
        $this->load->library('form_validation');

        $dtini = $this->input->post('dtini');
        $dtfim = $this->input->post('dtfim');
        $operacao = $this->input->post('operacao');
        $filial = $this->input->post('filial[]');
        $cif = $this->input->post('cif[]');
        $session_data = $this->session->userdata('newadt');
        $coduser = $session_data['usu_codigo'];
        $coduser = 556;

        if (!empty($filial)) {
            $filial_list = rtrim(implode(',', $filial), ',');
        } else {
            $filial_list = '';
        }

        if (!empty($cif)) {
            $codcif_list = rtrim(implode("','", $cif), ',');
        } else {
            $codcif_list = '';
        }

        $codemp = $this->ocs->getEmp($filial_list);

        if (!empty($codemp)) {
            $codemp_list = rtrim(implode(',', $codemp), ',');
        } else {
            $codemp_list = '';
        }

        $statusapr = $this->folhas->getApr($codemp_list, $coduser);
        //var_dump($statusapr);

        if ($statusapr) {
            $checkccu = $this->folhas->getCcu($statusapr, $coduser);
        } else {
            echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Você não tem permissão de aprovador</div>';
            exit();
        }
        
        
        $result = $this->folhas->busca($dtini, $dtfim, $filial_list, $codcif_list, $coduser, $codemp_list, $statusapr, $checkccu);
        
        //var_dump($result);
	//exit();
        if($result) {
            if ($dtini == null) {
                $dtini = 0;
                $dtfim = 0;
            }
            
            if ($dtfim == null) {
                $dtini = 0;
                $dtfim = 0;
            }
            
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
            
            $this->CI->table->set_heading('<a href="javascript:;" class="btn-sm btn-info" onclick="tudo()" id="selecionarTodos"><i class="fa fa-check-square-o fa-lg"></i></a>','CIF','Data', 'Qtde', 'Valor', 'Detalhes');
            
            $tot_fil = 0;
            $cont = 0;
            $sub_fil_ped = 0;
            $tot_ped = 0;
            
            foreach ($result as $row) {
                if ($row->FILSAP !== $tot_fil) {
                    if ($cont > 0) {
                        $totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 4);
                        $espaço = array('data' => '', 'colspan' => 4);
                        $this->table->add_row(
                            $totalun,
                            '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.').'</strong></p>',										
                            $espaço
                        );															
                        $sub_fil_ped = 0;                                    
                    }
                    $filial = array('data' => '<strong>'.$row->FILSAP.' - '.$row->SIGFIL.' '.$row->USU_INSTAN.'</strong>', 'class' => 'info text-left', 'colspan' => 6);
                    $this->table->add_row($filial);
                }
                
                $check = array(
                    'name'  => 'id['.$row->CODCIF.$row->FILSAP.'][id]',
                    'id'    => 'checkcol',
                    'value' => $row->CODCIF.$row->FILSAP,
                    'title' => $row->CODCIF.$row->FILSAP,
                    'rel'   => $row->VLRPGT
                );
                
                $datini = array(
                    'name'      => 'id['.$row->CODCIF.$row->FILSAP.'][datini]',
                    'id'        => 'datini'.$row->CODCIF.$row->FILSAP,
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $dtini,
                    'title'     => $dtini
                );
                
                $datfim = array(
                    'name'      => 'id['.$row->CODCIF.$row->FILSAP.'][datfim]',
                    'id'        => 'datfim'.$row->CODCIF.$row->FILSAP,
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $dtfim,
                    'title'     => $dtfim
                );
                
                $cif = array(
                    'name'      => 'id['.$row->CODCIF.$row->FILSAP.'][cif]',
                    'id'        => 'cif'.$row->CODCIF.$row->FILSAP,
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->CODCIF,
                    'title'     => $row->CODCIF
                );
                
                $fil = array(
                    'name'      => 'id['.$row->CODCIF.$row->FILSAP.'][fil]',
                    'id'        => 'fil'.$row->CODCIF.$row->FILSAP,
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->FILSAP,
                    'title'     => $row->FILSAP
                );
                
                $emp = array(
                    'name'      => 'id['.$row->CODCIF.$row->FILSAP.'][emp]',
                    'id'        => 'emp'.$row->CODCIF.$row->FILSAP,
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->CODEMP,
                    'title'     => $row->CODEMP
                );
                
                $codnap = array(
                    'name'      => 'id['.$row->CODCIF.$row->FILSAP.'][codnap]',
                    'id'        => 'codnap'.$row->CODCIF.$row->FILSAP,
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->CODNAP,
                    'title'     => $row->CODNAP
                );
                
                $datvnc = array(
                    'name'      => 'id['.$row->CODCIF.$row->FILSAP.'][datvnc]',
                    'id'        => 'datvnc'.$row->CODCIF.$row->FILSAP,
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->VNCTIT,
                    'title'     => $row->VNCTIT
                );
                
                $valor = array(
                    'name'      => 'id['.$row->CODCIF.$row->FILSAP.'][valor]',
                    'id'        => 'valor'.$row->CODCIF.$row->FILSAP,
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->VLRPGT,
                    'title'     => $row->VLRPGT
                );
                
                $this->CI->table->add_row(
                    form_checkbox($check).form_input($datini).form_input($datfim).form_input($cif).form_input($fil).form_input($codnap).form_input($emp).form_input($valor).form_input($datvnc),
                    '<p class="text-left">'.$row->CODCIF.' - '.$row->DESCIF.'</p>',
                    '<p class="text-center">'.$row->VNCTIT.'</p>',
                    '<p class="text-right">'.$row->QTDLAN.'</p>',
                    '<p class="text-right">'.number_format(str_replace("," , "." , $row->VLRPGT), 2, ',', '.').'</p>',                                                            
                    '<a href="javascript:;" onclick="jVeItens('.$row->CODCIF.','.$row->FILSAP.',\''.$dtini.'\',\''.$dtfim.'\',\''.$row->VNCTIT.'\')"><i class="fas fa-clipboard-check fa-2x"></i></a>'                    
                );
                
                $cont++;
                $sub_fil_ped += str_replace("," , "." , $row->VLRPGT);
                $tot_fil = $row->FILSAP;
                $tot_ped += str_replace("," , "." , $row->VLRPGT);
                                
            }
            
            $totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 4);
                $espaço = array('data' => '', 'colspan' => 4);
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
                'tabela'    => $this->CI->table->generate(),
                'user'      => $coduser,
                'operacao'  => $operacao
            );            
                
            $this->load->view('folhatable_view', $tabela);
        } else {
            echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Nenhum resultado foi encontrado!</div>';
        }
        
    }
    
    function busca_folitem() {
        $dtini = $this->input->post('dtini');
        $dtfim = $this->input->post('dtfim');
        $dtvnc = $this->input->post('dtvnc');
        $cif = $this->input->post('cif');
        $filial = $this->input->post('filial');
        //list($pedido, $filial) = explode('-' , $codigo);
        $dados['result'] = $this->folhas->getItem($cif, $filial, $dtini, $dtfim, $dtvnc);
        $this->load->view('itensfol_view', $dados);
    }
    
    function aprovar() {
        $id = array_filter($this->input->post('id', TRUE));        
        $session_data = $this->session->userdata('newadt');
        $coduser = $session_data['usu_codigo'];
        
        //var_dump($id);
        
        $erro = 0;
        $sucess = 0;
        $msg_erro = '';
        $msg_sucess = '';
        
        if ($id) {
            foreach ($id as $row) {
                //var_dump($row);
                
                $valor  = $row['valor'];
                $emp    = $row['emp'];
                $datini = $row['datini'];
                $datfim = $row['datfim'];
                $datvnc = $row['datvnc'];
                $cif    = $row['cif'];
                $fil    = $row['fil'];
                $codnap = $row['codnap'];                
                
                switch ($cif) {
                    case 8:
                        $codtrs = '90506';
                    case 9:                        
                    case 10:
                        $codtrs = '90510';
                    case 11:
                        $codtrs = '90511';
                    case 13:
                        $codtrs = '90507';
                    default :
                        $codtrs = '90505';
                }                
                            
                
                $numapr = $this->folhas->getNumapr();
                
                $insere_614apr = $this->insereaprfol($emp, $numapr[0]['NUMAPR'], $valor, $coduser, $codnap);
                
                if ($insere_614apr == true) {
                    $seqapr = 1;
                    
                    $insere_614usu = $this->insereusufol($emp, $numapr[0]['NUMAPR'], $coduser, $codnap, $seqapr);
                    
                     if ($insere_614usu == true) {
                        
                        if ($datini == 0 or $datfim == 0) {
                            
                            $altper = array(
                                $codtrs,                            
                                intval($numapr[0]['NUMAPR']),
                                intval($coduser),
                                $fil,
                                $cif,
                                $datvnc
                            );

                            $sql_per = "update vetorh.R047PER set CODTRS = ?, USU_NUMAPR = ?, USU_CODUSU = ? where FILSAP = ? and CODCIF = ? and VNCTIT = ?";
                            
                        } else {
                         
                            $altper = array(
                                $codtrs,                            
                                intval($numapr[0]['NUMAPR']),
                                intval($coduser),
                                $fil,
                                $cif,
                                $datini,
                                $datfim
                            );

                            $sql_per = "update vetorh.R047PER set CODTRS = ?, USU_NUMAPR = ?, USU_CODUSU = ? where FILSAP = ? and CODCIF = ? and VNCTIT BETWEEN ? and ?";
                        }
                        
                        $result_per = $this->ocs->crud($sql_per, $altper);
                        
                        if ($result_per) {
                            
                            if ($datini == 0 or $datfim == 0) {
                                $altpen = array(
                                    $codtrs,                                                                                                
                                    $fil,
                                    $cif,
                                    $datvnc
                                );

                                $sql_pen = "update vetorh.R047PEN set CODTRS = ? where FILSAP = ? and CODCIF = ? and VNCTIT = ?";
                                
                            } else {
                            
                                $altpen = array(
                                    $codtrs,                                                                                                
                                    $fil,
                                    $cif,
                                    $datini,
                                    $datfim
                                );

                                $sql_pen = "update vetorh.R047PEN set CODTRS = ? where FILSAP = ? and CODCIF = ? and VNCTIT BETWEEN ? and ?";
                            }
                            
                            $result_pen = $this->ocs->crud($sql_pen, $altpen);
                            
                            if ($result_pen) {
                                $sucess += 1;
                                $msg_sucess .= '<div class="alert alert-success small">CIF: '.$cif.' Valor: '.$valor.' Aprovado com sucesso!</div>';
                            } else {
                                $erro += 1;
                                $msg_erro .= '<div class="alert alert-danger small">Erro ao atualizar tabela R047PEN - Informe ao administrador do sistema</div>';
                            }
                            
                            
                        } else {
                            $erro += 1;
                            $msg_erro .= '<div class="alert alert-danger small">Erro ao atualizar tabela R047PER - Informe ao administrador do sistema</div>';
                        }
                                                 
                         
                     } else {
                        $erro += 1;
                        $msg_erro .= '<div class="alert alert-danger small">E614USU - Erro ao inserir aprovação da CIF '.$cif.' Valor: '.$valor.'</div>';
                     }
                } else {
                    $erro += 1;
                    $msg_erro .= '<div class="alert alert-danger small">E614APR -Erro ao inserir aprovação da CIF '.$cif.' Valor: '.$valor.'</div>';
                }
                
                // $numapr[0]['NUMAPR'];
            }
            
            if ($erro > 0) {
                echo '<legend>Foram encontrados '.$erro.' erro(s)!</legend>';
                echo $msg_erro.'<br>';
            }
            echo '<legend>Foram processados '.$sucess.' folha(s)!</legend>';
            if ($sucess > 0) {
                echo $msg_sucess;
            }
        }
    }
    
    public function insereaprfol ($emp, $numapr, $valor, $coduser, $codnap) {
        
        $insereapr_fol = array(
            intval($emp),
            0,
            68,
            intval($numapr),
            intval($codnap),
            $valor,
            '',
            'APR',
            0,
            '',
            intval($coduser),
            0,            
            '',
            0,
            1,
            '',            
            ''            
        );
        
        $sqlapr_fol = "INSERT INTO E614APR VALUES (?,?,?,?,?,?,?,?,?,?,?,TO_CHAR(SYSDATE,'DD/MM/YYYY'),((TO_CHAR(SYSDATE,'hh24')*60)+TO_CHAR(SYSDATE,'mi')),?,?,?,?,?,?)";
        
        $result_aprfol = $this->ocs->crud($sqlapr_fol, $insereapr_fol);
		
        if ($result_aprfol) {			
                return  true; 
        } else {
                return false;
        }
        
    }
    
    public function insereusufol($emp, $numapr, $coduser, $codnap, $seqapr) {
		
        $insere_apr = array(
                        intval($emp),
                        0,
                        68,
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

}
