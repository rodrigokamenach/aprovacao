<script>
    function jProcessa() {
        //alert('oi');
        bootbox.dialog({
            title: 'Confirma Alterção',
            message: '<div class="row">  ' +
                        '<div class="col-sm-12">' +
                        '<h3 class="alert alert-danger">' +
                        'Deseja alterar a quantidade dos itens?' +                        
                        '<h3></div>' +                        
                        '</div> </div>',
            buttons: {
                cancel: {
                    label: 'Cancel',
                    className: 'btn-danger'
                },
                success: {
                    'label': 'Confirma',
                    'className': "btn-success",
                    'callback': function () {
                        dataString = $("#alteraitem").serialize();
                        $.blockUI({
                            message: '<h5><i class="fa fa-refresh fa-spin"></i>  Processando!<br>Aguarde....</h5>',
                            css: {
                                border: 'none',
                                padding: '15px',
                                backgroundColor: '#000',
                                '-webkit-border-radius': '10px',
                                '-moz-border-radius': '10px',
                                opacity: .5,
                                color: '#fff'
                        }});
                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url(); ?>index.php/oc/recalcular",
                            data: dataString,
                            success: function (data) {
                                $.unblockUI();
                                $('#retorno').html(data);
                                $('#mRetorno').modal('show');
                                $('#mRetorno').on('hidden.bs.modal', function () {
                                    window.location = "<?php echo base_url(); ?>index.php/oc";
                                });
                            }
                        });
                    }
                }
            }
        });
    }
</script>
<?php if ($result) { 
    $this->load->helper(array('form'));
    echo validation_errors();
    $attributes = array('id' => 'alteraitem', 'class' => 'form-horizontal');
    echo form_open('oc/altera_item', $attributes);
    //var_dump($result);
        
?>
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
<?php foreach ($result as $row) { 
    
    $item = array(
        'name'  => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.$row->PROSER.$row->SEQIT.'][item]',
        'id'    => 'item',
        'value' => $row->CODEMP.'|'.$row->CODFIL.'|'.$row->NUMOCP.'|'.$row->PROSER.'|'.$row->SEQIT.'|'.$row->UNIMED,
        'title' => $row->CODEMP.'|'.$row->CODFIL.'|'.$row->NUMOCP.'|'.$row->PROSER.'|'.$row->SEQIT.'|'.$row->UNIMED,
        'type'  => 'hidden',        
    );
    echo form_input($item);
    
    $qtde = array(
        'name'  => 'id['.$row->CODEMP.$row->CODFIL.$row->NUMOCP.$row->PROSER.'][qtde]',
        'id'    => 'qtde',
        'value' => $row->QTDPED,
        'title' => $row->QTDPED,
        'class'     => 'form-control input-sm'
    );
    
    
    ?>
			<tr>		
                            <td class="text-center"><?php echo $row->SEQIT ?></td>				
                            <td class="text-center"><?php echo $row->PROSER ?></td>
                            <td class="text-left col-sm-6"><?php echo $row->DESCRI ?></td>
                            <td class="text-center col-sm-1"><?php 
                            if ($codnap == 30) { 
                                echo form_input($qtde); 
                            } else { echo $row->QTDPED; }?></td>
                            <td class="text-center"><?php echo $row->QTDREC ?></td>
                            <td class="text-right col-sm-2">R$ <?php echo number_format(str_replace("," , "." , $row->PREUNI), 2, ',', '.') ?></td>				
                            <td class="text-center"><?php echo $row->DATGER ?></td>							
                            <td class="text-center"><?php echo $row->NUMNFC ?></td>
			</tr>
		</tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="text-right">
                            <?php if ($codnap == 30) { ?>
                                <button type="button" onclick="jProcessa()" name="processa" id="processa" class="btn btn-success">Processar</button>
                            <?php } ?>
                        </td>
                    </tr>
                </tfoot>
<?php 				
            }
    echo form_close();
} else {
	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
}
?>
	</table>