<?php if ($result) { ?>
	<table class="table table-striped table-condensed table-hover small">
		<thead>
			<tr class="cabecalho">
                            <th class="text-center">Item</th>		    		
                            <th colspan="2" class="text-center">Produto</th>
                            <th class="text-center">Qdte</th>
                            <th class="text-center">Qdte Recebida</th>
			    <th class="text-center">Valor Unit</th>			    
			    <th class="text-center">Entrega</th>	
			    <th class="text-center">Nota</th>			    		
			</tr>
		</thead>
		<tbody>		    		    
<?php foreach ($result as $row) { ?>
			<tr>		
                            <td class="text-center"><?php echo $row->SEQIT ?></td>				
                            <td class="text-center"><?php echo $row->PROSER ?></td>
                            <td class="text-left"><?php echo $row->DESCRI ?></td>
                            <td class="text-center"><?php echo $row->QTDPED ?></td>
                            <td class="text-center"><?php echo $row->QTDREC ?></td>
                            <td class="text-right">R$ <?php echo number_format(str_replace("," , "." , $row->PREUNI), 2, ',', '.') ?></td>				
                            <td class="text-center"><?php echo $row->DATGER ?></td>							
                            <td class="text-center"><?php echo $row->NUMNFC ?></td>
			</tr>
		</tbody>
<?php 				
		}							
} else {
	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
}
?>
	</table>