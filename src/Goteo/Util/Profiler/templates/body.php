<div id="g_profiler">
    <ul>
        <li><a href="#phpinfo">PHP <?= phpversion() ?></a></li>
        <li class="<?= count($this->errors) ? 'ko' : 'ok' ?>"><a href="#errors">Errors: <?= count($this->errors) ?></a></li>
        <li class="<?= count($this->events) ? 'ok' : 'ko' ?>"><a href="#events">Events: <?= count($this->events) ?></a></li>
        <li class="<?= $this->queries['total'] > 100 ? ($this->queries['total'] > 300 ? 'ko' : 'warn') : 'ok' ?>"><a href="#queries_server">SQL queries: <?= $this->queries['total'] ?></a></li>
        <li class="<?= $this->queries['total_cached'] < 10 ? 'warn' : 'ok' ?>"><a href="#queries_cached">Cached: <?= $this->queries['total_cached'] ?></a></li>
        <li class="<?= $this->queries['total_non_cached'] > 100 ? 'warn' : 'ok' ?>"><a href="#queries_non_cached">Non cached: <?= $this->queries['total_non_cached'] ?></a></li>
        <li class="<?= $this->queries['time'] > 1 ? ($this->queries['time'] > 3 ? 'ko' : 'warn') : 'ok' ?>"><a href="#queries_long">Query time: <?= round( 1000 * $this->queries['time']) ?> ms</a></li>
        <li><a href="#session">Render time: <?= round( 1000 * $this->session['duration']) ?> ms</a></li>

        <li class="right top"><a href="#hide_bar">&times;</a></li>
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
    <div class="session">
        <p>Session start time: <strong><?= date("r", $this->session['start_time']) ?></strong></p>
        <p>Session expires in: <strong><?= date("r", $this->session['expires_in']) ?></strong></p>
        <p>Session expiration time: <strong><?= number_format($this->session['expire_time']/60, 2, '.', ' ') ?> minutes</strong></p>
        <h2>Requests headers:</h2>
        <span class="pre"><?= $this->headers['request'] ?></span>
        <h2>Response headers:</h2>
        <span class="pre"><?= $this->headers['response'] ?></span>

        <h2>Session vars:</h2>
        <ul>
        <?php foreach($this->session['keys'] as $var => $value): ?>
            <li><strong><?= $var ?></strong>: <span class="pre"><?php var_dump($value) ?></span></li>
        <?php endforeach ?>
        </ul>
        <h2>Cookies vars:</h2>
        <ul>
        <?php foreach($this->cookies['keys'] as $var => $value): ?>
            <li><strong><?= $var ?></strong>: <span class="pre"><?php var_dump($value) ?></span></li>
        <?php endforeach ?>
        </ul>
    </div>
    <div class="events">
        <ul>
        <?php foreach($this->events as $event): ?>
            <li>
            <h2><?= $event['class'] ?></h2>
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
        <h2>Master queries</h2>
        <ul>
        <?php foreach($this->queries['sql_master_cached'] as $sql): ?>
            <li>
            Num: <strong><?= $sql[0] ?></strong><br>
            <span class="-pre"><?= $sql[1] ?></span><br>
            <strong><?= print_r($sql[2], true) ?></strong>
            </li>
        <?php endforeach ?>
        </ul>
        <h2>Replica queries</h2>
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
        <h2>Master queries</h2>
        <ul>
        <?php foreach($this->queries['sql_master_non_cached'] as $sql): ?>
            <li>
            Num: <strong><?= $sql[0] ?></strong> Time: <strong><?= round( 1000 * $sql[3]) ?> ms</strong><br>
            <span class="-pre"><?= $sql[1] ?></span><br>
            <strong><?= print_r($sql[2], true) ?></strong>
            </li>
        <?php endforeach ?>
        </ul>
        <h2>Replica queries</h2>
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
        <h2>Long queries</h2>
        <?php foreach($this->queries['sql_long_queries'] as $sql): ?>
            <li>
            Num: <strong><?= $sql[0] ?></strong> Time: <strong><?= round( 1000 * $sql[3]) ?> ms</strong><br>
            <span class="pre"><?= $sql[1] ?></span><br>
            <strong><?= print_r($sql[2], true) ?></strong>
            </li>
        <?php endforeach ?>
    </div>
</div>

<script>
$(function() {
    $('#g_profiler a').click(function(e){
        e.preventDefault();
        $('#g_profiler a').removeClass('active');

        var href = $(this).attr('href').substr(1);

        if(href === 'hide_bar') {
            $('#g_profiler_info').hide();
            if($('#g_profiler').width()>20) {
                $('#g_profiler').animate({width: '20px'}, 500);
            }
            else {
                $('#g_profiler').animate({width: '100%'}, 500);
            }

            return;
        }

        if($('#g_profiler_info').is(':visible')) {
            if($('#g_profiler_info>div.' + href).is(':visible')) {
                $('#g_profiler_info').hide();
                return;
            }
        }
        $('#g_profiler_info>div').hide();
        $(this).addClass('active');
        $('#g_profiler_info').show();
        $('#g_profiler_info>div.' + href).show();
    });
});
</script>
