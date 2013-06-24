    <!-- librerias externas -->
    <script language="javascript" type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script language="javascript" type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/d3/3.0.8/d3.min.js"></script>
    <script language="javascript" type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
    <script language="javascript" type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/datatables/1.9.4/jquery.dataTables.min.js"></script>

    <!-- funciones para la visualización -->
    <script language="javascript" type="text/javascript" src="/view/js/project/graphA.js"></script>
    <script language="javascript" type="text/javascript" src="/view/js/project/graphB.js"></script>

    <!-- estilos para la visualización -->
    <link rel="stylesheet" type="text/css" href="/view/css/project/widget/graph.css"/>	
    
    
    
    <div class="widget"> 
            <div id="project_selection" style="margin-bottom: 10px"></div>
            <div style="width: 295px; float: left">
                <h2>FINANCIACI&OacuteN OBTENIDA</h2>
                <div id="funded" class="obtenido number"></div>
                <div id="de" style="float: left; margin-left: 5px"></div>
                <div id="minimum" class="minimum number" style="margin-left: 5px"></div>
                <div id="euros" style="margin-left: 5px; float:left"></div>
            </div>
            <div style="width: 295px; float: left; text-align: right">
                <div style="font-weight: normal; font-size: 12px">QUEDAN<h2 id="dias"></h2>D&IacuteAS</div>
            </div>

            <div id="funds" style="clear: both"></div>
            <div>
                <h2>COFINANCIADORES</h2>
            </div>
            <div id="cofund" style="clear: both"></div>
    </div>
    <div class="table_widget"> 
            <div style="padding: 10px 10px 10px 20px;">
                <h2>TR&AacuteFICO</h2>
            </div>
			<div id="source_table" class="table">
					<div class="row head_row">
						<div class="column_head">ORIGIN</div>
						<div class="column_head">VISITAS</div>
						<div class="column_head">DONACIONES</div>
						<div class="column_head">DONACION MEDIA</div>
					</div>
				<div id="source_table_body">
				</div>
			</div>
    </div>
    
<script type="text/javascript">
    /* función para cargar los datos del gáfico, sacado de graphA.js */
    $(function(){
        updateGraph(<?php echo $this['project']->id; ?>); 
        // Initialize
        loadData();
    });

</script>        
