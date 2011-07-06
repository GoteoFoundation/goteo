<?php 

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;
    
$bodyClass = 'project-edit';

$project = $this['project'];

$status = new View('view/project/edit/status.html.php', array('status' => $this['project']->status, 'progress' => $this['project']->progress));
$steps  = new View('view/project/edit/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step'], 'errors' => $this['project']->errors));

include 'view/prologue.html.php';
    
    include 'view/header.html.php'; ?>

    <div id="sub-header">
        <div class="project-header">
            <a href="/user/<?php echo $project->owner; ?>" target="_blank"><img src="/image/<?php echo $project->user->avatar->id; ?>/50/50" /></a>
            <h2><span><?php echo htmlspecialchars($project->name) ?></span></h2>
            <div class="project-by"><a href="/user/<?php echo $project->owner; ?>" target="_blank">Por: <?php echo $project->user->name; ?></a></div>
        </div>
    </div>

    <div id="main" class="<?php echo htmlspecialchars($this['step']) ?>">
        
        <form method="post" action="<?php echo SITE_URL . "/project/edit/" . $this['project']->id ?>" class="project" enctype="multipart/form-data" >
                        
            <input type="hidden" name="view-step-<?php echo $this['step'] ?>" value="please" />
            
            <?php echo $status ?>
            <?php echo $steps ?>            
            
            <?php echo new View("view/project/edit/{$this['step']}.html.php", $this->getArrayCopy() + array('level' => 3)) ?>

            <?php echo $steps ?>                                    

        </form>

    </div>            

    <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>