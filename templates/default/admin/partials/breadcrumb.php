<div id="sub-header" style="margin-bottom: 10px;">
    <div class="breadcrumbs">
    <?php

    $parts = array();
    foreach($this->breadcrumb as $val) {
        if($val[1]) $parts[] = '<a href="' . $val[1] .  '">' . $val[0] . '</a>';
        else        $parts[] = '<strong>' . $val[0] . '</strong>';
    }
    echo implode(' &gt; ', $parts);

    ?>

    <div class="channel">

        <?php if($this->admin_nodes): ?>
            <?php if(count($this->admin_nodes) > 1): ?>
                Channel: <?= $this->html('select', ['options' => $this->admin_nodes, 'value' => $this->admin_node, 'name' => 'select-node', 'attribs' => ['id' => 'select-node']]) ?>
            <?php else: ?>
                Channel: <strong><?= $this->admin_nodes[$this->admin_node] ?></strong>
            <?php endif ?>
        <?php endif ?>

        <span class="label label-<?= $this->get_user()->getNodeRole($this->admin_node) ?>"><?= $this->get_user()->getNodeRole($this->admin_node) ?></span>
    </div>
    </div>
</div>
