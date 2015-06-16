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

    <?php if($this->admin_nodes): ?>
        <div style="float:right">
            Channel: <?= $this->html('select', ['options' => $this->admin_nodes, 'value' => $this->admin_node, 'name' => 'select-node', 'attribs' => ['id' => 'select-node']]) ?>
        </div>
    <?php endif ?>

    </div>
</div>
