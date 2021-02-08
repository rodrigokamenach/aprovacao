<script type="text/javascript">
$(document).ready(function(){
    //$('[data-toggle="tooltip"]').tooltip();  
        
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
		 <div class="container-fluid scroll">
		 	<?php echo $tabela; ?>
		</div>		
	<?php echo form_close();?>
	</div>
	<div class="panel-footer">    	
    	<div class="clearfix"></div>
    </div>    
</div>