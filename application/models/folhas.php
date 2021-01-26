<?php

class Folhas extends CI_Model {

    //VERIFICA STATUS DO APROVADOR
    function getApr($codemp, $coduser) {

        if ($codemp != null) {
            $condicao = "AND CODEMP IN ($codemp)";
        } else {
            $condicao = '';
        }

        $query = $this->db->query("SELECT CODEMP, ROTNAP, CODNAP FROM E068UNA WHERE ROTNAP = 68 AND CODUSU = $coduser AND SITUNA = 'A' $condicao order by codemp");

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
        $query = $this->db->query("SELECT usu_tnumapr_seq.nextval numapr FROM dual");
        
        return $query->result_array();
    }
    
    
    function busca($dtini, $dtfim, $filial_list, $codcif_list, $coduser, $codemp_list, $statusapr, $checkccu) {
        $condicao = '';

        if ($dtini != null and $dtfim != null) {
            $condicao .= "and p.vnctit between to_date('$dtini', 'dd/mm/yyyy') and to_date('$dtfim', 'dd/mm/yyyy')";
        }

        if ($filial_list != null) {
            $condicao .= "AND p.filsap IN ($filial_list)";
        }

        if ($codcif_list != null) {
            $condicao .= "AND c.codcif IN ('$codcif_list')";
        } else {
            $condicao .= "and c.codcif=decode(0,0, c.codcif, 0)";
        }
        $sql = '';
        //var_dump($checkccu);
        if ($checkccu) {
            foreach ($checkccu as $key => $row) {
                //$condicao_apr = '';
                //$condnivel = '';
                //var_dump($key);
                if ($statusapr) {
                    $codnap = $statusapr[$key]['CODNAP'];
                }
                
                switch ($key) {
                case 1:
                    $numemp = "and p.NUMEMP = 1";
                case 2:
                    $numemp = "and p.NUMEMP in (4,5,7,10,23)";
                case 3:
                    $numemp = "and p.NUMEMP in (3)";
                case 4:
                    $numemp = "and p.NUMEMP in (8)";
                case 5:
                    $numemp = "and p.NUMEMP in (6)";
                case 6:
                    $numemp = "and p.NUMEMP in (24)";
                case 7:
                    $numemp = "and p.NUMEMP in (25)";
                case 8:
                    $numemp = "and p.NUMEMP in (26)";
                case 9:
                    $numemp = "and p.NUMEMP in (27)";
                case 16:
                    $numemp = "and p.NUMEMP in (28)";
                default :
                    $numemp = '';
                }                
//                    if ($statusapr[$key]['CODNAP'] === 40) {
//                        $condnivel = "AND APR.NIVEXI LIKE '%40%'";
//                        //$condicao_apr = "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 30 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)
//			//								AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 40 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
//                    } else if ($statusapr[$key]['CODNAP'] === 50) {
//                        $condnivel = "AND APR.NIVEXI LIKE '%50%'";
//                        //$condicao_apr = "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 40 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)
//			//								AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 50 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
//                    } else {
//                        $condnivel = "AND APR.NIVEXI LIKE '%30%'";
//                        //$condicao_apr = "AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR IN (30,40,50) AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
//                    }
//                }

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
                                            $condicao_ccu .= "OR p.CODCCU IN (".ltrim(rtrim($cculista[$n],','),',').")";
                                    } else {
                                            $condicao_ccu .= "p.CODCCU IN (".ltrim(rtrim($cculista[$n],','),',').")";
                                    }
                                    //echo $cculista[$n];							
                            }
                    } else {
                            $condicao_ccu = "p.CODCCU IN ($ccu_list)";
                    }
                    
                    if ($condicao_ccu) {
                            $condccu = "AND (".$condicao_ccu.")";
                    } else {
                            $condccu ='';
                    }
                    
                    $q1 = "select fil.codemp, p.filsap, fil.sigfil, fil.USU_INSTAN, c.codcif, c.descif, p.vnctit, $codnap codnap,  count(*) qtdlan, sum(valrat) vlrpgt
                                            from vetorh.R047PER p, vetorh.r047cif c, E070fil fil, vetorh.R047PEN pen
                                            where p.codcif=c.codcif
                                            and p.filsap = fil.codfil
                                            and p.NUMEMP = pen.numemp
                                            and p.codfil = pen.codfil
                                            and p.codfor = pen.codfor
                                            and p.TIPTIS = pen.TIPTIS
                                            and p.codcal = pen.codcal
                                            and p.numcad = pen.numcad
                                            and p.codcif = pen.codcif
                                            and p.coddep = pen.coddep
                                            AND P.DHIPEN = PEN.DHIPEN
                                            and (p.CODTRS = ' ' or p.codtrs is null)
                                            and p.USU_APROVADO = 'S'
                                            $numemp
                                            $condicao
                                            $condicao_ccu                                             
                                            group by fil.codemp, p.filsap, fil.sigfil, fil.USU_INSTAN, c.codcif, c.descif, p.vnctit";
                                        
                    
                } else {
                    $q1 = "select fil.codemp, p.filsap, fil.sigfil, fil.USU_INSTAN, c.codcif, c.descif, p.vnctit, $codnap codnap, count(*) qtdlan, sum(valrat) vlrpgt
                                            from vetorh.R047PER p, vetorh.r047cif c, E070fil fil, vetorh.R047PEN pen
                                            where p.codcif=c.codcif
                                            and p.filsap = fil.codfil
                                            and p.NUMEMP = pen.numemp
                                            and p.codfil = pen.codfil
                                            and p.codfor = pen.codfor
                                            and p.TIPTIS = pen.TIPTIS
                                            and p.codcal = pen.codcal
                                            and p.numcad = pen.numcad
                                            and p.codcif = pen.codcif
                                            and p.coddep = pen.coddep
                                            AND P.DHIPEN = PEN.DHIPEN
                                            and (p.CODTRS = ' ' or p.codtrs is null)
                                            and p.USU_APROVADO = 'S'
                                            $numemp
                                            $condicao                                                                                         
                                            group by fil.codemp, p.filsap, fil.sigfil, fil.USU_INSTAN, c.codcif, c.descif, p.vnctit";                   
                }
                $sql .= $q1.' union all ';
            }
            //echo $sql;
            $sql = substr_replace($sql, '', -10);
            $query = $this->db->query("select distinct * from (".$sql.") order by 2,7,5");
                    
            $result = $query->result();
            
            return $result;
        } else {
            return false;
        }
    }
    
    
    function getItem($cif, $filial, $dtini, $dtfim, $dtvnc) {
                
        $condicao = "and p.vnctit = '$dtvnc'";
        
	
        $query = $this->db->query("select p.vnctit, p.filsap, fil.sigfil, fil.USU_INSTAN, c.codcif, c.descif, p.numcad, fun.nomfun, p.valrat vlrpgt
                                    from vetorh.R047PER p, vetorh.r047cif c, E070fil fil, vetorh.r034fun fun, vetorh.R047PEN pen
                                    where p.codcif=c.codcif
                                      and p.filsap = fil.codfil
                                      and p.filsap=$filial 
                                      $condicao
                                      and c.codcif=$cif
                                      and p.numemp = fun.numemp
                                      and p.numcad = fun.numcad
                                      and p.NUMEMP = pen.numemp
                                      and p.codfil = pen.codfil
                                      and p.codfor = pen.codfor
                                      and p.TIPTIS = pen.TIPTIS
                                      and p.codcal = pen.codcal
                                      and p.numcad = pen.numcad
                                      and p.codcif = pen.codcif
                                      and p.coddep = pen.coddep
                                      AND P.DHIPEN = PEN.DHIPEN                                      
                                     order by 1,2, 5, 8");

        if($query -> num_rows() > 0) {
                return $query->result();
        } else {
                return false;
        }

    }
    
    
    function buscafol($dtini, $dtfim, $filial_list, $codcif_list, $situacao, $aprovador) {
        $condicao = '';
        
        if ($dtini != null and $dtfim != null) {
            $condicao .= "and p.vnctit between to_date('$dtini', 'dd/mm/yyyy') and to_date('$dtfim', 'dd/mm/yyyy') ";
        }

        if ($filial_list != null) {
            $condicao .= "AND p.filsap IN ($filial_list)";
        }

        if ($codcif_list != null) {
            $condicao .= "AND c.codcif IN ($codcif_list)";
        } else {
            //$condicao .= "and c.codcif=decode(0,0, c.codcif, 0)";
        }
        
        if ($aprovador != null and $aprovador <> 0) {
            $condicao .= "and p.USU_CODUSU = $aprovador";
        }
        
        if ($situacao) {
            if ($situacao == 'APR') {
                $condicao .= "and p.CODTRS <> ' '";
            } else {
                $condicao .= "and (p.CODTRS = ' ' or p.codtrs is null)";
            }
        }
        
        //var_dump($condicao);
        $query = $this->db->query("select fil.codemp, p.filsap, fil.sigfil, fil.USU_INSTAN, c.codcif, c.descif, nvl(p.USU_CODUSU,0) USU_CODUSU, usu.nomusu, p.vnctit, count(*) qtdlan, sum(valrat) vlrpgt
                                            from vetorh.R047PER p
                                            inner join vetorh.r047cif c
                                            on p.codcif=c.codcif
                                            inner join E070fil fil
                                            on p.filsap = fil.codfil
                                            left join vetorh.R047PEN pen
                                            on p.NUMEMP = pen.numemp
                                            and p.codfil = pen.codfil
                                            and p.codfor = pen.codfor
                                            and p.TIPTIS = pen.TIPTIS
                                            and p.codcal = pen.codcal
                                            and p.numcad = pen.numcad
                                            and p.codcif = pen.codcif
                                            and p.coddep = pen.coddep
                                            AND P.DHIPEN = PEN.DHIPEN
                                            left join E099USU usu
                                            on p.USU_CODUSU = usu.CODUSU
                                            and fil.codemp = usu.codemp                                                                                        
                                            where 1=1                  
                                            and p.USU_APROVADO = 'S'
                                            $condicao                                                                                         
                                            group by fil.codemp, p.filsap, fil.sigfil, fil.USU_INSTAN, c.codcif, c.descif, nvl(p.USU_CODUSU,0), usu.nomusu, p.vnctit order by 2,9,5");

        if($query -> num_rows() > 0) {
                return $query->result();
        } else {
                return false;
        }
        
        
    }

}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

