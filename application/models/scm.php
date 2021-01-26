<?php
Class Scm extends CI_Model {
	
	function getAprSol($codemp, $coduser) {
	
		if ($codemp != null){
			$condicao = "AND CODEMP IN ($codemp)";
		} else {
			$condicao = '';
		}
	
		$query = $this->db->query("SELECT CODEMP, ROTNAP, CODNAP FROM E068UNA WHERE ROTNAP = 6 AND CODUSU = $coduser AND SITUNA = 'A' $condicao");
	
		if($query -> num_rows() > 0) {
			
			foreach($query->result_array() as $row)	{
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
	
	//VERIFICA OS CENTROS DE CUSTO DO APROVADOR
	function getCcuSol($datapr, $coduser) {
		//var_dump($datapr);
		foreach ($datapr as $row) {
	
			$query = $this->db->query("SELECT CODCCU, CODNAP FROM E068CNA
					WHERE CODEMP = ".$row['CODEMP']."
					AND ROTNAP = ".$row['ROTNAP']."
					AND CODNAP = ".$row['CODNAP']."
					AND CODUSU = $coduser
					AND LENGTH(CODCCU) >=1
					AND SITCNA = 'A'");
			
                        
			if($query->num_rows() > 0) {
				$codccu[$row['CODEMP']][$row['CODNAP']] = $query->result();
			} else {
				$codccu[$row['CODEMP']][$row['CODNAP']] = false;
			}
		}
		return $codccu;
	}
	
	function getCcuSolCom($codemp, $coduser) {
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
	
	function conbusca($tipo, $contapr, $filial_list, $produto, $servico, $coduser, $codemp_list, $statusapr, $checkccu, $sol) {
		ini_set('max_execution_time', 0);
		$condicao = '';		
                
		if ($tipo != null) {
			$condicao .= " AND E405SOL.PROSER = '$tipo'";
		}
		
		if ($contapr != null) {
			$condicao .= " AND E405SOL.SITAPR = '$contapr'";
		}
		
		if ($filial_list != null) {
			$condicao .= " AND E405SOL.FILSOL IN ($filial_list)";
		}
		
		if ($produto != null) {
			$condicao .= " AND E405SOL.CODPRO = '$produto'";
		}
		
		if ($servico != null) {
			$condicao .= " AND E405SOL.CODSER = '$servico'";
		}
		
		if ($sol != null) {
			$condicao = " AND E405SOL.NUMSOL = '$sol'";
		}
                //print_r($condicao);
                //print_r($checkccu);
                //var_dump($codemp_list);
                $condnivel = '';
                $condicao_apr = '';
		$sql = '';
		if ($checkccu) {
			foreach ($checkccu as $key => $row) {
				//var_dump($key);
				//var_dump($row);
				//print_r($statusapr);
                                foreach ($row as $rnap => $r) {
                                    //var_dump($rnap);
                                    //var_dump($r);
                                    if ($rnap == 30) {
                                        $condnivel = "AND E614APR.NIVEXI LIKE '%$rnap%'";
 					$condicao_apr = "AND E405SOL.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = E405SOL.CODEMP AND USA.ROTNAP = E405SOL.ROTNAP AND USA.NUMAPR = E405SOL.NUMAPR AND USA.NIVAPR = 20 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                    } elseif ($rnap == 40) {
                                        $condnivel = "AND E614APR.NIVEXI LIKE '%$rnap%'";
                                        $condicao_apr = "AND E405SOL.NUMAPR IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = E405SOL.CODEMP AND USA.ROTNAP = E405SOL.ROTNAP AND USA.NUMAPR = E405SOL.NUMAPR AND USA.NIVAPR = 30 AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                    } elseif ($rnap == 20) {
                                        $condnivel = "AND E614APR.NIVEXI LIKE '%$rnap%'";
                                        $condicao_apr = "AND E405SOL.NUMAPR NOT IN (SELECT USA.NUMAPR FROM E614USU USA WHERE USA.CODEMP = E405SOL.CODEMP AND USA.ROTNAP = E405SOL.ROTNAP AND USA.NUMAPR = E405SOL.NUMAPR AND USA.NIVAPR IN (20,30,40) AND USA.SITAPR = 'APR' AND USA.CODEMP = $key)";
                                    }
                                //}                                                                 								
				//var_dump($r);                                                              
				if ($r != false) {
					foreach ($r as $ccu) {                
                                            //echo count($ccu);
                                            $cculist[] = $ccu->CODCCU;                                            
                                            //$cculist[] = $ccu->CODCCU;                                            
					}
					//var_dump($cculist);
					if (!empty($cculist)) {
						$ccu_list = rtrim(implode("','", $cculist), "','");
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
								$condicao_ccu .= " OR E405RAT.CODCCU IN ('".ltrim(rtrim($cculista[$n],','),',')."')";
							} else {
								$condicao_ccu .= " E405RAT.CODCCU IN ('".ltrim(rtrim($cculista[$n],','),',')."')";
							}
							//echo $cculista[$n];
						}
					} else {
						$condicao_ccu = " E405SOL.CCURES IN ('$ccu_list')";
					}
					
					if ($condicao_ccu) {
						$condccu = " AND (".$condicao_ccu.")";
					} else {
						$condccu ='';
					}
					//echo $condccu;
                                        //print_r($condccu);
					$q1 = "SELECT E405SOL.CODEMP,
                                                                    E070FIL.SIGFIL,
                                                                    E070FIL.USU_INSTAN,
                                                                    E405SOL.NUMSOL,
                                                                    E405SOL.SEQSOL,
                                                                    E405SOL.CODUSU,
                                                                    e099usu.NOMUSU,
                                                                    E405SOL.DATSOL,
                                                                    E405SOL.HORSOL,
                                                                    E405SOL.FILSOL,
                                                                    E405SOL.CODPRO,
                                                                    E405SOL.CODDER,
                                                                    E405SOL.CODSER,
                                                                    E405SOL.PROSER,
                                                                    E405SOL.CODFAM,
                                                                    E405SOL.CPLPRO,
                                                                    E405SOL.UNIMED,
                                                                    E405SOL.CODAGE,
                                                                    E405SOL.CODAGC,
                                                                    E405SOL.DATEFC,
                                                                    E405SOL.OBSSOL,
                                                                    E405SOL.QTDSOL,
                                                                    E405SOL.QTDAPR,
                                                                    E405SOL.QTDCAN,
                                                                    E405SOL.DATPRV,
                                                                    E405SOL.CODPVP,
                                                                    E405SOL.CODDEP,
                                                                    E405SOL.NUMPRJ,
                                                                    E405SOL.CODFPJ,
                                                                    E405SOL.CTAFIN,                                                                    
                                                                    E405SOL.CTARED,
                                                                    E091PLF.ABRCTA,
                                                                    E405SOL.USUSOL,
                                                                    E405SOL.USURES,
                                                                    E405SOL.CCURES,
                                                                    E044CCU.ABRCCU,
                                                                    E405SOL.FILPED,
                                                                    E405SOL.NUMPED,
                                                                    E405SOL.SEQIPD,
                                                                    E405SOL.SEQISP,
                                                                    E405SOL.NUMEME,
                                                                    E405SOL.SEQEME,
                                                                    E405SOL.NUMCOT,
                                                                    E405SOL.INDEQI,
                                                                    E405SOL.DATLIC,
                                                                    E405SOL.CODBEM,
                                                                    E405SOL.ROTNAP,
                                                                    E405SOL.NUMAPR,
                                                                    E405SOL.SITAPR,
                                                                    E405SOL.NUMPCT,
                                                                    E405SOL.INDAPS,
                                                                    E405SOL.APRSOL,
                                                                    E405SOL.DATAPR,
                                                                    E405SOL.PRCSOL,
                                                                    E405SOL.SOLINT,
                                                                    E405SOL.SITSOL,
                                                                    E405SOL.TIPINT,
                                                                    E405SOL.PRESOL,
                                                                    E405SOL.CODTNS,
                                                                    E405SOL.CODMOT,
                                                                    E405SOL.OBSMOT,
                                                                    E405SOL.CODPRI,
                                                                    E405SOL.PVPPAI,
                                                                    E405SOL.USUCAN,
                                                                    E405SOL.DATCAN,
                                                                    E405SOL.HORCAN,
                                                                    E405SOL.BEMPRI,
                                                                    E405SOL.CODCLI,
                                                                    E405SOL.CODEQP,
                                                                    E405SOL.NUMMNT,
                                                                    E405SOL.LOCDOC,
                                                                    E405SOL.ROTANX,
                                                                    E405SOL.NUMANX,
                                                                    E405SOL.MOTDEV,
                                                                    E405SOL.AGRNEC,
                                                                    E405SOL.AGRPAI,
                                                                    E405SOL.TIPCPR,
                                                                    E405SOL.CODMOD,
                                                                    E405SOL.USUCPR,
                                                                    E405SOL.APRINI,
                                                                    E405SOL.REASOL,
                                                                    E614APR.NIVEXI,
                                                                    $rnap CODNAPAPR
                                                                    FROM E405SOL, E614APR, E070FIL, E405RAT, e099usu, E044CCU, E091PLF, E045PLA
                                                                    WHERE E405SOL.CODEMP = $key
                                                                    AND E405SOL.NUMAPR = E614APR.NUMAPR
                                                                    AND E405SOL.ROTNAP = E614APR.ROTNAP
                                                                    AND E405SOL.CODEMP = E614APR.CODEMP
                                                                    AND E614APR.CODFIL = 0
                                                                    AND E405SOL.NUMCOT = 0
                                                                    AND E614APR.CODEMP = E070FIL.CODEMP
                                                                    AND E405SOL.CODEMP = E070FIL.CODEMP
                                                                    AND E405SOL.FILSOL = E070FIL.CODFIL
                                                                    AND 0 = 0											   
                                                                    AND E405SOL.QTDSOL <> E405SOL.QTDCAN
                                                                    AND E405SOL.CODEMP = E405RAT.CODEMP
                                                                    AND E405SOL.NUMSOL = E405RAT.NUMSOL
                                                                    AND E405SOL.SEQSOL = E405RAT.SEQSOL
                                                                    AND E405SOL.CODEMP = e099usu.CODEMP
                                                                    AND E405SOL.CODUSU = e099usu.CODUSU
                                                                    AND E405SOL.CODEMP = E044CCU.CODEMP							
                                                                    AND E405SOL.CCURES = E044CCU.CODCCU
                                                                    AND E405SOL.CODEMP = E091PLF.CODEMP
                                                                    AND E405SOL.CTAFIN = E091PLF.CTAFIN
                                                                    AND E405SOL.CODEMP = E045PLA.CODEMP(+)
                                                                    AND E405SOL.CTARED = E045PLA.CTARED(+)
                                                                    AND E405SOL.SITAPR = 'ANA'
                                                                    $condnivel
                                                                    $condicao
                                                                    $condccu
                                                                    GROUP BY E405SOL.CODEMP,
                                                                        E070FIL.SIGFIL,
                                                                        E070FIL.USU_INSTAN,
                                                                        E405SOL.NUMSOL,
                                                                        E405SOL.SEQSOL,
                                                                        E405SOL.CODUSU,
                                                                        e099usu.NOMUSU,
                                                                        E405SOL.DATSOL,
                                                                        E405SOL.HORSOL,
                                                                        E405SOL.FILSOL,
                                                                        E405SOL.CODPRO,
                                                                        E405SOL.CODDER,
                                                                        E405SOL.CODSER,
                                                                        E405SOL.PROSER,
                                                                        E405SOL.CODFAM,
                                                                        E405SOL.CPLPRO,
                                                                        E405SOL.UNIMED,
                                                                        E405SOL.CODAGE,
                                                                        E405SOL.CODAGC,
                                                                        E405SOL.DATEFC,
                                                                        E405SOL.OBSSOL,
                                                                        E405SOL.QTDSOL,
                                                                        E405SOL.QTDAPR,
                                                                        E405SOL.QTDCAN,
                                                                        E405SOL.DATPRV,
                                                                        E405SOL.CODPVP,
                                                                        E405SOL.CODDEP,
                                                                        E405SOL.NUMPRJ,
                                                                        E405SOL.CODFPJ,
                                                                        E405SOL.CTAFIN,                                                                        
                                                                        E405SOL.CTARED,
                                                                        E091PLF.ABRCTA,
                                                                        E405SOL.USUSOL,
                                                                        E405SOL.USURES,
                                                                        E405SOL.CCURES,
                                                                        E044CCU.ABRCCU,
                                                                        E405SOL.FILPED,
                                                                        E405SOL.NUMPED,
                                                                        E405SOL.SEQIPD,
                                                                        E405SOL.SEQISP,
                                                                        E405SOL.NUMEME,
                                                                        E405SOL.SEQEME,
                                                                        E405SOL.NUMCOT,
                                                                        E405SOL.INDEQI,
                                                                        E405SOL.DATLIC,
                                                                        E405SOL.CODBEM,
                                                                        E405SOL.ROTNAP,
                                                                        E405SOL.NUMAPR,
                                                                        E405SOL.SITAPR,
                                                                        E405SOL.NUMPCT,
                                                                        E405SOL.INDAPS,
                                                                        E405SOL.APRSOL,
                                                                        E405SOL.DATAPR,
                                                                        E405SOL.PRCSOL,
                                                                        E405SOL.SOLINT,
                                                                        E405SOL.SITSOL,
                                                                        E405SOL.TIPINT,
                                                                        E405SOL.PRESOL,
                                                                        E405SOL.CODTNS,
                                                                        E405SOL.CODMOT,
                                                                        E405SOL.OBSMOT,
                                                                        E405SOL.CODPRI,
                                                                        E405SOL.PVPPAI,
                                                                        E405SOL.USUCAN,
                                                                        E405SOL.DATCAN,
                                                                        E405SOL.HORCAN,
                                                                        E405SOL.BEMPRI,
                                                                        E405SOL.CODCLI,
                                                                        E405SOL.CODEQP,
                                                                        E405SOL.NUMMNT,
                                                                        E405SOL.LOCDOC,
                                                                        E405SOL.ROTANX,
                                                                        E405SOL.NUMANX,
                                                                        E405SOL.MOTDEV,
                                                                        E405SOL.AGRNEC,
                                                                        E405SOL.AGRPAI,
                                                                        E405SOL.TIPCPR,
                                                                        E405SOL.CODMOD,
                                                                        E405SOL.USUCPR,
                                                                        E405SOL.APRINI,
                                                                        E405SOL.REASOL,
                                                                        E614APR.NIVEXI                                                                        
                                                                       ";
							//$query[$key] = $q1->result();
										
				} else {
					
					$q1 = "SELECT E405SOL.CODEMP,
							E070FIL.SIGFIL,
                                                        E070FIL.USU_INSTAN,
							E405SOL.NUMSOL,
							E405SOL.SEQSOL,
							E405SOL.CODUSU,
							e099usu.NOMUSU,
							E405SOL.DATSOL,
							E405SOL.HORSOL,
							E405SOL.FILSOL,
							E405SOL.CODPRO,
							E405SOL.CODDER,
							E405SOL.CODSER,
							E405SOL.PROSER,
							E405SOL.CODFAM,
							E405SOL.CPLPRO,
							E405SOL.UNIMED,
							E405SOL.CODAGE,
							E405SOL.CODAGC,
							E405SOL.DATEFC,
							E405SOL.OBSSOL,
							E405SOL.QTDSOL,
							E405SOL.QTDAPR,
							E405SOL.QTDCAN,
							E405SOL.DATPRV,
							E405SOL.CODPVP,
							E405SOL.CODDEP,
							E405SOL.NUMPRJ,
							E405SOL.CODFPJ,
							E405SOL.CTAFIN,							
							E405SOL.CTARED,
							E045PLA.ABRCTA,
							E405SOL.USUSOL,
							E405SOL.USURES,
							E405SOL.CCURES,
							E044CCU.ABRCCU,
							E405SOL.FILPED,
							E405SOL.NUMPED,
							E405SOL.SEQIPD,
							E405SOL.SEQISP,
							E405SOL.NUMEME,
							E405SOL.SEQEME,
							E405SOL.NUMCOT,
							E405SOL.INDEQI,
							E405SOL.DATLIC,
							E405SOL.CODBEM,
							E405SOL.ROTNAP,
							E405SOL.NUMAPR,
							E405SOL.SITAPR,
							E405SOL.NUMPCT,
							E405SOL.INDAPS,
							E405SOL.APRSOL,
							E405SOL.DATAPR,
							E405SOL.PRCSOL,
							E405SOL.SOLINT,
							E405SOL.SITSOL,
							E405SOL.TIPINT,
							E405SOL.PRESOL,
							E405SOL.CODTNS,
							E405SOL.CODMOT,
							E405SOL.OBSMOT,
							E405SOL.CODPRI,
							E405SOL.PVPPAI,
							E405SOL.USUCAN,
							E405SOL.DATCAN,
							E405SOL.HORCAN,
							E405SOL.BEMPRI,
							E405SOL.CODCLI,
							E405SOL.CODEQP,
							E405SOL.NUMMNT,
							E405SOL.LOCDOC,
							E405SOL.ROTANX,
							E405SOL.NUMANX,
							E405SOL.MOTDEV,
							E405SOL.AGRNEC,
							E405SOL.AGRPAI,
							E405SOL.TIPCPR,
							E405SOL.CODMOD,
							E405SOL.USUCPR,
							E405SOL.APRINI,
							E405SOL.REASOL,
							E614APR.NIVEXI,
                                                        $rnap CODNAPAPR
							FROM E405SOL, E614APR, E070FIL, E405RAT, e099usu, E044CCU, E091PLF, E045PLA
							WHERE E405SOL.CODEMP = $key
							AND E405SOL.NUMAPR = E614APR.NUMAPR
							AND E405SOL.ROTNAP = E614APR.ROTNAP
							AND E405SOL.CODEMP = E614APR.CODEMP
							AND E614APR.CODFIL = 0
							AND E405SOL.NUMCOT = 0
							AND E614APR.CODEMP = E070FIL.CODEMP
							AND E405SOL.CODEMP = E070FIL.CODEMP
							AND E405SOL.FILSOL = E070FIL.CODFIL
							AND 0 = 0							
							AND E405SOL.QTDSOL <> E405SOL.QTDCAN
							AND E405SOL.CODEMP = E405RAT.CODEMP
   							AND E405SOL.NUMSOL = E405RAT.NUMSOL
   							AND E405SOL.SEQSOL = E405RAT.SEQSOL
							AND E405SOL.CODEMP = e099usu.CODEMP
							AND E405SOL.CODUSU = e099usu.CODUSU
							AND E405SOL.CODEMP = E044CCU.CODEMP							
							AND E405SOL.CCURES = E044CCU.CODCCU
							AND E405SOL.CODEMP = E091PLF.CODEMP
							AND E405SOL.CTAFIN = E091PLF.CTAFIN
							AND E405SOL.CODEMP = E045PLA.CODEMP
							AND E405SOL.CTARED = E045PLA.CTARED
							AND E405SOL.SITAPR = 'ANA'
							$condicao							
							GROUP BY E405SOL.CODEMP,
							E070FIL.SIGFIL,
                                                        E070FIL.USU_INSTAN,
							E405SOL.NUMSOL,
							E405SOL.SEQSOL,
							E405SOL.CODUSU,
							e099usu.NOMUSU,
							E405SOL.DATSOL,
							E405SOL.HORSOL,
							E405SOL.FILSOL,
							E405SOL.CODPRO,
							E405SOL.CODDER,
							E405SOL.CODSER,
							E405SOL.PROSER,
							E405SOL.CODFAM,
							E405SOL.CPLPRO,
							E405SOL.UNIMED,
							E405SOL.CODAGE,
							E405SOL.CODAGC,
							E405SOL.DATEFC,
							E405SOL.OBSSOL,
							E405SOL.QTDSOL,
							E405SOL.QTDAPR,
							E405SOL.QTDCAN,
							E405SOL.DATPRV,
							E405SOL.CODPVP,
							E405SOL.CODDEP,
							E405SOL.NUMPRJ,
							E405SOL.CODFPJ,
							E405SOL.CTAFIN,							
							E405SOL.CTARED,
							E045PLA.ABRCTA,
							E405SOL.USUSOL,
							E405SOL.USURES,
							E405SOL.CCURES,
							E044CCU.ABRCCU,
							E405SOL.FILPED,
							E405SOL.NUMPED,
							E405SOL.SEQIPD,
							E405SOL.SEQISP,
							E405SOL.NUMEME,
							E405SOL.SEQEME,
							E405SOL.NUMCOT,
							E405SOL.INDEQI,
							E405SOL.DATLIC,
							E405SOL.CODBEM,
							E405SOL.ROTNAP,
							E405SOL.NUMAPR,
							E405SOL.SITAPR,
							E405SOL.NUMPCT,
							E405SOL.INDAPS,
							E405SOL.APRSOL,
							E405SOL.DATAPR,
							E405SOL.PRCSOL,
							E405SOL.SOLINT,
							E405SOL.SITSOL,
							E405SOL.TIPINT,
							E405SOL.PRESOL,
							E405SOL.CODTNS,
							E405SOL.CODMOT,
							E405SOL.OBSMOT,
							E405SOL.CODPRI,
							E405SOL.PVPPAI,
							E405SOL.USUCAN,
							E405SOL.DATCAN,
							E405SOL.HORCAN,
							E405SOL.BEMPRI,
							E405SOL.CODCLI,
							E405SOL.CODEQP,
							E405SOL.NUMMNT,
							E405SOL.LOCDOC,
							E405SOL.ROTANX,
							E405SOL.NUMANX,
							E405SOL.MOTDEV,
							E405SOL.AGRNEC,
							E405SOL.AGRPAI,
							E405SOL.TIPCPR,
							E405SOL.CODMOD,
							E405SOL.USUCPR,
							E405SOL.APRINI,
							E405SOL.REASOL,
							E614APR.NIVEXI                                                        
							";
					//$query[$key] = $q1->result();
					
				}
                                
				$sql .= $q1.' union all ';
                                //echo $sql;
                                }
			}
                        $sql = substr_replace($sql, '', -10);
                        $query = $this->db->query("SELECT DISTINCT * FROM (".$sql ." ) ORDER BY 1,10,4,5");
			$result = $query->result();
                    
                        if ($result) {
                            return $result;
                        } else {
                            return false;
                        }
			//var_dump($cculist);
		} else {			
			return false;
		}
		
	}
	
	function getPendente($codemp, $numapr, $rotnap, $numsol, $codfil, $seqsol) {
	
		$query = $this->db->query("SELECT DISTINCT SC.CODEMP,
										        SC.FILSOL,
										        SC.NUMSOL,
										        SC.SEQSOL,
										        SC.NUMAPR,
										        APR.NIVEXI,
										        CNA.CODNAP,
										        NAP.DESNAP,
										        USU.CODUSU,
										        USU.NOMUSU
										        FROM E405SOL SC
										        INNER JOIN E614APR APR
										        ON SC.CODEMP = APR.CODEMP
										        AND SC.ROTNAP = APR.ROTNAP
										        AND SC.NUMAPR = APR.NUMAPR
										        INNER JOIN E405RAT RAT
										        ON SC.CODEMP = RAT.CODEMP
										        AND SC.SEQSOL = RAT.SEQSOL
										        AND SC.NUMSOL = RAT.NUMSOL
										        INNER JOIN E068CNA CNA
										        ON SC.CODEMP = CNA.CODEMP
										        AND SC.ROTNAP = CNA.ROTNAP
										        AND RAT.CODCCU = CNA.CODCCU
										        AND APR.NIVEXI LIKE '%' || CNA.CODNAP || '%'
										        AND CNA.SITCNA = 'A'
										        INNER JOIN E068NAP NAP
										        ON CNA.CODEMP = NAP.CODEMP
										        AND CNA.ROTNAP = NAP.ROTNAP
										        AND CNA.CODNAP = NAP.CODNAP
										        INNER JOIN R999USU USU
										        ON USU.CODUSU = CNA.CODUSU
										        WHERE SC.NUMSOL = $numsol
										        AND SC.FILSOL = $codfil
										        AND SC.NUMAPR = $numapr
										        AND SC.CODEMP = $codemp
										        AND SC.ROTNAP = $rotnap
										        AND SC.SEQSOL = $seqsol
										        AND CNA.CODNAP NOT IN (SELECT APR.NIVAPR FROM e614usu APR WHERE APR.CODEMP = SC.CODEMP
										        AND APR.ROTNAP = SC.ROTNAP AND APR.NUMAPR = SC.NUMAPR AND APR.SITAPR = 'APR')
										        UNION ALL
					                            SELECT DISTINCT SC.CODEMP,
					                            SC.FILSOL,
					                            SC.NUMSOL,
					                            SC.SEQSOL,
					                            SC.NUMAPR,
					                            APR.NIVEXI,
					                            UNA.CODNAP,
					                            NAP.DESNAP,
					                            USU.CODUSU,
					                            USU.NOMUSU
					                            FROM E405SOL SC
					                            INNER JOIN E614APR APR
					                            ON SC.CODEMP = APR.CODEMP
					                            AND SC.ROTNAP = APR.ROTNAP
					                            AND SC.NUMAPR = APR.NUMAPR
					                            INNER JOIN E405RAT RAT
					                            ON SC.CODEMP = RAT.CODEMP
					                            AND SC.SEQSOL = RAT.SEQSOL
					                            AND SC.NUMSOL = RAT.NUMSOL
					                            INNER JOIN E068UNA UNA
					                            ON SC.CODEMP = UNA.CODEMP
					                            AND SC.ROTNAP = UNA.ROTNAP
					                            AND APR.NIVEXI LIKE '%' || UNA.CODNAP || '%'
					                            AND UNA.SITUNA = 'A'
					                            AND UNA.CODUSU NOT IN (select CNA.CODUSU from E068CNA CNA WHERE UNA.CODEMP = CNA.CODEMP AND UNA.ROTNAP = CNA.ROTNAP AND UNA.CODNAP = CNA.CODNAP) 
					                            INNER JOIN E068NAP NAP
					                            ON UNA.CODEMP = NAP.CODEMP
					                            AND UNA.ROTNAP = NAP.ROTNAP
					                            AND UNA.CODNAP = NAP.CODNAP
					                            INNER JOIN R999USU USU
					                            ON USU.CODUSU = UNA.CODUSU
					                            WHERE SC.NUMSOL = $numsol
					                            AND SC.FILSOL = $codfil
					                            AND SC.NUMAPR = $numapr
					                            AND SC.CODEMP = $codemp
					                            AND SC.ROTNAP = $rotnap
					                            AND SC.SEQSOL = $seqsol
					                            AND UNA.CODNAP NOT IN (SELECT APR.NIVAPR FROM e614usu APR WHERE APR.CODEMP = SC.CODEMP
					                            AND APR.ROTNAP = SC.ROTNAP AND APR.NUMAPR = SC.NUMAPR AND APR.SITAPR = 'APR')
					                            ORDER BY 1, 2, 3, 6, 7");
		if($query -> num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	
	}
        
        function conbuscasol($tipo, $contapr, $filial_list, $produto, $servico, $coduser, $codemp_list, $statusapr, $checkccu, $sol) {
		ini_set('max_execution_time', 0);
		$condicao = '';		
                
		if ($tipo != null) {
			$condicao .= " AND E405SOL.PROSER = '$tipo'";
		}
		
		if ($contapr != null) {
			$condicao .= " AND E405SOL.SITAPR = '$contapr'";
		}
		
		if ($filial_list != null) {
			$condicao .= " AND E405SOL.FILSOL IN ($filial_list)";
		}
		
		if ($produto != null) {
			$condicao .= " AND E405SOL.CODPRO = '$produto'";
		}
		
		if ($servico != null) {
			$condicao .= " AND E405SOL.CODSER = '$servico'";
		}
		
		if ($sol != null) {
			$condicao = " AND E405SOL.NUMSOL = '$sol'";
		}
                
                if ($codemp_list != null) {
                    $condicao .= " AND E405SOL.CODEMP IN ($codemp_list)";
                }
                //print_r($condicao);
                //print_r($checkccu);
                //var_dump($codemp_list);
                $condnivel = '';
                $condicao_apr = '';
		$sql = '';		
				//var_dump($key);
				//var_dump($row);
				//print_r($statusapr);                               
				//var_dump($r);                                                              									
					$q1 = "SELECT E405SOL.CODEMP,
							E070FIL.SIGFIL,
                                                        E070FIL.USU_INSTAN,
							E405SOL.NUMSOL,
							E405SOL.SEQSOL,
							E405SOL.CODUSU,
							e099usu.NOMUSU,
							E405SOL.DATSOL,
							E405SOL.HORSOL,
							E405SOL.FILSOL,
							E405SOL.CODPRO,
							E405SOL.CODDER,
							E405SOL.CODSER,
							E405SOL.PROSER,
							E405SOL.CODFAM,
							E405SOL.CPLPRO,
							E405SOL.UNIMED,
							E405SOL.CODAGE,
							E405SOL.CODAGC,
							E405SOL.DATEFC,
							E405SOL.OBSSOL,
							E405SOL.QTDSOL,
							E405SOL.QTDAPR,
							E405SOL.QTDCAN,
							E405SOL.DATPRV,
							E405SOL.CODPVP,
							E405SOL.CODDEP,
							E405SOL.NUMPRJ,
							E405SOL.CODFPJ,
							E405SOL.CTAFIN,							
							E405SOL.CTARED,
							E091PLF.ABRCTA,
							E405SOL.USUSOL,
							E405SOL.USURES,
							E405SOL.CCURES,
							E044CCU.ABRCCU,
							E405SOL.FILPED,
							E405SOL.NUMPED,
							E405SOL.SEQIPD,
							E405SOL.SEQISP,
							E405SOL.NUMEME,
							E405SOL.SEQEME,
							E405SOL.NUMCOT,
							E405SOL.INDEQI,
							E405SOL.DATLIC,
							E405SOL.CODBEM,
							E405SOL.ROTNAP,
							E405SOL.NUMAPR,
							E405SOL.SITAPR,
							E405SOL.NUMPCT,
							E405SOL.INDAPS,
							E405SOL.APRSOL,
							E405SOL.DATAPR,
							E405SOL.PRCSOL,
							E405SOL.SOLINT,
							E405SOL.SITSOL,
							E405SOL.TIPINT,
							E405SOL.PRESOL,
							E405SOL.CODTNS,
							E405SOL.CODMOT,
							E405SOL.OBSMOT,
							E405SOL.CODPRI,
							E405SOL.PVPPAI,
							E405SOL.USUCAN,
							E405SOL.DATCAN,
							E405SOL.HORCAN,
							E405SOL.BEMPRI,
							E405SOL.CODCLI,
							E405SOL.CODEQP,
							E405SOL.NUMMNT,
							E405SOL.LOCDOC,
							E405SOL.ROTANX,
							E405SOL.NUMANX,
							E405SOL.MOTDEV,
							E405SOL.AGRNEC,
							E405SOL.AGRPAI,
							E405SOL.TIPCPR,
							E405SOL.CODMOD,
							E405SOL.USUCPR,
							E405SOL.APRINI,
							E405SOL.REASOL,
							E614APR.NIVEXI                                                        
							FROM E405SOL
                                                        INNER JOIN E614APR
                                                        ON E405SOL.NUMAPR = E614APR.NUMAPR
							AND E405SOL.ROTNAP = E614APR.ROTNAP
							AND E405SOL.CODEMP = E614APR.CODEMP							
                                                        INNER JOIN E070FIL
                                                        ON E614APR.CODEMP = E070FIL.CODEMP
							AND E405SOL.CODEMP = E070FIL.CODEMP
							AND E405SOL.FILSOL = E070FIL.CODFIL
                                                        INNER JOIN E405RAT
                                                        ON E405SOL.CODEMP = E405RAT.CODEMP
   							AND E405SOL.NUMSOL = E405RAT.NUMSOL
   							AND E405SOL.SEQSOL = E405RAT.SEQSOL
                                                        INNER JOIN e099usu
                                                        ON E405SOL.CODEMP = e099usu.CODEMP
							AND E405SOL.CODUSU = e099usu.CODUSU
                                                        INNER JOIN E044CCU
                                                        ON E405SOL.CODEMP = E044CCU.CODEMP							
							AND E405SOL.CCURES = E044CCU.CODCCU
                                                        INNER JOIN E091PLF
                                                        ON E405SOL.CODEMP = E091PLF.CODEMP
							AND E405SOL.CTAFIN = E091PLF.CTAFIN
                                                        INNER JOIN E045PLA
                                                        ON E405SOL.CODEMP = E045PLA.CODEMP
							AND E405SOL.CTARED = E045PLA.CTARED
                                                        WHERE E405SOL.NUMCOT = 0													
							AND E405SOL.SITAPR = 'ANA'
							$condicao							
							GROUP BY E405SOL.CODEMP,
							E070FIL.SIGFIL,
                                                        E070FIL.USU_INSTAN,
							E405SOL.NUMSOL,
							E405SOL.SEQSOL,
							E405SOL.CODUSU,
							e099usu.NOMUSU,
							E405SOL.DATSOL,
							E405SOL.HORSOL,
							E405SOL.FILSOL,
							E405SOL.CODPRO,
							E405SOL.CODDER,
							E405SOL.CODSER,
							E405SOL.PROSER,
							E405SOL.CODFAM,
							E405SOL.CPLPRO,
							E405SOL.UNIMED,
							E405SOL.CODAGE,
							E405SOL.CODAGC,
							E405SOL.DATEFC,
							E405SOL.OBSSOL,
							E405SOL.QTDSOL,
							E405SOL.QTDAPR,
							E405SOL.QTDCAN,
							E405SOL.DATPRV,
							E405SOL.CODPVP,
							E405SOL.CODDEP,
							E405SOL.NUMPRJ,
							E405SOL.CODFPJ,
							E405SOL.CTAFIN,							
							E405SOL.CTARED,
							E091PLF.ABRCTA,
							E405SOL.USUSOL,
							E405SOL.USURES,
							E405SOL.CCURES,
							E044CCU.ABRCCU,
							E405SOL.FILPED,
							E405SOL.NUMPED,
							E405SOL.SEQIPD,
							E405SOL.SEQISP,
							E405SOL.NUMEME,
							E405SOL.SEQEME,
							E405SOL.NUMCOT,
							E405SOL.INDEQI,
							E405SOL.DATLIC,
							E405SOL.CODBEM,
							E405SOL.ROTNAP,
							E405SOL.NUMAPR,
							E405SOL.SITAPR,
							E405SOL.NUMPCT,
							E405SOL.INDAPS,
							E405SOL.APRSOL,
							E405SOL.DATAPR,
							E405SOL.PRCSOL,
							E405SOL.SOLINT,
							E405SOL.SITSOL,
							E405SOL.TIPINT,
							E405SOL.PRESOL,
							E405SOL.CODTNS,
							E405SOL.CODMOT,
							E405SOL.OBSMOT,
							E405SOL.CODPRI,
							E405SOL.PVPPAI,
							E405SOL.USUCAN,
							E405SOL.DATCAN,
							E405SOL.HORCAN,
							E405SOL.BEMPRI,
							E405SOL.CODCLI,
							E405SOL.CODEQP,
							E405SOL.NUMMNT,
							E405SOL.LOCDOC,
							E405SOL.ROTANX,
							E405SOL.NUMANX,
							E405SOL.MOTDEV,
							E405SOL.AGRNEC,
							E405SOL.AGRPAI,
							E405SOL.TIPCPR,
							E405SOL.CODMOD,
							E405SOL.USUCPR,
							E405SOL.APRINI,
							E405SOL.REASOL,
							E614APR.NIVEXI                                                        
							";					                                				                                                                			                        
                        $query = $this->db->query("SELECT DISTINCT * FROM (".$q1 ." ) ORDER BY 1,10,4,5");
			$result = $query->result();
                    
                        if ($result) {
                            return $result;
                        } else {
                            return false;
                        }
			//var_dump($cculist);										
		
	}
}