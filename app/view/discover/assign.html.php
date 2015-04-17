<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

// en la página de cofinanciadores, paginación de 20 en 20
$pagedResults = new Paginated($vars['list'], 9, isset($_GET['page']) ? $_GET['page'] : 1);



$bodyClass = 'discover';

include __DIR__ . '/../prologue.html.php';

include __DIR__ . '/../header.html.php' ?>

<script type="text/javascript">
function projAssign(projId) {
	//llamar al identificador de sesion
	$.getJSON('/json/assign_proj_call/'+projId,function(data){
		if(data.assigned) {
            $("#assign_"+projId).html('<span style="color:red;"><?php echo Text::get('regular-call-assigned'); ?></span>');
		} else {
            alert('<?php echo Text::get('assign-call-failed'); ?>');
        }
	});


    return false;
}
</script>

        <div id="sub-header">
            <div>
                <h2 class="title"><?php echo $vars['title']; ?></h2>
            </div>

        </div>

<?php if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; } ?>

        <div id="main">

            <div class="widget projects">
                <?php while ($project = $pagedResults->fetchPagedRow()) :
                        echo View::get('project/widget/project.html.php', array(
                            'project' => $project
                            ));
                endwhile; ?>
            </div>

            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>

        </div>

        <?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
