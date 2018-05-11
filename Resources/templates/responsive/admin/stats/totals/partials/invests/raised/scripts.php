<?php
$target = $this->target ?: 'raised';
$scripts = [];
foreach($this->methods as $id => $method):
    foreach($this->intervals as $interval => $label): ?>
    <script type="text/template" id="template-<?= $target ?>-<?= "$id-$interval" ?>"><?=
        $this->insertIf("admin/stats/totals/partials/invests/" . $target, [
            'id' => $id,
            'method' => $method,
            'interval' => $interval,
            'target' => $target
        ]) ?: '<p class="text-danger">Error loading [' . $target . '] view</p>'
        ?>
    </script>
<?php endforeach;
endforeach;


