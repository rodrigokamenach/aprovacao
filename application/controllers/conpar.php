<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Conpar extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('globals','',TRUE);
        $this->load->model('ocs','',TRUE);
        $this->load->model('parcerias','',TRUE);
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
            $dados['fornec'] = $this->globals->lista_forpar();            
            $dados['users'] = $this->globals->lista_users();

            $this->load->view('header_view', $data);
            $this->load->view('conpar_view', $dados);
            $this->load->view('footer_view');
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }
    
    
    function carreg_par() {
        ini_set('max_execution_time', 0);
        $this->load->library('form_validation');
        
        $dtini = $this->input->post('dtini');
        $dtfim = $this->input->post('dtfim');
        $situacao = $this->input->post('situacao');
        $filial = $this->input->post('filial[]');
        $codfor = $this->input->post('fornecedor[]');
        $aprovador = $this->input->post('aprovador');
        
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
        
        $result = $this->parcerias->buscapar($dtini, $dtfim, $filial_list, $codfor_list, $situacao, $aprovador);
        
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
            
            $this->CI->table->set_heading('Titulo', 'Tipo', 'Fornecedor', 'Obs', 'Vencimento', 'Valor Original', 'Valor Corrigido', 'Valor Aprovado', 'Aprovador', 'Data Aprov');
            
            $tot_fil = 0;
            $cont = 0;
            $sub_fil_ped = 0;
            $sub_fil_cor = 0;
            $sub_fil_apr = 0;
            $tot_ped = 0;
            $tot_cor = 0;
            $tot_apr = 0;
            
            foreach ($result as $row) {
                
                if ($row->USU_VLRAPR == null) {
                    $vlrpar = 0;
                } else {
                    $vlrpar = $row->USU_VLRAPR;
                }
                
                if ($row->USU_CODFIL !== $tot_fil) {
                    if ($cont > 0) {
                        $totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 5);
                        //$espaço = array('data' => '', 'colspan' => 3);
                        $this->table->add_row(
                            $totalun,
                            '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.').'</strong></p>',										
                            '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_cor), 2, ',', '.').'</strong></p>',
                            '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_apr), 2, ',', '.').'</strong></p>'
                            //$espaço
                        );															
                        $sub_fil_ped = 0;                                    
                        $sub_fil_cor = 0;
                        $sub_fil_apr = 0;
                    }
                    $filial = array('data' => '<strong>'.$row->USU_CODFIL.' - '.$row->SIGFIL.' '.$row->USU_INSTAN.'</strong>', 'class' => 'info text-left', 'colspan' => 10);
                    $this->table->add_row($filial);
                }
                
                $this->CI->table->add_row(                    
                    '<p class="text-left">'.$row->USU_NUMTIT.'</p>',
                    '<p class="text-left">'.$row->USU_CODTPT.'</p>',
                    '<p class="text-left">'.$row->USU_CODFOR.' - '.$row->APEFOR.'</p>',
                    '<p class="text-left">'.$row->USU_OBSTCP.'</p>',
                    '<p class="text-center">'.$row->USU_VCTORI.'</p>',
                    '<p class="text-right">'.number_format(str_replace("," , "." , $row->USU_VLRORI), 2, ',', '.').'</p>',                                                            
                    '<p class="text-right">'.number_format(str_replace("," , "." , $row->VLRCORRIGIDO), 2, ',', '.').'</p>',                     
                    '<p class="text-right">'.number_format(str_replace("," , "." , $vlrpar), 2, ',', '.').'</p>',
                    '<p class="text-left">'.$row->USU_USUAPR.'-'.$row->NOMUSU.'</p>',
                    '<p class="text-center">'.$row->USU_DATAPR.'</p>'
                );
                
                
                $cont++;
                $sub_fil_ped += str_replace("," , "." , $row->USU_VLRORI);
                $sub_fil_cor += str_replace("," , "." , $row->VLRCORRIGIDO);
                $sub_fil_apr += str_replace("," , "." , $vlrpar);
                $tot_fil = $row->USU_CODFIL;
                $tot_ped += str_replace("," , "." , $row->USU_VLRORI);
                $tot_cor += str_replace("," , "." , $row->VLRCORRIGIDO);
                $tot_apr += str_replace("," , "." , $vlrpar);
                                
            }
            
            $totalge = array('data' => '<strong>Total Geral</strong>', 'class' => 'text-left', 'colspan' => 5);
            //$espaço = array('data' => '', 'colspan' => 7);
                $this->table->add_row(
                    $totalge,
                    '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_ped), 2, ',', '.').'</strong></p>',					
                    '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_cor), 2, ',', '.').'</strong></p>',
                    '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_apr), 2, ',', '.').'</strong></p>'
                    //$espaço
            );
                
            $tabela = array ('tabela' => $this->CI->table->generate());            
                
            $this->load->view('conpartable_view', $tabela);    
            
        } else {
            echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Nenhum resultado foi encontrado!</div>';
        }
        
    }
}

