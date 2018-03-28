<?php
$target = $this->target ?: 'raised';

$scripts = [];
    foreach($this->intervals as $interval => $label): ?>
    <script type="text/template" id="template-matchfunding-global-<?= $interval ?>"><?=
        $this->insertIf("admin/stats/totals/partials/invests/" . $target, [
            'id' => 'global',
            'method' => 'global',
            'interval' => $interval
        ]) ?: '<p class="text-danger">Error loading [' . $target . '] view</p>'
        ?>
    </script>
<?php endforeach;



