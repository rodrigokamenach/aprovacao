<?php  
if ($result) {
    //echo json_encode($result);
?>
<script type="text/javascript">
        $(document).ready(function () {
            var jsonData = <?php echo json_encode($result); ?>;
            var source =
            {
                localdata: jsonData,
                datatype: "json",
                datafields:
                [
                    { name: 'FILIAL', type: 'string' },
                    { name: 'AREA', type: 'string' },
                    { name: 'REA', type: 'number' },
                    { name: 'ORC', type: 'number' }                    
                ]
            };
            var dataAdapter = new $.jqx.dataAdapter(source);
            dataAdapter.dataBind();
            var pivotDataSource = new $.jqx.pivot(
                dataAdapter,
                {
                    pivotValuesOnRows: false,
//                    customAggregationFunctions: {
//                        'printMoeda': function (value) {
//                          // console.log("this is console.log for the date");
//                          return value;
//                          alert(values[0]);
//                        }
//                    },                    
                    rows: [{ dataField: 'FILIAL' }],
                    columns: [{ dataField: 'AREA' }],                    
                    values: [                                               
                        { dataField: 'REA', width: 90, 'function': 'sum', text: 'Realizado', formatSettings: { align: 'right', prefix: '', decimalPlaces: 2, decimalSeparator: ',', thousandsSeparator: '.' } },
                        { dataField: 'ORC', width: 90, 'function': 'sum', text: 'Orçado', formatSettings: { align: 'right', prefix: '', decimalPlaces: 2, decimalSeparator: ',', thousandsSeparator: '.' } }                        
                    ]
                });
            $('#pivotccu').jqxPivotGrid(
            {
                source: pivotDataSource,
                treeStyleRows: false,
                autoResize: false,
                multipleSelectionEnabled: true,
                theme: 'office',
                itemsRenderer: function (pivotItem) {
                        var backgroundColor = pivotItem.isColumn ? 'rgb(212, 212, 212)' : 'rgb(212, 212, 212)';
                        if (pivotItem.isSelected) 
                            backgroundColor = pivotItem.isColumn ? 'rgb(132, 132, 132)' : 'rgb(132, 132, 132)';                                                
                        return "<div style='background: "
                            +  backgroundColor
                            + "; width: 100%; height: 100%; padding: 4px; color: #000; font-weight: bold '>"
                            + pivotItem.text
                            //+ sortElement
                            + "</div>";
                    }
            });
        });
</script>
<div class="col-md-7">
    <div class="panel panel-custom">
        <div class="panel-heading">
            <h5><i class="fas fa-chart-bar"></i> Resumo por Centro de Custo do Período(Mês Fechado)</h5>
        </div>
        <div class="panel-body">
            <div id="pivotccu"  style="height: 350px; width: 100%;"></div>
        </div>
    </div>
</div>
<?php
} else {
    echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Não existem dados para exibir.</div>';
}