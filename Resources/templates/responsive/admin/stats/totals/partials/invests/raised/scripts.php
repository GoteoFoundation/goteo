<?php

$scripts = [];
foreach($this->methods as $id => $method):
    foreach($this->intervals as $interval => $label): ?>
    <script type="text/template" id="template-<?= "$id-$interval" ?>"><?=
        $this->insertIf("admin/stats/totals/partials/invests/" . $this->part, [
            'id' => $id,
            'method' => $method,
            'interval' => $interval
        ]) ?: '<p class="text-danger">Error loading [' . $part . '] view</p>'
        ?>
    </script>
<?php endforeach;
endforeach;


