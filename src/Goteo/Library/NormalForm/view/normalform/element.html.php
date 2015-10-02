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

<?php if (!empty($element->children) && $element->type == 'group'): ?>
<div class="children">
    <?php echo new View('superform/elements.html.php', $element->children) ?>
</div>
<?php endif ?>
