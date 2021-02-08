<?php

class Parcerias extends CI_Model {
    function getApr($codemp, $coduser) {

        if ($codemp != null) {
            $condicao = "AND CODEMP IN ($codemp)";
        } else {
            $condicao = '';
        }

        $query = $this->db->query("SELECT CODEMP, ROTNAP, CODNAP FROM E068UNA WHERE ROTNAP = 69 AND CODUSU = $coduser AND SITUNA = 'A' $condicao order by codemp");

        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                $apr[$row['CODEMP']] = array(
                    'CODEMP' => $row['CODEMP'],
                    'ROTNAP' => $row['ROTNAP'],
                    'CODNAP' => $row['CODNAP']
                );
            }
            return $apr;
        } else {
            return false;
        }
    }

    function getCcu($datapr, $coduser) {
        //var_dump($datapr);		
        foreach ($datapr as $row) {
            //var_dump($row);
            //echo $row['CODEMP'];
            $query = $this->db->query("SELECT CODCCU, CODNAP FROM E068CNA
                                        WHERE CODEMP = " . $row['CODEMP'] . " 
                                                AND ROTNAP = " . $row['ROTNAP'] . " 
                                                AND CODNAP = " . $row['CODNAP'] . " 
                                                AND CODUSU = $coduser
                                                AND LENGTH(CODCCU) >=1
                                                AND SITCNA = 'A'");

            if ($query->num_rows() > 0) {
                $codccu[$row['CODEMP']] = $query->result();
            } else {
                $codccu[$row['CODEMP']] = false;
            }
        }
        return $codccu;
    }
    
    function getNumapr() {
        $query = $this->db->query("SELECT usu_tnumaprpar_seq.nextval numapr FROM dual");
        
        return $query->result_array();
    }
    
    function busca($dtini, $dtfim, $filial_list, $codfor_list, $coduser, $codemp_list, $statusapr, $checkccu) {
        $condicao = '';

        if ($dtini != null and $dtfim != null) {
            $condicao .= "and a.USU_VCTORI between to_date('$dtini', 'dd/mm/yyyy') and to_date('$dtfim', 'dd/mm/yyyy')";
        }

        if ($filial_list != null) {
            $condicao .= "AND a.USU_CODFIL IN ($filial_list)";
        }

        if ($codfor_list != null) {
            $condicao .= "AND a.USU_CODFOR IN ('$codfor_list')";
        } 
        
        $sql = '';
        
        if ($checkccu) {
            foreach ($checkccu as $key => $row) {
                if ($statusapr) {
                    $codnap = $statusapr[$key]['CODNAP'];
                }
                
                if ($row) {
                    foreach ($row as $ccu) {
                        //echo $ccu->CODCCU;					
                        $cculist[] = $ccu->CODCCU;
                    }
                    
                    if (!empty($cculist)) {
                        $ccu_list = rtrim(implode(',', $cculist), ',');
                    } else {
                        $ccu_list = '';
                    }
                    
                    $condicao_ccu = '';
                    $tamanho = strlen($ccu_list);
                    
                    if ($tamanho > 1000) {
                            $numero = $tamanho/1000;
                            $cculista = str_split($ccu_list, 1000);
                            for ($n = 0; $n < $numero; $n++) {
                                    if ($n >=1) {
                                            $condicao_ccu .= "OR a.USU_CODCCU IN (".ltrim(rtrim($cculista[$n],','),',').")";
                                    } else {
                                            $condicao_ccu .= "a.USU_CODCCU IN (".ltrim(rtrim($cculista[$n],','),',').")";
                                    }
                                    //echo $cculista[$n];							
                            }
                    } else {
                            $condicao_ccu = "a.USU_CODCCU IN ($ccu_list)";
                    }
                    
                    if ($condicao_ccu) {
                            $condccu = "AND (".$condicao_ccu.")";
                    } else {
                            $condccu ='';
                    }
                    
                    $q1 = "SELECT distinct a.USU_CODEMP , a.USU_CODFIL ,b.SIGFIL,b.USU_INSTAN, a.USU_NUMTIT , a.USU_CODTPT ,a.USU_CODFOR , c.APEFOR, a.USU_DATEMI , a.USU_DATENT ,
                                    a.USU_OBSTCP , a.USU_VCTORI , a.USU_VLRORI ,
                                    round((a.USU_VLRORI / (SELECT d.vlrcot FROM e031imo d where a.USU_CODMOE = d.codmoe and a.USU_DATEMI = d.datmoe)) * (SELECT d.vlrcot FROM e031imo d where a.USU_CODMOE = d.codmoe and a.USU_DATENT = d.datmoe),2) vlrcorrigido,  
                                    a.USU_CODMOE , a.USU_DATPPT ,a.USU_CTAFIN ,a.USU_CODCCU , a.USU_NUMPRJ , a.USU_CODFPJ , a.USU_USUGER , a.USU_DATGER ,
                                    a.USU_USUAPR , a.USU_DATAPR , a.USU_HORAPR ,a.USU_ROTNAP , a.USU_NUMAPR , a.USU_VLRAPR, $codnap codnap  
                                    FROM usu_tparagr a
                                    inner join e070fil b
                                    on a.usu_codemp = b.codemp
                                    and a.usu_codfil = b.codfil
                                    inner join e095for c
                                    on a.USU_CODFOR = c.CODFOR
                                    where 1=1
                                    and a.USU_USUAPR is null
                                    $condicao
                                    $condicao_ccu";
                    
                } else {
                    
                    $q1 = "SELECT distinct a.USU_CODEMP , a.USU_CODFIL ,b.SIGFIL,b.USU_INSTAN, a.USU_NUMTIT , a.USU_CODTPT ,a.USU_CODFOR , c.APEFOR, a.USU_DATEMI , a.USU_DATENT ,
                                    a.USU_OBSTCP , a.USU_VCTORI , a.USU_VLRORI ,
                                    round((a.USU_VLRORI / (SELECT d.vlrcot FROM e031imo d where a.USU_CODMOE = d.codmoe and a.USU_DATEMI = d.datmoe)) * (SELECT d.vlrcot FROM e031imo d where a.USU_CODMOE = d.codmoe and a.USU_DATENT = d.datmoe),2) vlrcorrigido,  
                                    a.USU_CODMOE , a.USU_DATPPT ,a.USU_CTAFIN ,a.USU_CODCCU , a.USU_NUMPRJ , a.USU_CODFPJ , a.USU_USUGER , a.USU_DATGER ,
                                    a.USU_USUAPR , a.USU_DATAPR , a.USU_HORAPR ,a.USU_ROTNAP , a.USU_NUMAPR , a.USU_VLRAPR, $codnap codnap  
                                    FROM usu_tparagr a
                                    inner join e070fil b
                                    on a.usu_codemp = b.codemp
                                    and a.usu_codfil = b.codfil
                                    inner join e095for c
                                    on a.USU_CODFOR = c.CODFOR
                                    where 1=1
                                    and a.USU_USUAPR is null
                                    $condicao";
                    
                }
                
                $sql .= $q1.' union all ';
            }
            
            $sql = substr_replace($sql, '', -10);
            $query = $this->db->query("select distinct * from (".$sql.") order by 1,2,7,5");
                    
            $result = $query->result();
            
            return $result;
            
        } else {
            return false;
        }
    }
    
    
    function buscapar($dtini, $dtfim, $filial_list, $codfor_list, $situacao, $aprovador) {
        $condicao = '';
        
        if ($dtini != null and $dtfim != null) {
            $condicao .= "and USU_VCTORI between to_date('$dtini', 'dd/mm/yyyy') and to_date('$dtfim', 'dd/mm/yyyy')";
        }

        if ($filial_list != null) {
            $condicao .= "AND a.USU_CODFIL IN ($filial_list)";
        }

        if ($codfor_list != null) {
            $condicao .= "AND a.USU_CODFOR IN ($codfor_list)";
        } 
        
        if ($aprovador != null and $aprovador != '0') {
            $condicao .= "and a.USU_USUAPR = $aprovador";
        } 
        
        if ($situacao != null) {
            if ($situacao == 'APR') {
                $condicao .= "and a.USU_NUMAPR is not null";
            } else {
                $condicao .= "and a.USU_NUMAPR is null";
            }
        }
        
        //var_dump($condicao);
        $query = $this->db->query("SELECT distinct a.USU_CODEMP , a.USU_CODFIL ,b.SIGFIL,b.USU_INSTAN, a.USU_NUMTIT , a.USU_CODTPT ,a.USU_CODFOR , c.APEFOR, a.USU_DATEMI , a.USU_DATENT ,
                                    a.USU_OBSTCP , a.USU_VCTORI , a.USU_VLRORI ,
                                    round((a.USU_VLRORI / (SELECT d.vlrcot FROM e031imo d where a.USU_CODMOE = d.codmoe and a.USU_DATEMI = d.datmoe)) * (SELECT d.vlrcot FROM e031imo d where a.USU_CODMOE = d.codmoe and a.USU_DATENT = d.datmoe),2) vlrcorrigido,  
                                    a.USU_CODMOE , a.USU_DATPPT ,a.USU_CTAFIN ,a.USU_CODCCU , a.USU_NUMPRJ , a.USU_CODFPJ , a.USU_USUGER , a.USU_DATGER ,
                                    a.USU_USUAPR, usu.nomusu, a.USU_DATAPR , a.USU_HORAPR ,a.USU_ROTNAP , a.USU_NUMAPR , a.USU_VLRAPR 
                                    FROM usu_tparagr a
                                    inner join e070fil b
                                    on a.usu_codemp = b.codemp
                                    and a.usu_codfil = b.codfil
                                    inner join e095for c
                                    on a.USU_CODFOR = c.CODFOR
                                    left join E099USU usu
                                    on a.USU_USUAPR = usu.CODUSU
                                    and a.USU_CODEMP = usu.codemp
                                    where 1=1
                                    $condicao
                                    order by 1,2,7,5");

        if($query -> num_rows() > 0) {
                return $query->result();
        } else {
                return false;
        }
        
        
    }
}