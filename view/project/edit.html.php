<?php 

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;
    
$bodyClass = 'project-edit';

$status = new View('view/project/edit/status.html.php', array('status' => $this['project']->status, 'progress' => $this['project']->progress));
$steps  = new View('view/project/edit/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step'], 'errors' => $this['project']->errors));

include 'view/prologue.html.php';
    
    include 'view/header.html.php'; ?>

    <div id="sub-header">
        <div>
            <h2><?php echo Text::get('project-form-header'); ?></h2>
        </div>
    </div>

    <div id="main" class="<?php echo htmlspecialchars($this['step']) ?>">
        
        <form method="post" action="" class="project" enctype="multipart/form-data">
                        
            <input type="hidden" name="view-step-<?php echo $this['step'] ?>" value="please" />
            
            <?php echo $status ?>
            <?php echo $steps ?>            
            
            <?php 
                        
            echo new View("view/project/edit/{$this['step']}.html.php", $this->getArrayCopy() + array('level' => 3)) ?>

            <?php echo $steps ?>                                    

        </form>

    </div>            

<?php \trace($project->errors); \trace($project->okeys); ?>

    <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>