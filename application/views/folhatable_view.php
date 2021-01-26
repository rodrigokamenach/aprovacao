<script type="text/javascript">
$(document).ready(function(){       

    $(":checkbox").click(function(){
	    //var options="";
	var options = $(this).attr('title');
    	var id = $(this).attr('name');
    	//alert(id);	        	
    	//classe = $(this).attr('class');
    	
	    if ($("input[type=checkbox][name^='"+id+"']").prop('checked')) {
	    	//$("input[type=text][name^='id["+options+"][dtpro]]").prop('disabled', '');
                    $('#datini'+options).prop('disabled', '');
                    $('#datfim'+options).prop('disabled', '');
                    $('#datvnc'+options).prop('disabled', '');
                    $('#cif'+options).prop('disabled', '');
                    $('#fil'+options).prop('disabled', '');                    
                    $('#codnap'+options).prop('disabled', ''); 
                    $('#emp'+options).prop('disabled', '');
                    $('#valor'+options).prop('disabled', '');
	    	//$('#vlr'+id).val(options);	    	
		} else {
                    $('#datini'+options).prop('disabled', 'disabled');
                    $('#datfim'+options).prop('disabled', 'disabled');
                    $('#datvnc'+options).prop('disabled', 'disabled');
                    $('#cif'+options).prop('disabled', 'disabled');
                    $('#fil'+options).prop('disabled', 'disabled');                    
                    $('#codnap'+options).prop('disabled', 'disabled');
                    $('#emp'+options).prop('disabled', 'disabled');
                    $('#valor'+options).prop('disabled', 'disabled');
			//$('#vlr'+id).val('');			
		}			   
	});	    		       	 	     
});	
</script>
<script type="text/javascript">
    function jAprova() {
        check = new Array();
        soma = 0;
        $("input[type=checkbox][name^='id']:checked").each(function () {
            check.push($(this).val());
            valor = String($(this).attr("rel"));
            valor = valor.replace(/,/g, '.');
            soma += parseFloat(valor);
        });

        soma = soma.toFixed(2);
        soma = CurrencyFormat(soma);
        //alert(soma);
        if (check.length == 0) {
            bootbox.alert({
                size: 'small',
                "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um item!</h5>',
                "className": "alert-modal"
            });
        } else {
            bootbox.dialog({
                title: 'Confirma Aprovação',
                message: '<div class="row">  ' +
                        '<div class="col-md-12">' +
                        '<h3 class="alert alert-danger">' +
                        'Valor Total: <strong>R$ ' + soma + '</strong></h3>' +
                        '<h3 class="alert alert-warning">' +
                        'Quantidade de CIFs: <strong>' + check.length + '</strong>' +
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
                            dataString = $("#form_apr_fol").serialize();
                            $.blockUI({
                                message: '<h5><i class="fa fa-refresh fa-spin"></i>  Processando Aprovações!<br>Aguarde....</h5>',
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
                                url: "<?php echo base_url(); ?>index.php/folha/aprovar",
                                data: dataString,
                                success: function (data) {
                                    $.unblockUI();
                                    $('#retorno').html(data);
                                    $('#mRetorno').modal('show');
                                    $('#mRetorno').on('hidden.bs.modal', function () {
                                        window.location = "<?php echo base_url(); ?>index.php/folha";
                                    });
                                }
                            });
                        }
                    }
                }
            });
        }
    }
</script>
<div class="panel panel-custom">
	 <div class="panel-heading">
	 	<div class="row row-fluid">	 	
	 		<div class="col-md-2 col-xs-offset-0"><h5 class="small"><i class="fa fa-list-ol"></i> Resultado do Filtro</h5></div>
	 		<!--  <div class="col-md-2 col-md-offset-8 right"><button type="button" onclick="excel()" name="dpdf" id="dpdf" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Exportar Excel</button></div>-->
	 	</div>
	 </div>
	 <div class="panel-body">
	 <?php 								
		$attribute = array('id' => 'form_apr_fol', 'name' => 'form_apr_fol'); 
		echo form_open('', $attribute); 
	 ?>
		 <div class="table-responsive">
		 	<?php echo $tabela; ?>
		</div>		
	<?php echo form_close();?>
	</div>
	<div class="panel-footer">
    	<div class="pull-right">
            <input type="hidden" name="cd_user" id="cd_user" value="<?php echo $user; ?>">
            <?php if ($operacao == 'APR') {?>
                    <button type="button" onclick="jAprova()" name="transdata" id="transdata" class="btn btn-success"><i class="fa fa-check"></i> Aprovar</button>
                <?php } elseif ($operacao == 'CAN') {?>
                    <button type="button" onclick="jCancela()" name="transdata" id="transdata" class="btn btn-danger"><i class="fa fa-check"></i> Cancelar</button>                    
            <?php } ?>
    	</div>
    	<div class="clearfix"></div>
    </div>    
</div>