<?php

use Goteo\Core\View,
    Goteo\Library\Text;

// en la página de cofinanciadores, paginación de 20 en 20
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($this['list'], 9, isset($_GET['page']) ? $_GET['page'] : 1);



$bodyClass = 'discover';

include 'view/prologue.html.php';

include 'view/header.html.php' ?>

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
                <h2 class="title"><?php echo $this['title']; ?></h2>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['message'])) : ?><p><?php echo $this['message'] ?></p><?php endif;  ?>

            <div class="widget projects">
                <?php while ($project = $pagedResults->fetchPagedRow()) :
                        echo new View('view/project/widget/project.html.php', array(
                            'project' => $project
                            ));
                endwhile; ?>
            </div>

            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>

        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>