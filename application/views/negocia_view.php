<?php  
if ($result) {
    echo json_encode($result);
?>
                          
<script type="text/javascript">
        $(document).ready(function () {
            var sum = $.pivotUtilities.aggregatorTemplates.min;
            var numberFormat = $.pivotUtilities.numberFormat;
            var intFormat = numberFormat({digitsAfterDecimal: 2,thousandsSep:".", decimalSep:","});
            //var derivers = $.pivotUtilities.derivers;           
            $("#divPivotGrid").pivot(                    
                <?php echo json_encode($result); ?>,
                {
                    rows: ["Item", "Qtde"],
                    cols: ["Fornecedor"],
                    vals: ["PRECOT"],
                    aggregator: sum(intFormat)(["PRECOT"]),
                    //renderers: $.pivotUtilities.renderers,                    
                    aggregatorName: 'Minimum',
                    hideTotals: true                  
                   // rendererName: "Heatmap"
                }
            );
            
        });
</script>
<div id="divPivotGrid"></div>
<?php
} else {
    echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

