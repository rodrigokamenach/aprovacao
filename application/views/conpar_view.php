<script type="text/javascript">
    $(document).ready(function () { 
        $('#dtini, #dtfim').datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true,
            orientation: "top left"

        });
        
        $('#filial, #fornecedor, #aprovador, #situacao').selectpicker({    	       
            size: "auto"
    	    ,width: "150px"
    	    ,style:'btn-sm btn-default',
    	    iconBase: "fas",
    	    tickIcon: "fa-check",
    	    noneSelectedText: "Escolha",
    	    selectAllText: "Marcar Tudo",
    	    deselectAllText: "Desmarcar Tudo"
        });
    });
</script>
<script type="text/javascript">
function loading_show(e) {
    $('#'+e).html("<img src='<?php echo base_url();?>/assets/img/ajax_loader_blue_32.gif'/>").fadeIn('fast');
}
//Aqui desativa a imagem de loading
function loading_hide(e) {
    $('#'+e).fadeOut('fast');
}

$(function(){
	$("#buspar").click(function(){
    	$('#resultado').html('');
    	//$('#regiao').html('');
      	loading_show('load_par');
      	dataString = $("#buscapar").serialize();
      	$.ajax({
        	type: "POST",
        	url: "<?php echo base_url();?>index.php/conpar/carreg_par",
        	data: dataString,        	
        	datatype:'json',
        	success: function(data){           
            	loading_hide('load_par');
            	$('#resultado').html(data);            	            	            	          
			}
		}); 
    return false;  //stop the actual form post !important!
	});    
});

</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-custom">
				<div class="panel-heading">
			    	<h5><i class="fa fa-search"></i> Filtro Consulta de Parcerias</h5>
				</div>
				<div class="panel-body">
                                <?php 
                                    $this->load->helper(array('form'));
                                    echo validation_errors(); 
                                    $attributes = array('id' => 'buscapar', 'class' => 'form-horizontal'); 
                                    echo form_open('conpar/carreg_par', $attributes); 
                                ?>	
                                    <div class="form-group col-sm-2">
                                        <label for="cif" class="col-sm-12 small">Fornecedor</label>
                                        <div class="col-sm-3 small">
                                            <?php
                                            foreach ($fornec as $c) {
                                                $optfor[$c->CODFOR] = $c->CODFOR . ' - ' . $c->APEFOR;
                                            }
                                            echo form_dropdown('fornecedor[]', $optfor, 'class="selectpicker show-tick"', 'id="fornecedor" data-live-search="true" data-actions-box="true" multiple');                            
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <label for="filial" class="col-sm-12 small">Filial</label>
                                        <div class="col-sm-6">
                                            <?php
                                            foreach ($filiais as $fil) {
                                                $optfil[$fil->CODFIL] = $fil->CODFIL . ' - ' . $fil->USU_INSTAN . ' - ' . $fil->SIGFIL;
                                            }
                                            echo form_dropdown('filial[]', $optfil, 'class="selectpicker show-tick"', 'id="filial" data-live-search="true" data-actions-box="true" multiple');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-2">
                                    <label for="aprovador" class="col-sm-12 small">Aprovador</label>
                                    <div class="col-sm-6">
                                    <?php 	
                                            $optuser[] = 'Todos';
                                            foreach($users as $u) {
                                                $optuser[$u->CODUSU] = $u->CODUSU.' - '.$u->NOMUSU;}
                                            echo form_dropdown('aprovador', $optuser, 'class="selectpicker"', 'id="aprovador" data-live-search="true" data-actions-box="true"');
                                            ?>
                                    </div>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <label for="situacao" class="col-sm-12 small">Situação:</label>
                                        <div class="col-sm-3">
                                            <select name="situacao" id="situacao" class="selectpicker">							    		
                                                    <option value="APR">Aprovado</option>
                                                    <option value="AGU">Aguard. Aprov</option>                                                                                                                               
                                                    <option selected value="">Todos</option>							    								    	
                                            </select>
                                        </div>
                                    </div>	  					
                                    <div class="form-group col-sm-3">
                                        <label for="estado" class="col-sm-10 small left">Vencimento:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-sm" name="dtini" id="dtini" value="">
                                        </div>                        
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-sm" name="dtfim" id="dtfim" value="">
                                        </div>
                                    </div>	  						  						  										  						  				  					  					  						  						  						  					  					  		  					  						
	  			</div>		  																		
				<div class="panel-footer">
                                    <div class="row">      					
                                    <div class="col-sm-4">        				     
                                            <button type="submit" name="buspar" id="buspar" class="btn btn-success btn-sm" ><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                    <div id="load_par" class="col-sm-4"></div>
        			</div>	
        			<?php echo form_close();?>
				</div>
			</div>
		</div>
		<div class="row-fluid">
                    <div class="col-md-12">		
                        <div id="resultado"></div>
                    </div>
                </div>
                <div class="clearfix"></div>									
	</div>	
</div>
<div class="clearfix"></div>
</div>