<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Parceria extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('globals', '', TRUE);
        $this->load->model('ocs', '', TRUE);
        $this->load->model('parcerias', '', TRUE);
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
            $dados['fornec'] = $this->globals->lista_forpar();

            $this->load->view('header_view', $data);
            $this->load->view('parceria_view', $dados);
            $this->load->view('footer_view');
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }
    
    
    function carreg_parc() {
        ini_set('max_execution_time', 0);
        $this->load->library('form_validation');

        $dtini = $this->input->post('dtini');
        $dtfim = $this->input->post('dtfim');
        $operacao = $this->input->post('operacao');
        $filial = $this->input->post('filial[]');
        $fornec = $this->input->post('fornecedor[]');
        $session_data = $this->session->userdata('newadt');
        $coduser = $session_data['usu_codigo'];
        
        if (!empty($filial)) {
            $filial_list = rtrim(implode(',', $filial), ',');
        } else {
            $filial_list = '';
        }

        if (!empty($fornec)) {
            $codfor_list = rtrim(implode("','", $fornec), ',');
        } else {
            $codfor_list = '';
        }

        $codemp = $this->ocs->getEmp($filial_list);

        if (!empty($codemp)) {
            $codemp_list = rtrim(implode(',', $codemp), ',');
        } else {
            $codemp_list = '';
        }

        $statusapr = $this->parcerias->getApr($codemp_list, $coduser);
        //var_dump($statusapr);

        if ($statusapr) {
            $checkccu = $this->parcerias->getCcu($statusapr, $coduser);
        } else {
            echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Você não tem permissão de aprovador</div>';
            exit();
        }
        
        $result = $this->parcerias->busca($dtini, $dtfim, $filial_list, $codfor_list, $coduser, $codemp_list, $statusapr, $checkccu);
        
        //var_dump($result);
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
            
            $this->CI->table->set_heading('<a href="javascript:;" class="btn-sm btn-info" onclick="tudo()" id="selecionarTodos"><i class="fa fa-check-square-o fa-lg"></i></a>','Título', 'Tipo', 'Fornecedor', 'Obs','Vencimento','Vlr Original','Vlr Corrigido');
            
            $tot_fil = 0;
            $cont = 0;
            $sub_fil_ped = 0;
            $sub_fil_cor = 0;
            $tot_ped = 0;
            $tot_cor = 0;
            
            foreach ($result as $row) {
                if ($row->USU_CODFIL !== $tot_fil) {
                    if ($cont > 0) {
                        $totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 6);
                        //$espaço = array('data' => '', 'colspan' => 3);
                        $this->table->add_row(
                            $totalun,
                            '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.').'</strong></p>',										
                            '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_cor), 2, ',', '.').'</strong></p>'
                            //$espaço
                        );															
                        $sub_fil_ped = 0;                                    
                        $sub_fil_cor = 0;
                    }
                    $filial = array('data' => '<strong>'.$row->USU_CODFIL.' - '.$row->SIGFIL.' '.$row->USU_INSTAN.'</strong>', 'class' => 'info text-left', 'colspan' => 8);
                    $this->table->add_row($filial);
                }
                
                
                $check = array(
                    'name'  => 'id['.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT).'][id]',
                    'id'    => 'checkcol',
                    'value' => $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT,
                    'title' => str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT),
                    'rel'   => $row->VLRCORRIGIDO
                );
                
                $codfor = array(
                    'name'      => 'id['.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT).'][codfor]',
                    'id'        => 'codfor'.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT),
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->USU_CODFOR,
                    'title'     => $row->USU_CODFOR
                );
                
                $numtit = array(
                    'name'      => 'id['.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT).'][numtit]',
                    'id'        => 'numtit'.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT),
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->USU_NUMTIT,
                    'title'     => $row->USU_NUMTIT
                );
                
                $dtvenc = array(
                    'name'      => 'id['.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT).'][dtvenc]',
                    'id'        => 'dtvenc'.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT),
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->USU_VCTORI,
                    'title'     => $row->USU_VCTORI
                );
                
                $vlrcor = array(
                    'name'      => 'id['.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT).'][vlrcor]',
                    'id'        => 'vlrcor'.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT),
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->VLRCORRIGIDO,
                    'title'     => $row->VLRCORRIGIDO
                );
                
                $emp = array(
                    'name'      => 'id['.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT).'][emp]',
                    'id'        => 'emp'.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT),
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->USU_CODEMP,
                    'title'     => $row->USU_CODEMP
                );
                
                $codnap = array(
                    'name'      => 'id['.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT).'][codnap]',
                    'id'        => 'codnap'.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT),
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->CODNAP,
                    'title'     => $row->CODNAP
                );
                
                $fil = array(
                    'name'      => 'id['.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT).'][fil]',
                    'id'        => 'fil'.str_replace("/" , "" , $row->USU_CODFIL.$row->USU_CODFOR.$row->USU_NUMTIT),
                    'disabled'  => 'disabled',
                    'type'      => 'hidden',
                    'class'     => 'campo',
                    'value'     => $row->USU_CODFIL,
                    'title'     => $row->USU_CODFIL
                );
                
                
                $this->CI->table->add_row(
                    form_checkbox($check).form_input($codfor).form_input($numtit).form_input($dtvenc).form_input($vlrcor).form_input($emp).form_input($codnap).form_input($fil),
                    '<p class="text-left">'.$row->USU_NUMTIT.'</p>',
                    '<p class="text-left">'.$row->USU_CODTPT.'</p>',
                    '<p class="text-left">'.$row->USU_CODFOR.' - '.$row->APEFOR.'</p>',
                    '<p class="text-left">'.$row->USU_OBSTCP.'</p>',
                    '<p class="text-center">'.$row->USU_VCTORI.'</p>',
                    '<p class="text-right">'.number_format(str_replace("," , "." , $row->USU_VLRORI), 2, ',', '.').'</p>',                                                            
                    '<p class="text-right">'.number_format(str_replace("," , "." , $row->VLRCORRIGIDO), 2, ',', '.').'</p>'                     
                );
                
                
                $cont++;
                $sub_fil_ped += str_replace("," , "." , $row->USU_VLRORI);
                $sub_fil_cor += str_replace("," , "." , $row->VLRCORRIGIDO);
                $tot_fil = $row->USU_CODFIL;
                $tot_ped += str_replace("," , "." , $row->USU_VLRORI);
                $tot_cor += str_replace("," , "." , $row->VLRCORRIGIDO);
                
            }
            
            $totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 6);
                //$espaço = array('data' => '', 'colspan' => 3);
                $this->table->add_row(
                    $totalun,
                    '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.').'</strong></p>',
                    '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_cor), 2, ',', '.').'</strong></p>'
                    //$espaço
            );
                
            $totalge = array('data' => '<strong>Total Geral</strong>', 'class' => 'text-left', 'colspan' => 6);
            //$espaço = array('data' => '', 'colspan' => 7);
                $this->table->add_row(
                    $totalge,
                    '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_ped), 2, ',', '.').'</strong></p>',					
                    '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_cor), 2, ',', '.').'</strong></p>'
                    //$espaço
            );
                
            $tabela = array (
                'tabela'    => $this->CI->table->generate(),
                'user'      => $coduser,
                'operacao'  => $operacao
            );            
                
            $this->load->view('parctable_view', $tabela);    
        } else {
            echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Nenhum resultado foi encontrado!</div>';
        } 
    }


    function aprovar() {
        $id = array_filter($this->input->post('id', TRUE));        
        $session_data = $this->session->userdata('newadt');
        $coduser = $session_data['usu_codigo'];
        
        //var_dump($id);
        //exit();
        $erro = 0;
        $sucess = 0;
        $msg_erro = '';
        $msg_sucess = '';
        
        if ($id) {
           foreach ($id as $row) {
                $codfor = $row['codfor'];
                $numtit = $row['numtit'];
                $dtvenc = $row['dtvenc'];
                $vlrcor = $row['vlrcor'];
                $codnap = $row['codnap'];
                $emp    = $row['emp'];
                $fil    = $row['fil'];
                
                $numapr = $this->parcerias->getNumapr();
                
                $insere_614apr = $this->insereaprpar($emp, $numapr[0]['NUMAPR'], $vlrcor, $coduser, $codnap);
                
                if ($insere_614apr == true) {
                    $seqapr = 1;
                    
                    $insere_614usu = $this->insereusupar($emp, $numapr[0]['NUMAPR'], $coduser, $codnap, $seqapr);
                    
                     if ($insere_614usu == true) {
                        
                        $altpar = array(
                            intval($coduser),
                            69,                            
                            intval($numapr[0]['NUMAPR']),
                            $vlrcor,
                            $emp,
                            $fil,
                            $codfor,
                            $numtit,
                            $dtvenc
                        );
                        
                        $sql_par = "update usu_tparagr set USU_USUAPR = ?, USU_DATAPR = TO_CHAR(SYSDATE,'DD/MM/YYYY'), USU_HORAPR = ((TO_CHAR(SYSDATE,'hh24')*60)+TO_CHAR(SYSDATE,'mi')), USU_ROTNAP = ?, USU_NUMAPR = ?, USU_VLRAPR = ? where USU_CODEMP = ? and USU_CODFIL = ? and USU_CODFOR = ? and USU_NUMTIT = ? and USU_VCTORI = ?";
                        
                        $result_par = $this->ocs->crud($sql_par, $altpar);
                        
                        if ($result_par) {                            
                            $sucess += 1;
                            $msg_sucess .= '<div class="alert alert-success small">Fornecedor: '.$codfor.' Titulo: '.$numtit.' Aprovado com sucesso!</div>';                                                                                    
                        } else {
                            $erro += 1;
                            $msg_erro .= '<div class="alert alert-danger small">Erro ao aprovar o Fornecedor: '.$codfor.' Titulo: '.$numtit.'</div>';
                        }
                                                 
                         
                     } else {
                        $erro += 1;
                        $msg_erro .= '<div class="alert alert-danger small">E614USU - Erro ao inserir aprovação do Fornecedor: '.$codfor.' Titulo: '.$numtit.'</div>';
                     }
                } else {
                    $erro += 1;
                    $msg_erro .= '<div class="alert alert-danger small">E614APR - Erro ao inserir aprovação do Fornecedor: '.$codfor.' Titulo: '.$numtit.'</div>';
                }
               
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
    
    
    public function insereaprpar ($emp, $numapr, $valor, $coduser, $codnap) {
        
        $insereapr_fol = array(
            intval($emp),
            0,
            69,
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
    
    
    public function insereusupar($emp, $numapr, $coduser, $codnap, $seqapr) {
		
        $insere_apr = array(
                        intval($emp),
                        0,
                        69,
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