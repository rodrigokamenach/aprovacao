<script>
     $(document).ready(function () {
         $('#dtini').datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true,
            orientation: "top left"

        });

        $('#dtfim').datepicker({
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
        
         $('#filial').selectpicker({
            size: "auto"
            , width: "150px"
            , style: 'btn-sm btn-default',
            iconBase: "fa",
            tickIcon: "fa-check",
            noneSelectedText: "Escolha",
            selectAllText: "Marcar Tudo",
            deselectAllText: "Desmarcar Tudo"
        });

        $('#cif').selectpicker({
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
        $("#busfol").click(function () {
            $('#resultado').html('');
            //$('#regiao').html('');
            loading_show('load_fol');
            dataString = $("#buscafolha").serialize();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/folha/carreg_folha",
                data: dataString,
                datatype: 'json',
                success: function (data) {
                    loading_hide('load_fol');
                    $('#resultado').html(data);
                }
            });
            return false;  //stop the actual form post !important!
        });
    });
</script>
<script type="text/javascript">
    function jVeItens(cif, filial, dtini, dtfim, dtvnc) {
        $('#tbitem').html('');
        //alert(dtvnc);
        //antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal		
        carregaDadosItem(cif, filial, dtini, dtfim, dtvnc);
        $('#mVeItem').modal('show');
    }
    
    function carregaDadosItem(cif, filial, dtini, dtfim, dtvnc) {
        loading_show('load_folha');
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>index.php/folha/busca_folitem",
            data: { cif: cif, filial: filial, dtini: dtini, dtfim: dtfim, dtvnc: dtvnc },
            datatype: 'json',
            success: function (data) {
                loading_hide('load_folha');
                $('#tbitem').html(data);
            }
        });        
    }
    
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
                    <h5><i class="fa fa-search"></i> Filtro Aprovação de Folha de Pagamento</h5>
                </div>
                <div class="panel-body">
                <?php
                    $this->load->helper(array('form'));
                    echo validation_errors();
                    $attributes = array('id' => 'buscafolha', 'class' => 'form-horizontal');
                    echo form_open('folha/carreg_folha', $attributes);
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
                        <label for="cif" class="col-sm-12 small">CIF</label>
                        <div class="col-sm-3 small">
                            <?php
                            foreach ($cif as $c) {
                                $optcif[$c->CODCIF] = $c->CODCIF . ' - ' . $c->DESCIF;
                            }
                            echo form_dropdown('cif[]', $optcif, 'class="selectpicker show-tick"', 'id="cif" data-live-search="true" data-actions-box="true" multiple');                            
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
                        <label for="estado" class="col-sm-10 small left">Período:</label>
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
                            <button type="submit" name="busadt" id="busfol" class="btn btn-success" ><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div id="load_fol" class="col-sm-4"></div>
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

<!-- ------------------------------------------------------VE ITEM PEDIDO ----------------------------------------------------------------------------------------------------- -->	
<div class="modal fade" id="mVeItem" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                <h4 class="modal-title">Itens da Folha</h4>
            </div>
            <div class="modal-body">
                <div id="load_folha" class="col-sm-4"></div>
                <div id="tbitem"></div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">	           
            </div>	      	
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 

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
