<?php if ($result) { ?>
<div><h4><strong>Filial :</strong> <?php echo $result[0]->FILSAP.'-'.$result[0]->SIGFIL ?> - <strong>CIF :</strong> <?php echo $result[0]->CODCIF.'-'.$result[0]->DESCIF ?></h4></div>
	<table class="table table-striped table-condensed table-hover small">
		<thead>
			<tr class="cabecalho">
                            <th class="text-center">Data</th>
                            <th class="text-center">Matricula</th>		    		
                            <th class="text-center">Nome</th>
                            <th class="text-center">Valor</th>                            		    		
			</tr>
		</thead>
		<tbody>		    		    
<?php foreach ($result as $row) { ?>
			<tr>		
                            <td class="text-right"><?php echo $row->VNCTIT ?></td>				
                            <td class="text-right"><?php echo $row->NUMCAD ?></td>				
                            <td class="text-left"><?php echo $row->NOMFUN ?></td>
                            <td class="text-right"><?php echo number_format(str_replace("," , "." , $row->VLRPGT), 2, ',', '.') ?></td>                           
			</tr>
		</tbody>
<?php 				
		}							
} else {
	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
}
?>
	</table>