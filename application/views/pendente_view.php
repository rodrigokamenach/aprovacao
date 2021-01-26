<?php if ($result) { ?>
	<table class="table table-condensed table-hover small">		
		<thead>
			<tr class="cabecalho">
				<th class="text-center">Aprovador</th>		    		
			   	<th class="text-center">Nome</th>			   				   			    
			</tr>
		</thead>
		<tbody>		    		    
<?php 
$nivel = '';
$codnap = 0;
foreach ($result as $row) { 
		
	if ($nivel != $row->NIVEXI) { ?>
		<tr>
			<td class="subcabecalho" colspan="2">Nível Exigido: <?php echo $row->NIVEXI ?></td>
		</tr>		
	<?php 	
	}
	
	if ($codnap != $row->CODNAP) {?>
		<tr>
			<td class="subnivel" colspan="2"><?php echo $row->CODNAP.' - '.$row->DESNAP ?></td>
		</tr>
	<?php } ?>	
			<tr>										
				<td class="text-center"><?php echo $row->CODUSU ?></td>
				<td class="text-left"><?php echo $row->NOMUSU ?></td>				
			</tr>
		</tbody>
<?php 	
	$nivel = $row->NIVEXI;
	$codnap =$row->CODNAP;
		}							
} else {
	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
}
?>
	</table>