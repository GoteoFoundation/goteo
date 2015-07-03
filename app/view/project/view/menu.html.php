<?php use Goteo\Library\Text;

$items = array(
    (object) array(
        'id' => 'home',
        'show' => Text::get('project-menu-home'),
        'title' => Text::get('project-menu_title-home')
    ),
    (object) array(
        'id' => 'needs',
        'show' => Text::get('project-menu-needs'),
        'title' => Text::get('project-menu_title-needs')
    ),
    (object) array(
        'id' => 'supporters',
        'show' => Text::get('project-menu-supporters').' <span class="digits">'.$vars['supporters'].'</span>',
        'title' => Text::get('project-menu_title-supporters')
    ),
    (object) array(
        'id' => 'messages',
        'show' => Text::get('project-menu-messages').' <span class="digits">'.$vars['messages'].'</span>',
        'title' => Text::get('project-menu_title-messages')
    ),
    (object) array(
        'id' => 'updates',
        'show' => Text::get('project-menu-updates').' <span class="digits">'.$vars['updates'].'</span>',
        'title' => Text::get('project-menu_title-updates')
    )
);


?>
<div class="project-menu">
    <ul>
        <?php
        foreach ($items as $item): ?>
        <li class="<?php echo $item->id ?><?php if ($vars['show'] == $item->id) echo ' show' ?>">
        	<a href="/project/<?php echo htmlspecialchars($vars['project']->id) ?>/<?php echo $item->id ?>" class="tipsy" title="<?php echo $item->title ?>"><?php echo $item->show ?></a>
        </li>
        <?php endforeach ?>
    </ul>
</div>
