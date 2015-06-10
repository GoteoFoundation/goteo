<div id="g_profiler">
    <ul>
        <li><a href="phpinfo">PHP <?= phpversion() ?></a></li>
        <li class="<?= count($this->errors) ? 'ko' : 'ok' ?>"><a href="errors">Errors: <?= count($this->errors) ?></a></li>
        <li class="<?= count($this->events) ? 'ok' : 'ko' ?>"><a href="events">Events: <?= count($this->events) ?></a></li>
        <li class="<?= $this->queries['total'] > 100 ? 'ko' : 'ok' ?>"><a href="queries_server">SQL queries: <?= $this->queries['total'] ?></a></li>
        <li class="<?= $this->queries['total_cached'] < 10 ? 'ko' : 'ok' ?>"><a href="queries_cached">Cached: <?= $this->queries['total_cached'] ?></a></li>
        <li class="<?= $this->queries['total_non_cached'] > 100 ? 'ko' : 'ok' ?>"><a href="queries_non_cached">Non cached: <?= $this->queries['total_non_cached'] ?></a></li>
        <li class="<?= $this->queries['time'] > 1 ? 'ko' : 'ok' ?>"><a href="queries_long">Query time: <?= round( 1000 * $this->queries['time']) ?> ms</a></li>
    </ul>

</div>

<div id="g_profiler_info">

    <div class="phpinfo">
    <?php
    ob_start();
    phpinfo();
    $pinfo = ob_get_contents();
    ob_end_clean();

    echo preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);

    ?>
    </div>

    <div class="errors">
        <ul>
        <?php foreach($this->raw('errors') as $err): ?>
            <li><?= $err ?></li>
        <?php endforeach ?>
        </ul>
    </div>
    <div class="events">
        <ul>
        <?php foreach($this->events as $event): ?>
            <li>
            <strong><?= $event['class'] ?></strong><br>
            Time: <strong><?= $event['time'] ?> ms</strong><br>
            Memory: <strong><?= number_format($event['memory']/1024, 2, '.', ' ') ?> KB</strong><br>
            Controllers: <br>
            <?php foreach($event['controllers'] as $controller): ?>
                <span class="pre"><?php var_dump($controller) ?></span>
            <?php endforeach ?>
            Requests: <br>
            <?php foreach($event['requests'] as $request): ?>
                <span class="pre"><?php var_dump($request) ?></span>
            <?php endforeach ?>
            Responses: <br>
            <?php foreach($event['responses'] as $response): ?>
                <span class="pre"><?php var_dump($response) ?></span>
            <?php endforeach ?>
            </li>
        <?php endforeach ?>
        </ul>
    </div>
    <div class="queries_server">
        <p>Total master queries:  <strong><?= $this->queries['total_master'] ?></strong> Time: <strong><?= round( 1000 * $this->queries['time_master']) ?> ms</strong></p>
        <p>Total replica queries: <strong><?= $this->queries['total_replica'] ?></strong> Time: <strong><?= round( 1000 * $this->queries['time_replica']) ?> ms</strong></p>
    </div>
    <div class="queries_cached">
        <p><strong>Master queries</strong></p>
        <ul>
        <?php foreach($this->queries['sql_master_cached'] as $sql): ?>
            <li>
            Num: <strong><?= $sql[0] ?></strong><br>
            <span class="-pre"><?= $sql[1] ?></span><br>
            <strong><?= print_r($sql[2], true) ?></strong>
            </li>
        <?php endforeach ?>
        </ul>
        <p><strong>Replica queries</strong></p>
        <ul>
        <?php foreach($this->queries['sql_replica_cached'] as $sql): ?>
            <li>
            Num: <strong><?= $sql[0] ?></strong><br>
            <span class="-pre"><?= $sql[1] ?></span><br>
            <strong><?= print_r($sql[2], true) ?></strong>
            </li>
        <?php endforeach ?>
        </ul>
    </div>
    <div class="queries_non_cached">
        <p><strong>Master queries</strong></p>
        <ul>
        <?php foreach($this->queries['sql_master_non_cached'] as $sql): ?>
            <li>
            Num: <strong><?= $sql[0] ?></strong> Time: <strong><?= round( 1000 * $sql[3]) ?> ms</strong><br>
            <span class="-pre"><?= $sql[1] ?></span><br>
            <strong><?= print_r($sql[2], true) ?></strong>
            </li>
        <?php endforeach ?>
        </ul>
        <p><strong>Replica queries</strong></p>
        <ul>
        <?php foreach($this->queries['sql_replica_non_cached'] as $sql): ?>
            <li>
            Num: <strong><?= $sql[0] ?></strong> Time: <strong><?= round( 1000 * $sql[3]) ?> ms</strong><br>
            <span class="-pre"><?= $sql[1] ?></span><br>
            <strong><?= print_r($sql[2], true) ?></strong>
            </li>
        <?php endforeach ?>
        </ul>
    </div>
    <div class="queries_long">
        <p><strong>Long queries</strong></p>
        <?php foreach($this->queries['sql_long_queries'] as $sql): ?>
            <li>
            Num: <strong><?= $sql[0] ?></strong> Time: <strong><?= round( 1000 * $sql[3]) ?> ms</strong><br>
            <span class="pre"><?= $sql[1] ?></span><br>
            <strong><?= print_r($sql[2], true) ?></strong>
            </li>
        <?php endforeach ?>
    </div>
</div>
