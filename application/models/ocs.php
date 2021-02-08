<?php
Class Ocs extends CI_Model {
	
	//PEGA EMPRESAS
	function getEmp($filial) {
		
		if ($filial != null){
			$condicao = "WHERE CODFIL IN ($filial)";
		} else {
			$condicao = '';
		}
		
		$query = $this->db->query("SELECT distinct CODEMP FROM E070FIL $condicao");
		
		if($query -> num_rows() > 0) {
			
			foreach($query->result_array() as $row)	{
				$codemp[$row['CODEMP']] = $row['CODEMP'];
			}
			return $codemp;
		} else {
			return false;
		}
	}
	
	function checkoc($emp, $fil, $oc) {
			
		$query = $this->db->query("SELECT * FROM USU_TOCPFEC where usu_tcoemp = $emp AND usu_tcodfil = $fil AND usu_tnumocp = $oc");
	
		if($query -> num_rows() > 0) {						
			return true;
		} else {
			return false;
		}
	}
	
	//VERIFICA STATUS DO APROVADOR
	function getApr($codemp, $coduser) {
		
		if ($codemp != null){
			$condicao = "AND CODEMP IN ($codemp)";
		} else {
			$condicao = '';
		}
		
		$query = $this->db->query("SELECT CODEMP, ROTNAP, CODNAP FROM E068UNA WHERE ROTNAP = 12 AND CODUSU = $coduser AND SITUNA = 'A' $condicao order by codemp");
		
		if($query -> num_rows() > 0) {
			
			foreach($query->result_array() as $row)	{
				$apr[$row['CODEMP']][] = array(
						'CODEMP' => $row['CODEMP'],
						'ROTNAP' => $row['ROTNAP'],
						'CODNAP' => $row['CODNAP']
						);
			}
			return $apr;
		} else {
			return false;
		}
		
<<<<<<< HEAD
	}
        
       /* function getApr($codemp, $coduser) {  ORIGINAL
		
		if ($codemp != null){
			$condicao = "AND CODEMP IN ($codemp)";
		} else {
			$condicao = '';
		}
		
		$query = $this->db->query("SELECT CODEMP, ROTNAP, CODNAP FROM E068UNA WHERE ROTNAP = 12 AND CODUSU = $coduser AND SITUNA = 'A' $condicao order by codemp");
		
		if($query -> num_rows() > 0) {
			
			foreach($query->result_array() as $row)	{
				$apr[$row['CODEMP'] = array(
						'CODEMP' => $row['CODEMP'],
						'ROTNAP' => $row['ROTNAP'],
						'CODNAP' => $row['CODNAP']
						);
			}
			return $apr;
		} else {
			return false;
		}
		
	}*/
	
=======
	}              	
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
	
	//VERIFICA OS CENTROS DE CUSTO DO APROVADOR
	function getCcu($datapr, $coduser) {		
		//var_dump($datapr);		
		foreach ($datapr as $row) {
                    foreach ($row as $r) {
			//var_dump($r);
			//echo $row['CODEMP'];
			$query = $this->db->query("SELECT CODCCU FROM E068CNA
										WHERE CODEMP = ".$r['CODEMP']." 
											AND ROTNAP = ".$r['ROTNAP']." 
											AND CODNAP = ".$r['CODNAP']." 
											AND CODUSU = $coduser
											AND LENGTH(CODCCU) >=1
											AND SITCNA = 'A'");
			
			if($query->num_rows() > 0) {
				 $codccu[$r['CODEMP']][$r['CODNAP']] = $query->result();
			} else {
				$codccu[$r['CODEMP']][$r['CODNAP']] = false;
			}
                    }
		}		
		return $codccu;		
	}
        
        //---------------------------------------------------ORIGINAL----------------------------------------------------------
//        function getCcu($datapr, $coduser) {		
//		//var_dump($datapr);		
//		foreach ($datapr as $row) {			
//			//var_dump($row);
//			//echo $row['CODEMP'];
//			$query = $this->db->query("SELECT CODCCU, CODNAP FROM E068CNA
//										WHERE CODEMP = ".$row['CODEMP']." 
//											AND ROTNAP = ".$row['ROTNAP']." 
//											AND CODNAP = ".$row['CODNAP']." 
//											AND CODUSU = $coduser
//											AND LENGTH(CODCCU) >=1
//											AND SITCNA = 'A'");
//			
//			if($query->num_rows() > 0) {
//				 $codccu[$row['CODEMP']] = $query->result();
//			} else {
//				$codccu[$row['CODEMP']] = false;
//			}			
//		}		
//		return $codccu;		
//	}
	
	function getCcuCom($codemp, $coduser) {
		//$i=0;
		foreach ($codemp as $row) {
			//var_dump($row);
			$query = $this->db->query("SELECT CODCCU FROM e099uxu
					WHERE CODEMP = $row				
					AND CODUSU = $coduser					
					AND SITUXU = 'A'");
				
			if($query->num_rows() > 0) {
				$codccu[$row] = $query->result();
			} else {
				$codccu[$row] = false;
			}
			
		//$i++;
		}
		return $codccu;
	}
	
	
	function busca($dtini, $dtfim, $contapr, $filial_list, $pedido, $codfor_list, $coduser, $codemp_list, $statusapr, $checkccu, $operacao) {
		
		//var_dump($statusapr);
				
		$condicao = '';
		
		if($dtini != null and $dtfim != null) {
			$condicao .= "AND OC.DATEMI between to_date('$dtini', 'dd/mm/yyyy') and to_date('$dtfim', 'dd/mm/yyyy')";
		}
		
		if ($contapr != null) {
			$condicao .= "AND OC.SITAPR = '$contapr'";
		}
		
		if ($filial_list != null) {
			$condicao .= "AND OC.CODFIL IN ($filial_list)";
		}
		
		if ($codfor_list != null) {
			$condicao .= "AND OC.CODFOR IN ($codfor_list)";
		}
		
		if($pedido != null) {
<<<<<<< HEAD
                    if ($contapr != null) {
			$condicao = "AND OC.NUMOCP = $pedido AND OC.SITAPR = '$contapr'";
                    } else {
                        $condicao = "AND OC.NUMOCP = $pedido";
                    }
		}
                //var_dump($condicao);
                $condnivel = '';
                $condicao_apr = '';
		$sql = '';
                if ($checkccu) {
                    //var_dump($checkccu);
                    foreach ($checkccu as $key =>$row) {
                        //var_dump($key);                        
                        //var_dump($row);
=======
			if ($contapr != null) {
				$condicao = "AND OC.NUMOCP = $pedido AND OC.SITAPR = '$contapr'";
			} else {
				$condicao = "AND OC.NUMOCP = $pedido";
			}
		}
                //var_dump($condicao);
		$condnivel = '';
		$condicao_apr = '';
		$sql = '';
		var_dump($statusapr);
		exit;
			if ($checkccu) {
                    //var_dump($checkccu);
                    foreach ($checkccu as $key =>$row) {
                        //var_dump($key);                        
						var_dump($row);
						if ($row[1] <> false) {
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
                        foreach ($row as $rnap => $r) {
                            //var_dump($r);
                            $condicao_ccu = '';
                            if ($rnap == 25) {
                                $condnivel = " AND APR.NIVEXI LIKE '%25%'";
                                $condicao_apr = "AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR IN (25,30,40,50) AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                
                                
                            } elseif ($rnap == 30) {
                                $condnivel = " AND APR.NIVEXI LIKE '%30%'";
                                $condicao_apr = "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 25 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)
                                                 AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = $rnap AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                
                                if ($r) {
                                    $condicao_ccu = $this->arraytostring($r);                                    
                                }
                                
                            } elseif ($rnap == 40) {
                                $condnivel = "AND APR.NIVEXI LIKE '%40%'";
                                $condicao_apr = "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 30 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)
											AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = $rnap AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                if ($r) {
                                    $condicao_ccu = $this->arraytostring($r);                                    
                                }
                            } elseif ($rnap == 50) {
                                $condnivel = "AND APR.NIVEXI LIKE '%50%'";
                                $condicao_apr = "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 40 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)
											AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = $rnap AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                if ($r) {
                                    $condicao_ccu = $this->arraytostring($r);
                                }
                            } elseif ($rnap == 70) {
                                $condnivel = " AND APR.NIVEXI LIKE '%70%'";
                                $condicao_apr = "AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR IN (70) AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                //var_dump($r);
                                if ($r) {
                                    $condicao_ccu = $this->arraytostring($r);
                                }
                            } elseif ($rnap == 80) {
                                $condnivel = " AND APR.NIVEXI LIKE '%80%'";
                                $condicao_apr = "AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR IN (80) AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                if ($r) {                                
                                    $condicao_ccu = $this->arraytostring($r);
                                }
                            } 
                            
                            if ($operacao == 'CAP') {
                                $condnivel = '';
                                $condicao_apr = "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR IN (25,30,40,50,70) AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                            }
                            
                            //var_dump($condicao_ccu);
                            if ($condicao_ccu) {
                                $condccu = "AND (".$condicao_ccu.")";
                            } else {
                                $condccu ='';
                            }
                            
                            //exit();                                                                                                                        
                            $q1 = "select DISTINCT OC.CODEMP,
                                        OC.CODFIL,
<<<<<<< HEAD
                                        FIL.SIGFIL,
                                        FIL.USU_INSTAN,								
=======
                                        FIL.SIGFIL,                                        							
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
                                        OC.NUMOCP,
                                        TO_CHAR(OC.DATEMI, 'DD/MM/YYYY') DATEMI,
                                        OC.SITOCP,
                                        OC.CODFOR,
                                        FORN.APEFOR,
                                        OC.OBSOCP,
                                        OC.VLRLIQ,
                                        OC.CODUSU,
                                        USU.NOMUSU,
                                        OC.ROTNAP,
                                        OC.NUMAPR,
                                        OC.SITAPR,                                        
                                        APR.NIVEXI,
                                        OC.TEMPAR,
                                        OC.CODCPG,
                                        CP.DESCPG,
                                        TO_CHAR(PR.VCTPAR,'DD/MM/YYYY') VCTPAR,
                                        PR.VLRPAR,
                                        $rnap CODNAPAPR,
<<<<<<< HEAD
                                        (SELECT ICP.DIAPAR FROM E028ICP ICP WHERE OC.CODEMP = ICP.CODEMP AND OC.CODCPG = ICP.CODCPG AND ICP.SEQICP = 1) DIAPAR,
                                        NVL((select distinct V.numpct from USU_VCOTOC V where V.numocp = OC.NUMOCP and V.filocp = OC.CODFIL),0) NUMPCT,
=======
                                        (SELECT ICP.DIAPAR FROM E028ICP ICP WHERE OC.CODEMP = ICP.CODEMP AND OC.CODCPG = ICP.CODCPG AND ICP.SEQICP = 1) DIAPAR,                                        
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
                                        nvl((select max(seqobs) from e420obs o where o.numocp = OC.NUMOCP and o.codfil = OC.CODFIL),0) seqobs
                                        from E420OCP OC
                                        INNER JOIN E420RAT RAT
                                        ON OC.CODEMP = RAT.CODEMP
                                        AND OC.CODFIL = RAT.CODFIL
                                        AND OC.NUMOCP = RAT.NUMOCP
                                        INNER JOIN E095FOR FORN
                                        ON OC.CODFOR = FORN.CODFOR
                                        INNER JOIN R999USU USU
                                        ON OC.CODUSU = USU.CODUSU
                                        INNER JOIN E070FIL FIL
                                        ON OC.CODEMP = FIL.CODEMP
                                        AND OC.CODFIL = FIL.CODFIL
                                        INNER JOIN E614APR APR
                                        ON OC.CODEMP = APR.CODEMP
                                        AND OC.ROTNAP = APR.ROTNAP
                                        AND OC.NUMAPR = APR.NUMAPR
                                        $condnivel
                                        LEFT JOIN e028cpg CP
                                        ON OC.CODEMP = CP.CODEMP
                                        AND OC.CODCPG = CP.CODCPG
                                        LEFT JOIN E420PAR PR
                                        ON OC.CODEMP = PR.CODEMP
                                        AND OC.CODFIL = PR.CODFIL
                                        AND OC.NUMOCP = PR.NUMOCP
                                        AND PR.SEQPAR = 1
                                        where OC.SITOCP = 9	
                                        AND OC.CODEMP = $key							   
                                        $condccu
                                        $condicao
                                        $condicao_apr
                                        GROUP BY OC.CODEMP,
                                        OC.CODFIL,
<<<<<<< HEAD
                                        FIL.SIGFIL,
                                        FIL.USU_INSTAN,
=======
                                        FIL.SIGFIL,                                        
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
                                        OC.NUMOCP,
                                        OC.DATEMI,
                                        OC.SITOCP,
                                        OC.CODFOR,
                                        FORN.APEFOR,
                                        OC.OBSOCP,
                                        OC.VLRLIQ,
                                        OC.CODUSU,
                                        USU.NOMUSU,
                                        OC.ROTNAP,
                                        OC.NUMAPR,
                                        OC.SITAPR,                                        
                                        APR.NIVEXI,
                                        OC.TEMPAR,
                                        OC.CODCPG,
                                        CP.DESCPG,
                                        PR.VCTPAR,
                                        PR.VLRPAR
                                        ";
                            //echo $q1.'<br><br>';
                            //echo $condnivel.'<br>';
                            //echo $condicao_apr.'<br>';                             
                            //echo $q1;                                                      
                            $sql .= $q1.' union all ';
                            //echo $sql;
<<<<<<< HEAD
                        }                                                   
=======
						}   
						}                                                
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
                    }
                    $sql = substr_replace($sql, '', -10);
                    $query = $this->db->query("SELECT DISTINCT * FROM (".$sql ." ) ORDER BY 1,2,5");
                    
                    $result = $query->result();
                    
                    if ($result) {
                        return $result;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }                						
	}
                                
        
        function buscacon($dtini, $dtfim, $contapr, $filial_list, $pedido, $codfor_list, $codemp, $situacao, $aprovador) {
		
		//var_dump($aprovador);
				
		$condicao = '';
                $inner = '';
		
		if($dtini != null and $dtfim != null) {
			$condicao .= "AND OC.DATEMI between to_date('$dtini', 'dd/mm/yyyy') and to_date('$dtfim', 'dd/mm/yyyy')";
		}
		
		if ($contapr != null) {
			$condicao .= "AND OC.SITAPR = '$contapr'";
		}
		
		if ($filial_list != null) {
			$condicao .= "AND OC.CODFIL IN ($filial_list)";
		}
		
		if ($codfor_list != null) {
			$condicao .= "AND OC.CODFOR IN ($codfor_list)";
		}
                
                if ($situacao != null) {
			$condicao .= "AND OC.SITOCP = $situacao";
		}
                
                if ($aprovador !== "0") {
                    $inner = "INNER JOIN E068CNA CNA
                                ON OC.CODEMP = CNA.CODEMP
                                AND OC.ROTNAP = CNA.ROTNAP
                                AND RAT.CODCCU = CNA.CODCCU
                                AND APR.NIVEXI LIKE '%' || CNA.CODNAP || '%'
                                AND CNA.SITCNA = 'A'
                                INNER JOIN E068NAP NAP
                                ON CNA.CODEMP = NAP.CODEMP
                                AND CNA.ROTNAP = NAP.ROTNAP
                                AND CNA.CODNAP = NAP.CODNAP";
                    
                    $condicao .= " and CNA.CODUSU = $aprovador 
                                    AND CNA.CODNAP NOT IN (SELECT APR.NIVAPR FROM e614usu APR WHERE APR.CODEMP = OC.CODEMP AND APR.ROTNAP = OC.ROTNAP AND APR.NUMAPR = OC.NUMAPR AND APR.SITAPR = 'APR')
                                    AND OC.SITOCP = 9";
                }
		
		if($pedido != null) {
                    if ($contapr != null) {
			$condicao = "AND OC.NUMOCP = $pedido AND OC.SITAPR = '$contapr'";
                    } else {
                        $condicao = "AND OC.NUMOCP = $pedido";
                    }
		}                
		
                //var_dump($condicao);
                //exit();
		if ($codemp) {
			foreach ($codemp as $key ) {
				//echo $key;				
				//var_dump($key);				
				//exit();								
					$q1 = $this->db->query("select OC.CODEMP,
							OC.CODFIL,
<<<<<<< HEAD
							FIL.SIGFIL,
							FIL.USU_INSTAN,
							OC.NUMOCP,                                                        
							TO_CHAR(OC.DATEMI, 'DD/MM/YYYY') DATEMI,
                                                        OC.SITOCP,
=======
							FIL.SIGFIL,							
							OC.NUMOCP,                                                        
							TO_CHAR(OC.DATEMI, 'DD/MM/YYYY') DATEMI,
							OC.SITOCP,
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
							OC.CODFOR,
							FORN.APEFOR,
							OC.OBSOCP,
							OC.VLRLIQ,
<<<<<<< HEAD
                                                        OC.VLRORI,
=======
							OC.VLRORI,
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
							OC.CODUSU,
							USU.NOMUSU,
							OC.ROTNAP,
							OC.NUMAPR,
							OC.SITAPR,
							OC.SITOCP,
							APR.NIVEXI,
							OC.TEMPAR,
							OC.CODCPG,
							CP.DESCPG,
							TO_CHAR(PR.VCTPAR,'DD/MM/YYYY') VCTPAR,
<<<<<<< HEAD
                                                        PR.VLRPAR,
                                                        (SELECT ICP.DIAPAR FROM E028ICP ICP WHERE OC.CODEMP = ICP.CODEMP AND OC.CODCPG = ICP.CODCPG AND ICP.SEQICP = 1) DIAPAR,
                                                        NVL((select distinct V.numpct from USU_VCOTOC V where V.numocp = OC.NUMOCP and V.filocp = OC.CODFIL),0) NUMPCT                                        
=======
							PR.VLRPAR,
							(SELECT ICP.DIAPAR FROM E028ICP ICP WHERE OC.CODEMP = ICP.CODEMP AND OC.CODCPG = ICP.CODCPG AND ICP.SEQICP = 1) DIAPAR
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
							from E420OCP OC
							INNER JOIN E420RAT RAT
							ON OC.CODEMP = RAT.CODEMP
							AND OC.CODFIL = RAT.CODFIL
							AND OC.NUMOCP = RAT.NUMOCP
							INNER JOIN E095FOR FORN
							ON OC.CODFOR = FORN.CODFOR
							INNER JOIN R999USU USU
							ON OC.CODUSU = USU.CODUSU
							INNER JOIN E070FIL FIL
							 ON OC.CODEMP = FIL.CODEMP
							 AND OC.CODFIL = FIL.CODFIL
							LEFT JOIN E614APR APR
	 						ON OC.CODEMP = APR.CODEMP
	 						AND OC.ROTNAP = APR.ROTNAP
	 						AND OC.NUMAPR = APR.NUMAPR
							LEFT JOIN e028cpg CP
							ON OC.CODEMP = CP.CODEMP
							AND OC.CODCPG = CP.CODCPG
							LEFT JOIN E420PAR PR
<<<<<<< HEAD
                                                        ON OC.CODEMP = PR.CODEMP
                                                        AND OC.CODFIL = PR.CODFIL
                                                        AND OC.NUMOCP = PR.NUMOCP
                                                        AND PR.SEQPAR = 1
                                                        $inner
=======
							ON OC.CODEMP = PR.CODEMP
							AND OC.CODFIL = PR.CODFIL
							AND OC.NUMOCP = PR.NUMOCP
							AND PR.SEQPAR = 1
							$inner
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
							where OC.CODEMP = $key						
							$condicao							
							GROUP BY OC.CODEMP,
							OC.CODFIL,
<<<<<<< HEAD
							FIL.SIGFIL,
							FIL.USU_INSTAN,
							OC.NUMOCP,
							OC.DATEMI,
                                                        OC.SITOCP,
=======
							FIL.SIGFIL,							
							OC.NUMOCP,
							OC.DATEMI,
							OC.SITOCP,
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
							OC.CODFOR,
							FORN.APEFOR,
							OC.OBSOCP,
							OC.VLRLIQ,
<<<<<<< HEAD
                                                        OC.VLRORI,
=======
							OC.VLRORI,
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
							OC.CODUSU,
							USU.NOMUSU,
							OC.ROTNAP,
							OC.NUMAPR,
							OC.SITAPR,
							OC.SITOCP,
							APR.NIVEXI,
							OC.TEMPAR,
							OC.CODCPG,
							CP.DESCPG,
							PR.VCTPAR,
<<<<<<< HEAD
                                                        PR.VLRPAR
=======
							PR.VLRPAR
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
							ORDER BY 1,2,5");
					$query[$key] = $q1->result();
					//return false;
				//}
                                        //var_dump($q1);
			}
			
			return $query;
		} else {
			return false;
		}
		//exit();		
		//var_dump($query);												
	}
	
	function getItemPed($pedido, $filial) {
	
		$query = $this->db->query("SELECT CODEMP,
                                            CODFIL,
                                            NUMOCP,
                                            SEQIT,
                                            PROSER,
                                            UNIMED,
                                            DESpro DESCRI,
                                            PREUNI,
                                            QTDPED,
                                            QTDREC,
                                            DATGER,
                                            NUMNFC
                                            FROM (SELECT a.codemp, a.codfil, a.numocp, B.SEQIPO SEQIT, b.codpro PROSER, b.unimed, d.despro, b.preuni, b.qtdped, B.QTDREC, E.DATGER, E.NUMNFC  FROM e420ocp a
                                            inner join e420ipo b
                                            on a.codemp = b.codemp
                                            and a.codfil = b.codfil
                                            and a.numocp = b.numocp
                                            inner join e075pro d
                                            on b.codemp = d.codemp
                                            and b.codpro = d.codpro
                                            LEFT JOIN E440IPC E
                                            ON A.CODFIL = E.FILOCP
                                            AND A.NUMOCP = E.NUMOCP
                                            AND b.SEQIPO = E.SEQIPO
                                            union
                                            SELECT a.codemp, a.codfil, a.numocp, C.SEQISO SEQIT, c.codser PROSER, c.unimed, e.desser, C.PREUNI, C.QTDPED, C.QTDREC, F.DATGER, F.NUMNFC FROM e420ocp a
                                            inner join e420iso c
                                            on a.codemp = c.codemp
                                            and a.codfil = c.codfil
                                            and a.numocp = c.numocp
                                            inner join E080SER e
                                            on a.codemp = e.codemp
                                            and c.codser = e.CODSER
                                            LEFT JOIN E440ISC F
                                            ON A.CODFIL = F.FILOCP
                                            AND A.NUMOCP = F.NUMOCP
                                            AND c.SEQISO = F.SEQISO)
                                            WHERE NUMOCP   = $pedido
                                            AND CODFIL     = $filial");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	
	}
	
	function getAprovador($codemp, $numapr, $rotnap) {
		
		$query = $this->db->query("select USU.CODEMP, USU.ROTNAP, NAP.DESNAP, USU.SITAPR, USU.NUMAPR, USU.USUAPR, USA.NOMUSU, USU.NIVAPR, APR.NIVEXI
										  from E614USU USU
										  INNER JOIN E068NAP NAP
										  ON USU.CODEMP = NAP.CODEMP
										  AND USU.ROTNAP = NAP.ROTNAP
										  AND USU.NIVAPR = NAP.CODNAP
<<<<<<< HEAD
										  INNER JOIN R999USU USA
=======
										  INNER JOIN e099usu USA
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
										  ON USU.USUAPR = USA.CODUSU
										  INNER JOIN E614APR APR
										  ON USU.CODEMP = APR.CODEMP
										  AND USU.ROTNAP = APR.ROTNAP
										  AND USU.NUMAPR = APR.NUMAPR
										 where USU.codemp = $codemp
										   and USU.numapr = $numapr
										   and USU.rotnap = $rotnap");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	
	}
	
	function getPendente($codemp, $numapr, $rotnap, $numocp, $codfil) {
<<<<<<< HEAD
	
		$query = $this->db->query("SELECT DISTINCT OC.CODEMP,
                OC.CODFIL,
                OC.NUMOCP,
                OC.NUMAPR,
                APR.NIVEXI,                
                CNA.CODNAP,
                NAP.DESNAP,
                USU.CODUSU,
                USU.NOMUSU
			  FROM E420OCP OC
			 INNER JOIN E614APR APR
			    ON OC.CODEMP = APR.CODEMP
			   AND OC.ROTNAP = APR.ROTNAP
			   AND OC.NUMAPR = APR.NUMAPR
			 INNER JOIN E420RAT RAT
			    ON OC.CODEMP = RAT.CODEMP
			   AND OC.CODFIL = RAT.CODFIL
			   AND OC.NUMOCP = RAT.NUMOCP
			 INNER JOIN E068CNA CNA
			    ON OC.CODEMP = CNA.CODEMP
			   AND OC.ROTNAP = CNA.ROTNAP
			   AND RAT.CODCCU = CNA.CODCCU
			   AND APR.NIVEXI LIKE '%' || CNA.CODNAP || '%'
			   AND CNA.SITCNA = 'A'
			 INNER JOIN E068NAP NAP
			    ON CNA.CODEMP = NAP.CODEMP
			   AND CNA.ROTNAP = NAP.ROTNAP
			   AND CNA.CODNAP = NAP.CODNAP
			 INNER JOIN R999USU USU
			    ON USU.CODUSU = CNA.CODUSU
			 WHERE OC.NUMOCP = $numocp
			   AND OC.CODFIL = $codfil
			   AND OC.NUMAPR = $numapr
			   AND OC.CODEMP = $codemp
			   AND OC.ROTNAP = $rotnap
			   AND CNA.CODNAP NOT IN (SELECT APR.NIVAPR FROM e614usu APR WHERE APR.CODEMP = OC.CODEMP 
         								AND APR.ROTNAP = OC.ROTNAP AND APR.NUMAPR = OC.NUMAPR AND APR.SITAPR = 'APR')
			 ORDER BY 1, 2, 3, 6, 7");
=======
		

		$query = $this->db->query("SELECT * FROM (SELECT DISTINCT OC.CODEMP,
		OC.CODFIL,
		OC.NUMOCP,
		OC.NUMAPR,
		APR.NIVEXI,                
		UNA.CODNAP,
		NAP.DESNAP,
		USU.CODUSU,
		USU.NOMUSU,
		OC.ROTNAP
	  FROM E420OCP OC
	 INNER JOIN E614APR APR
		ON OC.CODEMP = APR.CODEMP
	   AND OC.ROTNAP = APR.ROTNAP
	   AND OC.NUMAPR = APR.NUMAPR      
	 INNER JOIN E420RAT RAT
		ON OC.CODEMP = RAT.CODEMP
	   AND OC.CODFIL = RAT.CODFIL
	   AND OC.NUMOCP = RAT.NUMOCP
INNER JOIN E068UNA UNA
ON OC.CODEMP = UNA.CODEMP
AND OC.ROTNAP = UNA.ROTNAP        
AND SITUNA = 'A'
INNER JOIN E068NAP NAP
		ON UNA.CODEMP = NAP.CODEMP
	   AND UNA.ROTNAP = NAP.ROTNAP
	   AND UNA.CODNAP = NAP.CODNAP
 AND APR.NIVEXI LIKE '%' || NAP.CODNAP || '%'
	 INNER JOIN E099USU USU
		ON USU.CODUSU = UNA.CODUSU          
union all       
SELECT DISTINCT OC.CODEMP,
		OC.CODFIL,
		OC.NUMOCP,
		OC.NUMAPR,
		APR.NIVEXI,                
		CNA.CODNAP,
		NAP.DESNAP,
		USU.CODUSU,
		USU.NOMUSU,
		OC.ROTNAP
	  FROM E420OCP OC
	 INNER JOIN E614APR APR
		ON OC.CODEMP = APR.CODEMP
	   AND OC.ROTNAP = APR.ROTNAP
	   AND OC.NUMAPR = APR.NUMAPR      
	 INNER JOIN E420RAT RAT
		ON OC.CODEMP = RAT.CODEMP
	   AND OC.CODFIL = RAT.CODFIL
	   AND OC.NUMOCP = RAT.NUMOCP   
INNER JOIN E068CNA CNA
		ON OC.CODEMP = CNA.CODEMP
	   AND OC.ROTNAP = CNA.ROTNAP
	   AND RAT.CODCCU = CNA.CODCCU
	   AND APR.NIVEXI LIKE '%' || CNA.CODNAP || '%'
	   AND CNA.SITCNA = 'A'
	 INNER JOIN E068NAP NAP
		ON CNA.CODEMP = NAP.CODEMP
	   AND CNA.ROTNAP = NAP.ROTNAP
	   AND CNA.CODNAP = NAP.CODNAP
	 LEFT JOIN E099USU USU
		ON USU.CODUSU = CNA.CODUSU) A

	 WHERE A.NUMOCP = $numocp
	   AND A.CODFIL = $codfil
	   AND A.NUMAPR = $numapr
	   AND A.CODEMP = $codemp
	   AND A.ROTNAP = $rotnap
	   AND A.CODNAP NOT IN (SELECT APR.NIVAPR FROM e614usu APR WHERE APR.CODEMP = A.CODEMP AND APR.ROTNAP = A.ROTNAP AND APR.NUMAPR = A.NUMAPR AND APR.SITAPR = 'APR')			
	ORDER BY 1, 2, 6, 8");
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	
	}
        
        function getNegocia($emp, $oc) {
            $query = $this->db->query("SELECT distinct cot.codfor, fo.apefor, cot.codpro item, pro.despro descricao, sol.numpct, cot.QTDCOT, REPLACE(cot.PRECOT,',','.') PRECOT FROM e410cot cot
                                        inner join e095for fo
                                        on cot.codfor = fo.codfor
                                        inner join e075pro pro
                                        on cot.codemp = pro.codemp 
                                        and cot.codpro = pro.codpro
                                        inner join e405sol sol
                                        on cot.codemp = sol.codemp
                                        and cot.numcot = sol.numcot
                                        where (cot.codemp, cot.numcot) in (SELECT codemp, numcot FROM E410LCO where codemp = $emp and NUMOCP = $oc)
                                        and cot.PRCCOT <> 5
                                        union all
                                        SELECT distinct cot.codfor, fo.apefor, cot.codser item, ser.desser descricao, sol.numpct, cot.QTDCOT, REPLACE(cot.PRECOT,',','.') PRECOT FROM e410cot cot
                                        inner join e095for fo
                                        on cot.codfor = fo.codfor
                                        inner join E080SER ser
                                        on cot.codemp = ser.codemp 
                                        and cot.codser = ser.codser
                                        inner join e405sol sol
                                        on cot.codemp = sol.codemp
                                        and cot.numcot = sol.numcot
                                        where (cot.codemp, cot.numcot) in (SELECT codemp, numcot FROM E410LCO where codemp = $emp and NUMOCP = $oc)
                                        and cot.PRCCOT <> 5");
            
            if($query -> num_rows() > 0) {
                return $query->result();
            } else {
                return false;
            }
        }
                
	function crud($sql, $dados) {
		$this->db->trans_begin();
	
		$this->db->query($sql, $dados);
	
		if ($this->db->trans_status() == FALSE) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return TRUE;
		}
	}
	
        function conbusca($dtini, $dtfim, $contapr, $filial_list, $pedido, $codfor_list, $coduser, $codemp_list, $statusapr, $checkccu) {
		
		//var_dump($statusapr);
				
		$condicao = '';
		
		if($dtini != null and $dtfim != null) {
			$condicao .= "AND OC.DATEMI between to_date('$dtini', 'dd/mm/yyyy') and to_date('$dtfim', 'dd/mm/yyyy')";
		}
		
		if ($contapr != null OR $contapr != '') {
			$condicao .= "AND OC.SITAPR = '$contapr'";
		}
		
		if ($filial_list != null) {
			$condicao .= "AND OC.CODFIL IN ($filial_list)";
		}
		
		if ($codfor_list != null) {
			$condicao .= "AND OC.CODFOR IN ($codfor_list)";
		}
		
		if($pedido != null) {
			$condicao = "AND OC.NUMOCP = $pedido OR OC.SITAPR = '$contapr'";
		}		
		
		if ($statusapr) {
			foreach ($statusapr as $st) {
				$stlist = $st['CODNAP'];
			}
			
			if ($stlist == 30) {
                            $condicao .= "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 25 AND USA.SITAPR = 'APR')";
                        } elseif ($stlist == 40) {
				$condicao .= "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 30 AND USA.SITAPR = 'APR')";
			} else if ($stlist == 50) {
				$condicao .= "AND OC.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR = 40 AND USA.SITAPR = 'APR')";
			}
			
			$condicao .= "AND OC.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = OC.CODEMP AND USA.ROTNAP = OC.ROTNAP AND USA.NUMAPR = OC.NUMAPR AND USA.NIVAPR IN ($stlist) AND USA.SITAPR = 'APR')";
		}
		
		if ($checkccu) {
			foreach ($checkccu as $key => $row) {
				//echo $key;			
				//var_dump($row);
				
				if ($row) {
					//echo $row->CODCCU;
					foreach ($row as $ccu) {
						//echo $ccu->CODCCU;					
						$cculist[] = $ccu->CODCCU;					
						
					}
					
					if (!empty($cculist)) {
						$ccu_list = rtrim(implode(',', $cculist), ',');
					} else {
						$ccu_list = '';
					}
					
					//var_dump(strlen($ccu_list));
					//var_dump($ccu_list);
					$condicao_ccu = '';
					$tamanho = strlen($ccu_list);
					if ($tamanho > 1000) {
						$numero = $tamanho/1000;
						$cculista = str_split($ccu_list, 1000);
						for ($n = 0; $n < $numero; $n++) {
							if ($n >=1) {
								$condicao_ccu .= "OR RAT.CODCCU IN (".ltrim(rtrim($cculista[$n],','),',').")";
							} else {
								$condicao_ccu .= "RAT.CODCCU IN (".ltrim(rtrim($cculista[$n],','),',').")";
							}
							//echo $cculista[$n];							
						}
					} else {
						$condicao_ccu = "RAT.CODCCU IN ($ccu_list)";
					}
					//echo $condicao_ccu;
					//echo $condicao;
					//exit();
					//var_dump($ccu_list);
					if ($condicao_ccu and !$pedido) {
						$condccu = "AND (".$condicao_ccu.")";
					} else {
						$condccu ='';
					}
					//echo $condccu;
					//echo $condicao;
					//exit();
					$q1 = $this->db->query("select OC.CODEMP,
								  OC.CODFIL,
                                                                  FIL.SIGFIL,
	 							  FIL.USU_INSTAN,								
                                                                  OC.NUMOCP,
                                                                  TO_CHAR(OC.DATEMI, 'DD/MM/YYYY') DATEMI,
                                                                  OC.CODFOR,
                                                                  FORN.APEFOR,
								  OC.OBSOCP,
								  OC.VLRLIQ,
								  OC.CODUSU,
                                                                  USU.NOMUSU,
								  OC.ROTNAP,
								  OC.NUMAPR,
								  OC.SITAPR,
								  OC.SITOCP,
								  APR.NIVEXI,
								  OC.TEMPAR,
								  OC.CODCPG,
								  CP.DESCPG,
								  TO_CHAR(PR.VCTPAR,'DD/MM/YYYY') VCTPAR,
                                                                  PR.VLRPAR,
                                                                  (select V.numpct from USU_VCOTOC V where V.numocp = OC.NUMOCP and V.filocp = OC.CODFIL) NUMPCT,
                                                                  (SELECT ICP.DIAPAR FROM E028ICP ICP WHERE OC.CODEMP = ICP.CODEMP AND OC.CODCPG = ICP.CODCPG AND ICP.SEQICP = 1) DIAPAR                                                                  
							from E420OCP OC
							INNER JOIN E420RAT RAT
							  ON OC.CODEMP = RAT.CODEMP
							  AND OC.CODFIL = RAT.CODFIL
							  AND OC.NUMOCP = RAT.NUMOCP
							INNER JOIN E095FOR FORN
	 						  ON OC.CODFOR = FORN.CODFOR
							LEFT JOIN R999USU USU
	 						  ON OC.CODUSU = USU.CODUSU
							INNER JOIN E070FIL FIL
	 						ON OC.CODEMP = FIL.CODEMP
	 						AND OC.CODFIL = FIL.CODFIL
							INNER JOIN E614APR APR
	 						ON OC.CODEMP = APR.CODEMP
	 						AND OC.ROTNAP = APR.ROTNAP
	 						AND OC.NUMAPR = APR.NUMAPR
							LEFT JOIN e028cpg CP
							ON OC.CODEMP = CP.CODEMP
							AND OC.CODCPG = CP.CODCPG
							LEFT JOIN E420PAR PR
                                                        ON OC.CODEMP = PR.CODEMP
                                                        AND OC.CODFIL = PR.CODFIL
                                                        AND OC.NUMOCP = PR.NUMOCP
                                                        AND PR.SEQPAR = 1
							where OC.CODEMP = $key
							   $condccu
							   $condicao
							GROUP BY OC.CODEMP,
							         OC.CODFIL,
									 FIL.SIGFIL,
									 FIL.USU_INSTAN,
								     OC.NUMOCP,
								     OC.DATEMI,
								     OC.CODFOR,
								     FORN.APEFOR,
									 OC.OBSOCP,
									 OC.VLRLIQ,
									 OC.CODUSU,
	          						 USU.NOMUSU,
									 OC.ROTNAP,
									 OC.NUMAPR,
									 OC.SITAPR,
									 OC.SITOCP,
									 APR.NIVEXI,
									 OC.TEMPAR,
									 OC.CODCPG,
									 CP.DESCPG,
									 PR.VCTPAR,
              					     PR.VLRPAR
							 ORDER BY 1,2,5");
					$query[$key] = $q1->result();
					
				} else {
					$q1 = $this->db->query("select OC.CODEMP,
							OC.CODFIL,
							FIL.SIGFIL,
							FIL.USU_INSTAN,
							OC.NUMOCP,
							TO_CHAR(OC.DATEMI, 'DD/MM/YYYY') DATEMI,
							OC.CODFOR,
							FORN.APEFOR,
							OC.OBSOCP,
							OC.VLRLIQ,
							OC.CODUSU,
							USU.NOMUSU,
							OC.ROTNAP,
							OC.NUMAPR,
							OC.SITAPR,
							OC.SITOCP,
							APR.NIVEXI,
							OC.TEMPAR,
							OC.CODCPG,
							CP.DESCPG,
							TO_CHAR(PR.VCTPAR,'DD/MM/YYYY') VCTPAR,
                                                        PR.VLRPAR,
                                                        (select V.numpct from USU_VCOTOC V where V.numocp = OC.NUMOCP and V.filocp = OC.CODFIL) NUMPCT,
                                                        (SELECT ICP.DIAPAR FROM E028ICP ICP WHERE OC.CODEMP = ICP.CODEMP AND OC.CODCPG = ICP.CODCPG AND ICP.SEQICP = 1) DIAPAR                                                        
							from E420OCP OC
							INNER JOIN E420RAT RAT
							ON OC.CODEMP = RAT.CODEMP
							AND OC.CODFIL = RAT.CODFIL
							AND OC.NUMOCP = RAT.NUMOCP
							INNER JOIN E095FOR FORN
							ON OC.CODFOR = FORN.CODFOR
							LEFT JOIN R999USU USU
							ON OC.CODUSU = USU.CODUSU
							INNER JOIN E070FIL FIL
							 ON OC.CODEMP = FIL.CODEMP
							 AND OC.CODFIL = FIL.CODFIL
							INNER JOIN E614APR APR
	 						ON OC.CODEMP = APR.CODEMP
	 						AND OC.ROTNAP = APR.ROTNAP
	 						AND OC.NUMAPR = APR.NUMAPR
							LEFT JOIN e028cpg CP
							ON OC.CODEMP = CP.CODEMP
							AND OC.CODCPG = CP.CODCPG
							LEFT JOIN E420PAR PR
			                ON OC.CODEMP = PR.CODEMP
			                AND OC.CODFIL = PR.CODFIL
			                AND OC.NUMOCP = PR.NUMOCP
			                AND PR.SEQPAR = 1
							where OC.CODEMP = $key						
							$condicao
							GROUP BY OC.CODEMP,
							OC.CODFIL,
							FIL.SIGFIL,
							FIL.USU_INSTAN,
							OC.NUMOCP,
							OC.DATEMI,
							OC.CODFOR,
							FORN.APEFOR,
							OC.OBSOCP,
							OC.VLRLIQ,
							OC.CODUSU,
							USU.NOMUSU,
							OC.ROTNAP,
							OC.NUMAPR,
							OC.SITAPR,
							OC.SITOCP,
							APR.NIVEXI,
							OC.TEMPAR,
							OC.CODCPG,
							CP.DESCPG,
							PR.VCTPAR,
                                                        PR.VLRPAR
							ORDER BY 1,2,5");
					$query[$key] = $q1->result();
					//return false;
					//echo $condicao;
				}
							
										
			}
			
			return $query;
		} else {
			return false;
		}
		//exit();		
		//echo $q1;						
						
            }
        
            function resumo_ccu($dtini, $dtfim, $filial_list) {
               
                
               $dtini = substr($dtini, 3, strlen($dtini));
               $dtfim = substr($dtfim, 3, strlen($dtfim));
               //var_dump($dtini);
               $condicao = "WHERE to_date(B.MESANO,'mm/yyyy') between to_date('$dtini', 'mm/yyyy') and to_date('$dtfim', 'mm/yyyy')";               
                
                if ($filial_list != null) {
                    $condicao .= "AND A.CODFIL IN ($filial_list)";
		}
                
<<<<<<< HEAD
                $query = $this->db->query("SELECT A.codfil, FIL.USU_INSTAN, A.claccu, translate( area.ABRCCU,
=======
                $query = $this->db->query("SELECT A.codfil, A.claccu, translate( area.ABRCCU,
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
                                            'ÁÇÉÍÓÚÀÈÌÒÙÂÊÎÔÛÃÕËÜáçéíóúàèìòùâêîôûãõëü',
                                            'ACEIOUAEIOUAEIOUAOEUaceiouaeiouaeiouaoeu')ABRCCU, A.vlrreal, B.vlrorc 
                                            FROM (SELECT to_char(ocp.datemi,'mm/yyyy') datemi, rat.codemp, rat.codfil, SUBSTR(ccu.claccu,1,1) claccu, replace(sum(rat.vlrrat),',','.') vlrreal 
                                            FROM e420rat rat
                                            inner join e420ocp ocp
                                            on rat.CODEMP = ocp.codemp
                                            and rat.codfil = ocp.codfil
                                            and rat.numocp = ocp.numocp
                                            inner join E044CCU ccu
                                            on rat.codemp = ccu.codemp
                                            and rat.codccu = ccu.codccu
                                            group by  to_char(ocp.datemi,'mm/yyyy'), rat.codemp, rat.codfil, SUBSTR(ccu.claccu,1,1)
                                            order by 1,2,3,4) A
                                            INNER JOIN (SELECT mesano, codemp, codfil, claccu, replace(sum(vlrcpi),',','.') vlrorc FROM (
                                            SELECT  to_char(orc.mesano,'mm/yyyy') mesano, orc.codemp, 
                                            case
                                                when LENGTH(orc.codemp) = 1 then SUBSTR(orc.numprj,1,3)
                                                when LENGTH(orc.codemp) = 2 then SUBSTR(orc.numprj,1,4)
                                                end codfil, orc.codccu, SUBSTR(ccu.claccu,1,1) claccu, orc.vlrcpi FROM e615orc orc
                                                inner join E044CCU ccu
                                                on orc.codemp = ccu.codemp
                                                and orc.codccu = ccu.codccu)
                                                group by mesano, codemp, codfil, claccu
                                                order by 1,2,3,4) B
                                            ON A.DATEMI = B.MESANO
                                            AND A.codemp = B.codemp
                                            AND A.codfil = B.codfil
                                            AND A.claccu = B.claccu
                                            INNER JOIN E070FIL FIL
                                            ON A.codemp = FIL.codemp
                                            AND A.codfil = FIL.codfil
                                            INNER JOIN E044CCU area
                                            ON A.CODEMP = area.CODEMP
                                            AND A.claccu = area.CLACCU
                                            $condicao ORDER BY 1,4");
	
		if($query -> num_rows() > 0) {						
			return $query->result();
		} else {
			return false;
		}
                
            }
            
            function resumo_cta($dtini, $dtfim, $filial_list) {
               
                
               $dtini = substr($dtini, 3, strlen($dtini));
               $dtfim = substr($dtfim, 3, strlen($dtfim));
               //var_dump($dtini);
               $condicao = "WHERE to_date(B.MESANO,'mm/yyyy') between to_date('$dtini', 'mm/yyyy') and to_date('$dtfim', 'mm/yyyy')";               
                
                if ($filial_list != null) {
                    $condicao .= "AND A.CODFIL IN ($filial_list)";
		}
                
<<<<<<< HEAD
                $query = $this->db->query("SELECT A.codfil, FIL.USU_INSTAN, A.clafin, translate( area.ABRCTA,
=======
                $query = $this->db->query("SELECT A.codfil, A.clafin, translate( area.ABRCTA,
>>>>>>> 3e2487da458faa8d13e32c3768c864bd97382f3e
                                            'ÁÇÉÍÓÚÀÈÌÒÙÂÊÎÔÛÃÕËÜáçéíóúàèìòùâêîôûãõëü',
                                            'ACEIOUAEIOUAEIOUAOEUaceiouaeiouaeiouaoeu')ABRCTA, A.vlrreal, B.vlrorc 
                                            FROM (SELECT to_char(ocp.datemi,'mm/yyyy') datemi, rat.codemp, rat.codfil, SUBSTR(cta.clafin,1,1) clafin, replace(sum(rat.vlrrat),',','.') vlrreal 
                                            FROM e420rat rat
                                            inner join e420ocp ocp
                                            on rat.CODEMP = ocp.codemp
                                            and rat.codfil = ocp.codfil
                                            and rat.numocp = ocp.numocp
                                            inner join e091plf cta
                                            on rat.codemp = cta.codemp
                                            and rat.ctafin = cta.ctafin
                                            group by  to_char(ocp.datemi,'mm/yyyy'), rat.codemp, rat.codfil, SUBSTR(cta.clafin,1,1)
                                            order by 1,2,3,4) A
                                            INNER JOIN 
                                            (SELECT mesano, codemp, codfil, clafin, replace(sum(vlrcpi),',','.') vlrorc FROM (
                                            SELECT  to_char(orc.mesano,'mm/yyyy') mesano, orc.codemp, 
                                            case
                                                when LENGTH(orc.codemp) = 1 then SUBSTR(orc.numprj,1,3)
                                                when LENGTH(orc.codemp) = 2 then SUBSTR(orc.numprj,1,4)
                                                end codfil, orc.ctafin, SUBSTR(cta.clafin,1,1) clafin, orc.vlrcpi FROM e615orc orc
                                                inner join e091plf cta
                                                on orc.codemp = cta.codemp
                                                and orc.ctafin = cta.ctafin)
                                                group by mesano, codemp, codfil, clafin
                                                order by 1,2,3,4) B
                                            ON A.DATEMI = B.MESANO
                                            AND A.codemp = B.codemp
                                            AND A.codfil = B.codfil
                                            AND A.clafin = B.clafin
                                            INNER JOIN E070FIL FIL
                                            ON A.codemp = FIL.codemp
                                            AND A.codfil = FIL.codfil
                                            INNER JOIN e091plf area
                                            ON A.CODEMP = area.CODEMP
                                            AND A.clafin = area.clafin
                                            $condicao ORDER BY 1,4");
	
		if($query -> num_rows() > 0) {						
			return $query->result();
		} else {
			return false;
		}
                
            }
            
            
            public function arraytostring($r) {
                $condicao_ccu = '';
                foreach ($r as $ccu) {                                    
                    $cculist[] = $ccu->CODCCU;					
                }                
                $ccu_list = rtrim(implode(',', $cculist), ',');
                $tamanho = strlen($ccu_list);
                if ($tamanho > 1000) {
                    $numero = $tamanho/1000;
                    $cculista = str_split($ccu_list, 1000);
                    for ($n = 0; $n < $numero; $n++) {
                        if ($n >=1) {
                            $condicao_ccu .= "OR RAT.CODCCU IN (".ltrim(rtrim($cculista[$n],','),',').")";
                        } else {
                            $condicao_ccu .= "RAT.CODCCU IN (".ltrim(rtrim($cculista[$n],','),',').")";
                        }
                            //echo $cculista[$n];							
                    }
                } else {
                        $condicao_ccu = "RAT.CODCCU IN ($ccu_list)";
                }
                return $condicao_ccu;
            }
		
}