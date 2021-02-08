<script type="text/javascript">
    $(document).ready(function () {    	    
    	$('#dtini, #dtfim').datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",            
            autoclose: true,
            orientation: "top left"            
            
        });      		    	    

    	$('#filial, #situacao, #fornecedor, #fornecedor, #contapr').selectpicker({    	       
            size: "auto"
    	    ,width: "150px"
    	    ,style:'btn-sm btn-default',
    	    iconBase: "fas",
    	    tickIcon: "fa-check",
    	    noneSelectedText: "Escolha",
    	    selectAllText: "Marcar Tudo",
    	    deselectAllText: "Desmarcar Tudo"
	    });
        
        $('#aprovador').selectpicker({
            size: "auto"
    	    ,width: "150px"
    	    ,style:'btn-sm btn-default',
            noneSelectedText: "Escolha"
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
	$("#busadt").click(function(){
    	$('#resultado').html('');
    	//$('#regiao').html('');
      	loading_show('load_adt');
      	dataString = $("#buscaoc").serialize();
      	$.ajax({
        	type: "POST",
        	url: "<?php echo base_url();?>index.php/conoc/carreg_oc",
        	data: dataString,        	
        	datatype:'json',
        	success: function(data){           
            	loading_hide('load_adt');
            	$('#resultado').html(data);            	            	            	          
			}
		}); 
    return false;  //stop the actual form post !important!
	});    
});
</script>
<script type="text/javascript">	
	function jVeItem(pedido, filial){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal		
		carregaDadosItemPed(pedido, filial);    		
		$('#mVeItem').modal();
	}

	function carregaDadosItemPed(pedido, filial){
		$.ajax({  
            type: "POST",  
            url : '<?php echo base_url(); ?>index.php/oc/busca_peditem/'+pedido+'/'+filial,              
            success: function(data){  
            	$('#tbitem').html(data); 
            }  
        }); 
	}
	
	function jVeAprovacao(emp, numapr, rotnap){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal			
		carregaDadosAprovadores(emp, numapr, rotnap);    		
		$('#mVeAprovador').modal('show');
	}

	function carregaDadosAprovadores(emp, numapr, rotnap){
		$.ajax({  
            type: "POST",  
            url : '<?php echo base_url(); ?>index.php/oc/busca_aprovador/'+emp+'/'+numapr+'/'+rotnap,              
            success: function(data){
                //alert(data);  
            	$('#tbapr').html(data); 
            }  
        }); 
	}

	function jVePendente(emp, numapr, rotnap, numocp, codfil){    		
		//antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal			
		carregaDadosPendentes(emp, numapr, rotnap, numocp, codfil);    		
		$('#mVePendente').modal('show');
	}

	function carregaDadosPendentes(emp, numapr, rotnap, numocp, codfil){
		$.ajax({  
            type: "POST",  
            url : '<?php echo base_url(); ?>index.php/oc/busca_pendente/'+emp+'/'+numapr+'/'+rotnap+'/'+numocp+'/'+codfil,              
            success: function(data){
                //alert(data);  
            	$('#tbpen').html(data); 
            }  
        }); 
	}

	function CurrencyFormat(number) {
	   var decimalplaces = 2;
	   var decimalcharacter = ",";
	   var thousandseparater = ".";
	   number = parseFloat(number);
	   var sign = number < 0 ? "-" : "";
	   var formatted = new String(number.toFixed(decimalplaces));
	   if( decimalcharacter.length && decimalcharacter != "." ) { formatted = formatted.replace(/\./,decimalcharacter); }
	   var integer = "";
	   var fraction = "";
	   var strnumber = new String(formatted);
	   var dotpos = decimalcharacter.length ? strnumber.indexOf(decimalcharacter) : -1;
	   if( dotpos > -1 )
	   {
	      if( dotpos ) { integer = strnumber.substr(0,dotpos); }
	      fraction = strnumber.substr(dotpos+1);
	   }
	   else { integer = strnumber; }
	   if( integer ) { integer = String(Math.abs(integer)); }
	   while( fraction.length < decimalplaces ) { fraction += "0"; }
	   temparray = new Array();
	   while( integer.length > 3 )
	   {
	      temparray.unshift(integer.substr(-3));
	      integer = integer.substr(0,integer.length-3);
	   }
	   temparray.unshift(integer);
	   integer = temparray.join(thousandseparater);
	   return sign + integer + decimalcharacter + fraction;
	}
	
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-custom">
				<div class="panel-heading">
			    	<h5><i class="fa fa-search"></i> Filtro Consulta de OCs</h5>
				</div>
				<div class="panel-body">
					<?php 
						$this->load->helper(array('form'));
						echo validation_errors(); 
						$attributes = array('id' => 'buscaoc', 'class' => 'form-horizontal'); 
						echo form_open('oc/busca', $attributes); 
					?>																
					  	<div class="form-group col-sm-2">
                                                    <label for="contapr" class="col-sm-12 small">Cont Aprovação:</label>
                                                    <div class="col-sm-3">
                                                        <select name="contapr" id="contapr" class="selectpicker">							    		
                                                                <option value="ANA">Em Análise</option>
                                                                <option value="APR">Aprovado</option>
                                                                <option value="CAN">Cancelado</option>
                                                                <option selected value="">Todos</option>							    								    	
                                                        </select>
                                                    </div>
                                                </div>
					  	<div class="form-group col-sm-2">
		    				<label for="fornecedor" class="col-sm-12 small">Fornecedor</label>
		    				<div class="col-sm-3 small">
		    				<?php 		    					
	    						foreach($fornec as $for)
	        						$optfor[$for->CODFOR] = $for->CODFOR.' - '.$for->APEFOR;
	    						echo form_dropdown('fornecedor[]', $optfor, 'class="selectpicker"', 'id="fornecedor" data-live-search="true" data-actions-box="true" multiple');
							?>
		    				</div>
	  					</div>
	  					<div class="form-group col-sm-2">
		    				<label for="filial" class="col-sm-12 small">Filial</label>
		    				<div class="col-sm-6">
		    				<?php 		    					
	    						foreach($filiais as $fil)
	        						$optfil[$fil->CODFIL] = $fil->CODFIL.' - '.$fil->USU_INSTAN.' - '.$fil->SIGFIL;
	    						echo form_dropdown('filial[]', $optfil, 'class="selectpicker"', 'id="filial" data-live-search="true" data-actions-box="true" multiple');
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
	    						echo form_dropdown('aprovador', $optuser, 'class="selectpicker"', 'id="aprovador" data-live-search="true" data-actions-box="false"');
							?>
		    				</div>
	  					</div>
                                                <div class="form-group col-sm-2">
                                                    <label for="situacao" class="col-sm-12 small">Situação:</label>
                                                    <div class="col-sm-3">
                                                        <select name="situacao" id="situacao" class="selectpicker">							    		
                                                                <option value="1">Aberto Total</option>
                                                                <option value="2">Aberto Parcial</option>
                                                                <option value="4">Liquidado</option>
                                                                <option value="5">Cancelado</option>
                                                                <option value="9">Não Fechado</option>                                                                
                                                                <option selected value="">Todos</option>							    								    	
                                                        </select>
                                                    </div>
                                                </div>
	  					<div class="form-group col-sm-2">
		    				<label for="pedido" class="col-sm-12 small">OC:</label>
	    					<div class="col-sm-10">
		    					<input type="text" class="form-control input-sm" id="pedido" name="pedido">
		    				</div>
  						</div>
  						<div class="form-group col-sm-3">
                        	<label for="estado" class="col-sm-10 small left">Emissão:</label>
                        	<div class="col-sm-6">
                        		<input type="text" class="form-control input-sm" name="dtini" id="dtini" value="<?php echo date('d/m/Y') ?>">
							</div>                        
                        	<div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="dtfim" id="dtfim" value="<?php echo date('d/m/Y') ?>">
                        	</div>
                        </div>	  						  						  										  						  				  					  					  						  						  						  					  					  		  					  						
	  			</div>		  																		
				<div class="panel-footer">
					<div class="row">      					
      					<div class="col-sm-4">        				     
        					<button type="submit" name="busadt" id="busadt" class="btn btn-success btn-sm" ><i class="fa fa-search"></i> Buscar</button>
        				</div>
        				<div id="load_adt" class="col-sm-4"></div>
        			</div>	
        			<?php echo form_close();?>
				</div>
			</div>
		</div>
		<div class="row-fluid">                    				
                    <div id="resultado"></div>			
		</div>
		<div class="clearfix"></div>									
	</div>	
</div>
<div class="clearfix"></div>
</div>

<!-- ------------------------------------------------------VE ITEM PEDIDO ----------------------------------------------------------------------------------------------------- -->	
	<div class="modal fade" id="mVeItem" >
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	      		<div class="modal-header modal-header-custom">
	        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        		<h4 class="modal-title">Itens do Pedido</h4>
	      		</div>
	      		<div class="modal-body">
	      	 		<div id="tbitem"></div>		    			   
	      		</div>
	      		<div class="modal-footer">	           
	      		</div>	      	
	    	</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 
	
<!-- ------------------------------------------------------VE APROVADORES ----------------------------------------------------------------------------------------------------- -->	
	<div class="modal fade" id="mVeAprovador" >
		<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header modal-header-custom">
	        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        		<h4 class="modal-title">Aprovações Realizadas</h4>
	      		</div>
	      		<div class="modal-body">
	      	 		<div id="tbapr"></div>		    			   
	      		</div>
	      		<div class="modal-footer">	           
	      		</div>	      	
	    	</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 
	
<!-- ------------------------------------------------------VE PENDENTES ----------------------------------------------------------------------------------------------------- -->	
	<div class="modal fade" id="mVePendente" >
		<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header modal-header-custom">
	        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
	        		<h4 class="modal-title">Aprovadores Pendentes</h4>
	      		</div>
	      		<div class="modal-body">
	      	 		<div id="tbpen"></div>		    			   
	      		</div>
	      		<div class="modal-footer">	           
	      		</div>	      	
	    	</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal --> 
	