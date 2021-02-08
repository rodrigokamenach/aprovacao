<script type="text/javascript">
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();  

    $('.dtprorroga').datepicker({
		format: "dd/mm/yyyy",
        language: "pt-BR",            
        autoclose: true,
        orientation: "top left" 
        
    });  

    $(":checkbox").click(function(){
	    //var options="";
	var options = $(this).attr('title');
    	var id = $(this).attr('name');
    	//alert(id);	        	
    	//classe = $(this).attr('class');
    	
	    if ($("input[type=checkbox][name^='"+id+"']").prop('checked')) {
	    	//$("input[type=text][name^='id["+options+"][dtpro]]").prop('disabled', '');
                    $('#dtpro'+options).prop('disabled', '');
                    $('#apr'+options).prop('disabled', '');
                    $('#dtemi'+options).prop('disabled', '');
                    $('#nap'+options).prop('disabled', '');
                    $('#niv'+options).prop('disabled', '');
                    $('#par'+options).prop('disabled', '');
                    $('#vlpar'+options).prop('disabled', '');
                    $('#seqobs'+options).prop('disabled', '');
	    	//$('#vlr'+id).val(options);	    	
		} else {
                    $('#dtpro'+options).prop('disabled', 'disabled');
                    $('#apr'+options).prop('disabled', 'disabled');
                    $('#dtemi'+options).prop('disabled', 'disabled');
                    $('#nap'+options).prop('disabled', 'disabled');
                    $('#niv'+options).prop('disabled', 'disabled');
                    $('#par'+options).prop('disabled', 'disabled');
                    $('#vlpar'+options).prop('disabled', 'disabled');
                    $('#seqobs'+options).prop('disabled', 'disabled');
			//$('#vlr'+id).val('');			
		}			   
	});	    		       	 	     
});	
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
		$attribute = array('id' => 'form_apr_ped', 'name' => 'form_apr_ped'); 
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
                    <?php } elseif ($operacao == 'CAP') {?>
                        <button type="button" onclick="jCancelaApr()" name="transdata" id="transdata" class="btn btn-warning"><i class="fa fa-check"></i> Cancelar Aprovação</button>
    		<?php } ?>
    	</div>
    	<div class="clearfix"></div>
    </div>    
</div>