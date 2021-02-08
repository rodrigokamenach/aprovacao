<script type="text/javascript">
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

        $('#contapr').selectpicker({
            size: "auto"
            , width: "150px"
            , style: 'btn-sm btn-default'
        });

        $('#filial, #fornecedor').selectpicker({
            size: "auto"
            , width: "150px"
            , style: 'btn-sm btn-default',
            iconBase: "fas",
            tickIcon: "fa-check",
            noneSelectedText: "Escolha",
            selectAllText: "Marcar Tudo",
            deselectAllText: "Desmarcar Tudo"
        });       

        $(":checkbox").click(function () {
            //var options="";
            var options = $(this).attr('title');
            var id = $(this).attr('name');
            //alert(options);	        	
            //classe = $(this).attr('class');

            if ($("input[type=checkbox][name^='" + id + "']").prop('checked')) {
                $('#dtpro' + options).prop('disabled', '');
                $('#apr' + options).prop('disabled', '');
                $('#dtemi' + options).prop('disabled', '');
                $('#nap' + options).prop('disabled', '');
                $('#niv' + options).prop('disabled', '');
                $('#par' + options).prop('disabled', '');
                $('#vlpar' + options).prop('disabled', '');
                $('#seqobs' + options).prop('disabled', '');
                //$('#vlr'+id).val(options);	    	
            } else {
                $('#dtpro' + options).prop('disabled', 'disabled');
                $('#apr' + options).prop('disabled', 'disabled');
                $('#dtemi' + options).prop('disabled', 'disabled');
                $('#nap' + options).prop('disabled', 'disabled');
                $('#niv' + options).prop('disabled', 'disabled');
                $('#par' + options).prop('disabled', 'disabled');
                $('#vlpar' + options).prop('disabled', 'disabled');
                $('#seqobs' + options).prop('disabled', 'disabled');
                //$('#vlr'+id).val('');			
            }
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
        $("#busadt").click(function () {
            $('#resultado').html('');
            //$('#regiao').html('');
            loading_show('load_adt');
            dataString = $("#buscaoc").serialize();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/oc/carreg_oc",
                data: dataString,
                datatype: 'json',
                success: function (data) {
                    loading_hide('load_adt');
                    $('#resultado').html(data);
                }
            });
            return false;  //stop the actual form post !important!
        });
    });
</script>
<script type="text/javascript">
    function jVeItem(pedido, filial) {
        //antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal		
        carregaDadosItemPed(pedido, filial);
        $('#mVeItem').modal('show');
    }
    
    function carregaDadosItemPed(pedido, filial) {
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>index.php/oc/busca_peditem/' + pedido + '/' + filial,
            success: function (data) {
                $('#tbitem').html(data);
            }
        });
    }
    
    function jVeItemAlt(pedido, filial, codnap, tempar) {
        //antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal		
        carregaDadosItemPedAlt(pedido, filial, codnap, tempar);
        $('#mVeItem').modal('show');
    }

    function carregaDadosItemPedAlt(pedido, filial, codnap, tempar) {
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>index.php/oc/busca_peditemalt/' + pedido + '/' + filial + '/' + codnap + '/' + tempar,
            success: function (data) {
                $('#tbitem').html(data);
            }
        });
    }

    function tudo() {
        check = $("#checkcol");
        if (check.prop("checked")) {
            $(':checkbox').prop('checked', '');
            $('.campo').prop('disabled', 'disabled');
            $('.dtprorroga').prop('disabled', 'disabled');
        } else {
            $(':checkbox').prop('checked', 'checked');
            $('.campo').prop('disabled', '');
            $('.dtprorroga').prop('disabled', '');
        }
    }
    
    function jVeNegocia(emp, oc) {
        //antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal	
        $('#tbneg').html('');
        carregaDadosNegocia(emp, oc);
        $('#mVeNegocia').modal('show');
    }
    
    function carregaDadosNegocia(emp, oc) {
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>index.php/oc/busca_negocia/' + emp + '/' + oc,
            success: function (data) {
                //alert(data);  
                $('#tbneg').html(data);
            }
        });
    }

    function jVeAprovacao(emp, numapr, rotnap) {
        //antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal			
        carregaDadosAprovadores(emp, numapr, rotnap);
        $('#mVeAprovador').modal('show');
    }

    function carregaDadosAprovadores(emp, numapr, rotnap) {
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>index.php/oc/busca_aprovador/' + emp + '/' + numapr + '/' + rotnap,
            success: function (data) {
                //alert(data);  
                $('#tbapr').html(data);
            }
        });
    }

    function jVePendente(emp, numapr, rotnap, numocp, codfil) {
        //antes de abrir a janela, preciso carregar os dados do cliente e preencher os campos dentro do modal			
        carregaDadosPendentes(emp, numapr, rotnap, numocp, codfil);
        $('#mVePendente').modal('show');
    }

    function carregaDadosPendentes(emp, numapr, rotnap, numocp, codfil) {
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>index.php/oc/busca_pendente/' + emp + '/' + numapr + '/' + rotnap + '/' + numocp + '/' + codfil,
            success: function (data) {
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

        if (check.length == 0) {
            bootbox.alert({
                size: 'small',
                "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um pedido!</h5>',
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
                        'Quantidade de Pedidos: <strong>' + check.length + '</strong>' +
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
                            dataString = $("#form_apr_ped").serialize();
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
                                url: "<?php echo base_url(); ?>index.php/oc/aprovar",
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
            //$("#pedidos").val(check);
            //document.getElementById('vpedidos').innerHTML= check.length+ ' itens selecionados.';;					   
            //$('#mLancaColetaAgrup').modal('show');
        }
        //alert(soma);
    }
    
    function jCancela() {
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

        if (check.length == 0) {
            bootbox.alert({
                size: 'small',
                "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um pedido!</h5>',
                "className": "alert-modal"
            });
        } else {
            bootbox.dialog({
                title: 'Confirma Cancelamento',
                message: '<div class="row">  ' +
                        '<div class="col-md-12">' +
                        '<h3 class="alert alert-danger">' +
                        'Valor Total: <strong>R$ ' + soma + '</strong></h3>' +
                        '<h3 class="alert alert-warning">' +
                        'Quantidade de Pedidos: <strong>' + check.length + '</strong>' +
                        '<h3></div>' +
                        '<div class="form-group col-sm-12">'+
                        '<form action="#" id="formobs" class="form-horizontal" method="post">' +                        
                        '<label for="pedido" class="col-sm-12 small">Observação:</label>'+
                        '<div class="col-sm-10">'+
                        '<input type="text" class="form-control input-sm" id="obs" name="obs">' +
                        '</div> </div></form>'+
                        '</div>',
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-danger'
                    },
                    success: {
                        'label': 'Confirma',
                        'className': "btn-success",
                        'callback': function () {
                            dataString = $("#form_apr_ped, #formobs").serialize();
                            $.blockUI({
                                message: '<h5><i class="fa fa-refresh fa-spin"></i>  Processando Cancelamentos!<br>Aguarde....</h5>',
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
                                url: "<?php echo base_url(); ?>index.php/oc/cancelar",
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
            //$("#pedidos").val(check);
            //document.getElementById('vpedidos').innerHTML= check.length+ ' itens selecionados.';;					   
            //$('#mLancaColetaAgrup').modal('show');
        }
        //alert(soma);
    }
    
    function jCancelaApr() {
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

        if (check.length == 0) {
            bootbox.alert({
                size: 'small',
                "message": '<h5 class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Selecione ao menos um pedido!</h5>',
                "className": "alert-modal"
            });
        } else {
            bootbox.dialog({
                title: 'Confirma Cancelamento de Aprovações',
                message: '<div class="row">  ' +
                        '<div class="col-md-12">' +
                        '<h3 class="alert alert-danger">' +
                        'Valor Total: <strong>R$ ' + soma + '</strong></h3>' +
                        '<h3 class="alert alert-warning">' +
                        'Quantidade de Pedidos: <strong>' + check.length + '</strong>' +
                        '<h3></div>',                        
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-danger'
                    },
                    success: {
                        'label': 'Confirma',
                        'className': "btn-success",
                        'callback': function () {
                            dataString = $("#form_apr_ped, #formobs").serialize();
                            $.blockUI({
                                message: '<h5><i class="fa fa-refresh fa-spin"></i>  Processando Cancelamento das Aprovações!<br>Aguarde....</h5>',
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
                                url: "<?php echo base_url(); ?>index.php/oc/cancelarapr",
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
            //$("#pedidos").val(check);
            //document.getElementById('vpedidos').innerHTML= check.length+ ' itens selecionados.';;					   
            //$('#mLancaColetaAgrup').modal('show');
        }
        //alert(soma);
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-custom">
                <div class="panel-heading">
                    <h5><i class="fa fa-search"></i> Filtro Aprovação de OCs</h5>
                </div>
                <div class="panel-body">
                <?php
                    $this->load->helper(array('form'));
                    echo validation_errors();
                    $attributes = array('id' => 'buscaoc', 'class' => 'form-horizontal');
                    echo form_open('oc/carreg_oc', $attributes);
                ?>										
                    <div class="form-group col-sm-2">
                        <label for="operacao" class="col-sm-12 small">Operação</label>
                        <div class="col-sm-3">
                            <select name="operacao" id="operacao" class="selectpicker">
                                <option value="APR">Aprovar</option>
                                <option value="CAN">Cancelar OC</option>
                                <option value="CAP">Cancelar Aprovação</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="contapr" class="col-sm-12 small">Cont Aprovação:</label>
                        <div class="col-sm-3">
                            <select name="contapr" id="contapr" class="selectpicker">							    		
                                <option value="ANA">Em Análise</option>
                                <option value="APR">Aprovado</option>
                                <option value="CAN">Cancelado</option>
                                <option value="">Todos</option>							    								    	
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="fornecedor" class="col-sm-12 small">Fornecedor</label>
                        <div class="col-sm-3 small">
                            <?php
                            foreach ($fornec as $for)
                                $optfor[$for->CODFOR] = $for->CODFOR . ' - ' . $for->APEFOR;
                            echo form_dropdown('fornecedor[]', $optfor, 'class="selectpicker"', 'id="fornecedor" data-live-search="true" data-actions-box="true" multiple');
                            ?>
                        </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="filial" class="col-sm-12 small">Filial</label>
                        <div class="col-sm-6">
                            <?php
                            foreach ($filiais as $fil)
                                $optfil[$fil->CODFIL] = $fil->CODFIL . ' - ' . $fil->USU_INSTAN . ' - ' . $fil->SIGFIL;
                            echo form_dropdown('filial[]', $optfil, 'class="selectpicker"', 'id="filial" data-live-search="true" data-actions-box="true" multiple');
                            ?>
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
                            <button type="submit" name="busadt" id="busadt" class="btn btn-success" ><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div id="load_adt" class="col-sm-4"></div>
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

<!-- ------------------------------------------------------VE NEGOCIACAO ----------------------------------------------------------------------------------------------------- -->	
<div class="modal fade" id="mVeNegocia" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                <h4 class="modal-title">Negociação</h4>
            </div>
            <div class="modal-body">
                <div id="tbneg"></div>		    			   
            </div>
            <div class="modal-footer">	           
            </div>	      	
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 