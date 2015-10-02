<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

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
<div class="feedback" id="superform-feedback-for-<?php echo htmlspecialchars($element->id) ?>"<?php if (in_array($element->id, array('user_avatar', 'images')) && !empty($element->errors)) echo ' style="display:block;"'; ?>>

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

<?php if (!empty($element->children) && $element->getType() == 'Group'): ?>
<div class="children">
    <?php echo View::get('superform/elements.html.php', $element->children) ?>
</div>
<?php endif ?>
