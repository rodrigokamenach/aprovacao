<script>
     $(document).ready(function () {
         $('#dtini, #dtfim').datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true,
            orientation: "top left"

        });
              
         $('#operacao').selectpicker({
            size: "auto"
            , width: "150px"
            , style: 'btn-sm btn-default'
        });
        
         $('#filial, #fornecedor').selectpicker({
            size: "auto"
            , width: "150px"
            , style: 'btn-sm btn-default',
            iconBase: "fa",
            tickIcon: "fa-check",
            noneSelectedText: "Escolha",
            selectAllText: "Marcar Tudo",
            deselectAllText: "Desmarcar Tudo"
        });                
        
     });
</script>
<script type="text/javascript">
    function loading_show(e) {
        $('#' + e).html("<img src='<?php echo base_url(); ?>/assets/img/ajax_loader_blue_32.gif'/>").fadeIn('fast');
    }
//Aqui desativa a imagem de loading
    function loading_hide(e) {
        $('#' + e).fadeOut('fast');
    }

    $(function () {
        $("#buspar").click(function () {
            $('#resultado').html('');
            //$('#regiao').html('');
            loading_show('load_par');
            dataString = $("#buscaparc").serialize();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/parceria/carreg_parc",
                data: dataString,
                datatype: 'json',
                success: function (data) {
                    loading_hide('load_par');
                    $('#resultado').html(data);
                }
            });
            return false;  //stop the actual form post !important!
        });
    });
</script>
<script type="text/javascript">        
    function tudo() {
        check = $("#checkcol");
        if (check.prop("checked")) {
            $(':checkbox').prop('checked', '');
            $('.campo').prop('disabled', 'disabled');            
        } else {
            $(':checkbox').prop('checked', 'checked');
            $('.campo').prop('disabled', '');            
        }
    }
    
    function CurrencyFormat(number) {
        var decimalplaces = 2;
        var decimalcharacter = ",";
        var thousandseparater = ".";
        number = parseFloat(number);
        var sign = number < 0 ? "-" : "";
        var formatted = new String(number.toFixed(decimalplaces));
        if (decimalcharacter.length && decimalcharacter != ".") {
            formatted = formatted.replace(/\./, decimalcharacter);
        }
        var integer = "";
        var fraction = "";
        var strnumber = new String(formatted);
        var dotpos = decimalcharacter.length ? strnumber.indexOf(decimalcharacter) : -1;
        if (dotpos > -1)
        {
            if (dotpos) {
                integer = strnumber.substr(0, dotpos);
            }
            fraction = strnumber.substr(dotpos + 1);
        } else {
            integer = strnumber;
        }
        if (integer) {
            integer = String(Math.abs(integer));
        }
        while (fraction.length < decimalplaces) {
            fraction += "0";
        }
        temparray = new Array();
        while (integer.length > 3)
        {
            temparray.unshift(integer.substr(-3));
            integer = integer.substr(0, integer.length - 3);
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
                    <h5><i class="fa fa-search"></i> Filtro Aprovação de Parceria</h5>
                </div>
                <div class="panel-body">
                <?php
                    $this->load->helper(array('form'));
                    echo validation_errors();
                    $attributes = array('id' => 'buscaparc', 'class' => 'form-horizontal');
                    echo form_open('parceria/carreg_parc', $attributes);
                ?>										
                    <div class="form-group col-sm-2">
                        <label for="operacao" class="col-sm-12 small">Operação</label>
                        <div class="col-sm-3">
                            <select name="operacao" id="operacao" class="selectpicker">
                                <option value="APR">Aprovar</option>
<!--                                <option value="CAN">Cancelar</option>							    							    	-->
                            </select>
                        </div>
                    </div>                    
                    <div class="form-group col-sm-2">
                        <label for="cif" class="col-sm-12 small">Fornecedor</label>
                        <div class="col-sm-3 small">
                            <?php
                            foreach ($fornec as $for) {
                                $optfor[$for->CODFOR] = $for->CODFOR . ' - ' . $for->APEFOR;
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
                    <div class="form-group col-sm-3">
                        <label for="estado" class="col-sm-10 small left">Data de Vencimento:</label>
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
                            <button type="submit" name="buspar" id="buspar" class="btn btn-success" ><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div id="load_par" class="col-sm-4"></div>
                    </div>	
                <?php echo form_close(); ?>
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


<!-- ------------------------------------------------------Retorno ----------------------------------------------------------------------------------------------------- -->	
<div class="modal fade" id="mRetorno" >
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                <h4 class="modal-title">Resultado da Aprovação</h4>
            </div>
            <div class="modal-body">	         		      						  	 
                <div id="retorno"></div>		 		  		 		   			    
            </div>
            <div class="modal-footer">
                <div class="row-fluid">
                    <div id="load" class="col-sm-2"></div>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>	       
                </div>	        		       
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 
