<?php 

use Goteo\Core\View;

$element = $this['element'];

?>
<?php if (isset($element->title)): ?>
<h<?php echo $element->level ?> class="title"><?php echo htmlspecialchars($element->title) ?></h<?php echo $element->level ?>>
<?php endif ?>

<?php if ('' !== ($innerHTML = $element->getInnerHTML())): ?>
<div class="contents">    
    <?php echo $innerHTML?>            
</div>
<?php endif ?>

<?php if (!empty($element->errors) || !empty($element->hint)): ?>
<div class="feedback" id="superform-feedback-for-<?php echo htmlspecialchars($element->id) ?>"
    <?php if (in_array($element->id, array('user_avatar', 'images')) && !empty($element->errors)) echo ' style="display:block;"'; ?>>

    <?php if (!empty($element->errors)): ?>
    <div class="error">        
        <?php foreach ($element->errors as $error): ?>
        <blockquote><?php echo $error ?></blockquote>
        <?php endforeach ?>
    </div>
    <?php endif ?>

    <?php if (isset($element->hint)): ?>
    <div class="hint">
        <blockquote><?php echo $element->hint ?></blockquote>
    </div>
    <?php endif ?>
    
</div>
<?php endif ?>

<?php if (!empty($element->children) && $element->type == 'group'): ?>
<div class="children">
    <?php echo new View('library/superform/view/elements.html.php', $element->children) ?>
</div>
<?php endif ?>
