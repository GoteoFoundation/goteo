<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$contract = $this['contract'];
$show = $this['show']; // estado del proceso (ver conroller/dashboard/projects::prepare_contract ) 

//esto es una tnteria temporal, esto debería ir en una página institucional o algo así
function wprint ($title, $message) {
    echo '<div class="widget projects"><h2 class="title">'.$title.'</h2><p>'.$message.'</p></div>';
}

switch ($show) {
    case 'payed': // ya se ha realizado el pago
        wprint('Proyecto en campaña', 'El proyecto está en campaña, aun no hay contrato.');
		break;
    
    case 'recieved': // goteo ha recibido el contrato firmado
        wprint('Contrato recibido', 'Hemos  recibido el contrato firmado, está en cola para revisión.');
		break;
    
    case 'ready': // el pdf está listo para descargarse
        wprint('Contrato listo', 'Ya puedes imprimir el pdf, firmar todas las hojas y enviárnoslo a ....</p><p><a href="/contract/'.$contract->project.'" target="_blank" class="button">PDF</a>');
		break;
    
    case 'review': // los datos están siendo revisados por el admín
        wprint('Datos en revisión', 'Los datos del contrato y la documentación están siendo revisados. Si todo está correcto habilitaremos el pdf. Si hay algo que aclarar te contactaremos por email.</p><p>Si quieres puedes consultar el documento PROVISIONAL <a href="/contract/'.$contract->project.'" target="_blank" class="button">aquí</a>');
		break;
    
    case 'closed': // el formuladio está cerrado, el contrato está en cola para ser revisado
        wprint('Formulario cerrado', 'La edición de datos está cerrada, a continuación puedes ver los datos introducidos.<br />Si hay alguna incorrección ponte en contacto con nosotros enviando un email a <a href="mailto:info@goteo.org">info@goteo.org</a>');
        echo new View('view/contract/widget/review.html.php', array('contract'=>$contract));
        break;
    
    case 'edit': // hay registro y se puede editar
        wprint('Pendiente de datos', 'Rellena el siguiente formulario --> <a href="/contract/edit/'.$contract->project.'" target="_blank" class="button">Datos de contrato</a>');
		break;
	
    case 'campaign': // el proyecto sigue en campaña, aun no se puede gestionar el contrato
        wprint('Proyecto en campaña', 'El proyecto está en campaña, aun no hay contrato.');
		break;
    
    case 'off': // el proyecto aun no ha sido publicado
    default: // off
        wprint('Proyecto no publicado', 'El proyecto aun no ha sido publicado, no hay nada que hacer aquí.');
        
        break;
}


/* 
 * Si hubiera que mostrarle el informe financiero... 
?>
<div class="widget projects">
    <h2 class="title">Informe financiero</h2>
    
    <p>Este es el informe final de financiación en goteo</p>
    <?php echo new View('view/project/report.html.php', array('project'=>$project, 'Data'=>$this['Data'])); ?>
</div>
<?php endif; 
 */
