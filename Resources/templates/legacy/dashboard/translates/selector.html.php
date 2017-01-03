<?php
use Goteo\Library\Text;

//piñonaco que te crio
$vars['option'] = str_replace(array('call_overview', 'node_overview'), array('overview', 'overview'), $vars['option']);
?>
<script type="text/javascript">
function item_select(type) {
    document.getElementById('selector-type').value = type;
    document.getElementById('selector-form').submit();
}
</script>
<div id="project-selector">
    <form id="selector-form" name="selector_form" action="<?php echo '/dashboard/translates/overview/select'; ?>" method="post">
        <input type="hidden" id="selector-type" name="type" value="profile" />

    <?php if (!empty($vars['projects'])) : ?>
        <label for="pselector"><?php echo Text::get('project-menu-home') ?></label>
        <select id="pselector" name="project" onchange="item_select('project');">
            <option value=""><?php echo Text::get('dashboard-translate-select_project') ?></option>
        <?php foreach ($vars['projects'] as $project) : ?>
            <option value="<?php echo $project->id; ?>"<?php if ($project->id == $_SESSION['translate_project']->id) echo ' selected="selected"'; ?>><?php echo $project->name; ?></option>
        <?php endforeach; ?>
        </select><br />
    <?php endif; ?>

    <?php if (!empty($vars['calls'])) : ?>
        <label for="cselector"><?php echo Text::get('call-menu-home') ?></label>
        <select id="cselector" name="call" onchange="item_select('call');">
            <option value=""><?php echo Text::get('dashboard-translate-select_call') ?></option>
        <?php foreach ($vars['calls'] as $call) : ?>
            <option value="<?php echo $call->id; ?>"<?php if ($call->id == $_SESSION['translate_call']->id) echo ' selected="selected"'; ?>><?php echo $call->name; ?></option>
        <?php endforeach; ?>
        </select><br />
    <?php endif; ?>

    <?php if (!empty($vars['nodes'])) : ?>
        <label for="nselector"><?php echo Text::get('node-menu-home') ?></label>
        <select id="nselector" name="node" onchange="item_select('node');">
            <option value=""><?php echo Text::get('dashboard-translate-select_node') ?></option>
        <?php foreach ($vars['nodes'] as $node) : ?>
            <option value="<?php echo $node->id; ?>"<?php if ($node->id == $_SESSION['translate_node']->id) echo ' selected="selected"'; ?>><?php echo $node->name; ?></option>
        <?php endforeach; ?>
        </select><br />
    <?php endif; ?>

    </form>

    <form id="lang-form" name="lang_form" action="<?php echo '/dashboard/'.$vars['section'].'/'.$vars['option'].'/lang'; ?>" method="post">
        <label for="selang"><?php echo Text::get('regular-lang') ?></label>
        <select id="selang" name="lang" onchange="document.getElementById('lang-form').submit();" style="width:150px;">
        <?php foreach ($vars['langs'] as $lng) : ?>
            <option value="<?php echo $lng->id; ?>"<?php if ($lng->id == $_SESSION['translate_lang']) echo ' selected="selected"'; ?>><?php echo $lng->name; ?></option>
        <?php endforeach; ?>
        </select>
        <?php if(in_array($_SESSION['translate_lang'], $vars['langs_available'])): ?>
        <a href="?remove_translation=<?= $_SESSION['translate_lang'] ?>" onclick="return confirm('<?= str_replace("'","\'",Text::get('project-remove-translation-confirm')) ?>')"><?= Text::get('project-remove-translation') ?></a>
        <?php endif ?>
    </form>

    <?php if ($_SESSION['translate_type'] == 'project' && !empty($_SESSION['translate_project'])) : ?>
    <p><?php echo Text::html('dashboard-translate-doing_project', $vars['project']->name, $vars['original']->lang_name) ?></p>
    <?php endif; ?>

    <?php if ($_SESSION['translate_type'] == 'call' && !empty($_SESSION['translate_call'])) : ?>
    <p><?php echo Text::html('dashboard-translate-doing_call', $vars['call']->name, $vars['original']->lang_name) ?></p>
    <?php endif; ?>

    <?php if ($_SESSION['translate_type'] == 'node' && !empty($_SESSION['translate_node'])) : ?>
    <p><?php echo Text::html('dashboard-translate-doing_node', $vars['node']->name) ?></p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['translate_type']) && $_SESSION['translate_type'] != 'profile') : ?>
        <a href="#" name="profile" class="button aqua" onclick="item_select('profile');"><?php echo Text::get('dashboard-translate-select_profile') ?></a>
    <?php else : ?>
        <p><?php echo Text::get('dashboard-translate-doing_profile') ?></p>
    <?php endif; ?>

</div>
