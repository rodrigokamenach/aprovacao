<?php if ($result) { ?>
	<table class="table table-striped table-condensed table-hover small">		
		<thead>
			<tr class="cabecalho">
				<th class="text-center">Nível</th>		    		
			   	<th class="text-center">Situação</th>
			   	<th class="text-center">Aprovador</th>			   			    	
			</tr>
		</thead>
		<tbody>		    		    
<?php 
$nivel = '';
foreach ($result as $row) { 
	if ($row->SITAPR == 'APR') {
		$texto = '- Aprovado';
	}
	
	if ($nivel != $row->NIVEXI) { ?>
		<tr>
			<td colspan="3">Nível Exigido: <?php echo $row->NIVEXI ?></td>
		</tr>		
	<?php 	
	}
	?>	
			<tr>		
				<td class="text-center"><?php echo $row->ROTNAP.' - '.$row->DESNAP ?></td>				
				<td class="text-center"><?php echo $row->SITAPR.$texto ?></td>
				<td class="text-left"><?php echo $row->USUAPR.' - '.$row->NOMUSU ?></td>				
			</tr>
		</tbody>
<?php 				
		}							
} else {
	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
}
?>
	</table>