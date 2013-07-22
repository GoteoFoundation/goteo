<?php
$project = $this['project'];

if (!$project instanceof  Goteo\Model\Project) {
    return;
}
?>
<!-- librerias externas -->
<!--    <script language="javascript" type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>  -->
    <script language="javascript" type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/d3/3.0.8/d3.min.js"></script>
    <script language="javascript" type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
    <script language="javascript" type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/datatables/1.9.4/jquery.dataTables.min.js"></script>

    <!-- funciones para la visualización -->
    <script language="javascript" type="text/javascript" src="/view/js/project/chart.js"></script>
    <script language="javascript" type="text/javascript" src="/view/js/project/visualizers.js"></script>
    <script language="javascript" type="text/javascript" src="/view/js/project/display.js"></script>

    <!-- estilos para la visualización -->
    <link rel="stylesheet" type="text/css" href="/view/css/dashboard/projects/graph.css"/>	
    
    
    
    <div class="widget"> 
            <div id="project_selection" style="margin-bottom: 10px"></div>
            <div class="titles">
                <div>
                    <h2>FINANCIACI&OacuteN OBTENIDA</h2>
                    <div id="funded" class="obtenido number"></div>
                    <div id="de" class="de"></div>
                    <div id="minimum" class="minimum number"></div>
                    <div id="euros" class="euros"></div>
                </div>
                <div class="quedan">
                    <div style="font-weight: normal; font-size: 12px">QUEDAN<h2 id="dias" style="display:inline"></h2>D&IacuteAS</div>
                </div>
            </div>
            <div id="funds" class="chart_div"></div>
            <div>
                <h2>COFINANCIADORES</h2>
            </div>
            <div id="cofund" class="chart_div"></div>
    </div>
    
<script type="text/javascript">
    /* función para cargar los datos del gáfico, sacado de graphA.js */
jQuery(document).ready(function(){
        GOTEO.initializeGraph(<?php echo $this['data']; ?>); 
    });

</script>