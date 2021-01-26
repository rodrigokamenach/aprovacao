<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Confol extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('globals','',TRUE);
        $this->load->model('ocs','',TRUE);
        $this->load->model('folhas','',TRUE);
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
            $dados['cif'] = $this->globals->lista_cif();            
            $dados['users'] = $this->globals->lista_users();

            $this->load->view('header_view', $data);
            $this->load->view('confol_view', $dados);
            $this->load->view('footer_view');
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }
    
    function carreg_fol() {
        ini_set('max_execution_time', 0);
        $this->load->library('form_validation');
        
        $dtini = $this->input->post('dtini');
        $dtfim = $this->input->post('dtfim');
        $situacao = $this->input->post('situacao');
        $filial = $this->input->post('filial[]');
        $cif = $this->input->post('cif[]');
        $aprovador = $this->input->post('aprovador');
        
        if (!empty($filial)) {
            $filial_list = rtrim(implode(',', $filial), ',');
        } else {
            $filial_list = '';
        }

        if (!empty($cif)) {
            $codcif_list = rtrim(implode(',', $cif), ',');
        } else {
            $codcif_list = '';
        }
                
        $result = $this->folhas->buscafol($dtini, $dtfim, $filial_list, $codcif_list, $situacao, $aprovador);
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
            
            $this->CI->table->set_heading('CIF', 'Data', 'Qtde', 'Valor', 'Aprovador', 'Detalhes');
            
            $tot_fil = 0;
            $cont = 0;
            $sub_fil_ped = 0;
            $tot_ped = 0;
            
            foreach ($result as $row) {
                if ($row->FILSAP !== $tot_fil) {
                    if ($cont > 0) {
                        $totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 3);
                        $espaço = array('data' => '', 'colspan' => 2);
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
                
                if ($row->USU_CODUSU == 0) {
                    $user = '';
                } else {
                    $user = $row->USU_CODUSU;
                }
                
                $this->CI->table->add_row(                    
                    '<p class="text-left">'.$row->CODCIF.' - '.$row->DESCIF.'</p>',
                    '<p class="text-center">'.$row->VNCTIT.'</p>',
                    '<p class="text-right">'.$row->QTDLAN.'</p>',
                    '<p class="text-right">'.number_format(str_replace("," , "." , $row->VLRPGT), 2, ',', '.').'</p>',
                    '<p class="text-center">'.$user.'-'.$row->NOMUSU.'</p>',
                    '<a href="javascript:;" onclick="jVeItens('.$row->CODCIF.','.$row->FILSAP.',\''.$dtini.'\',\''.$dtfim.'\',\''.$row->VNCTIT.'\')"><i class="fas fa-clipboard-check fa-2x"></i></a>'                    
                );
                
                $cont++;
                $sub_fil_ped += str_replace("," , "." , $row->VLRPGT);
                $tot_fil = $row->FILSAP;
                $tot_ped += str_replace("," , "." , $row->VLRPGT);
                                                            
            }
            
            $totalun = array('data' => '<strong>Total Unidade</strong>', 'class' => 'text-left', 'colspan' => 3);
                $espaço = array('data' => '', 'colspan' => 2);
                $this->table->add_row(
                    $totalun,
                    '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $sub_fil_ped), 2, ',', '.').'</strong></p>',
                    $espaço
                );
                
                $totalge = array('data' => '<strong>Total Geral</strong>', 'class' => 'text-left', 'colspan' => 3);
                //$espaço = array('data' => '', 'colspan' => 7);
                    $this->table->add_row(
                        $totalge,
                        '<p class="text-right"><strong>'.number_format(str_replace("," , "." , $tot_ped), 2, ',', '.').'</strong></p>',					
                        $espaço
                );
            
            $tabela = array ('tabela' => $this->CI->table->generate());            
                
            $this->load->view('confoltable_view', $tabela);
        } else {
            echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Nenhum resultado foi encontrado!</div>';
        }
    }
    
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

