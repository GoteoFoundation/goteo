<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
$duration = microtime(true) - $this->starttime;
 ?><div id="g_profiler">
    <ul>
        <li><a href="#phpinfo">PHP <?= phpversion() ?></a></li>
        <li class="<?= count($this->errors) ? 'ko' : (count($this->texts) ? 'warn' : 'ok') ?>"><a href="#errors">Errors: <?= (count($this->errors) + count($this->texts)) ?></a></li>
        <li class="<?= count($this->events) ? 'ok' : 'ko' ?>"><a href="#events">Events: <?= count($this->events) ?></a></li>
        <li class="<?= $this->queries['total'] > 100 ? ($this->queries['total'] > 300 ? 'ko' : 'warn') : 'ok' ?>"><a href="#queries_server">SQL queries: <?= $this->queries['total'] ?></a></li>
        <li class="<?= $this->queries['total_cached'] < 10 ? 'warn' : 'ok' ?>"><a href="#queries_cached">Cached: <?= $this->queries['total_cached'] ?></a></li>
        <li class="<?= $this->queries['total_non_cached'] > 100 ? 'warn' : 'ok' ?>"><a href="#queries_non_cached">Non cached: <?= $this->queries['total_non_cached'] ?></a></li>
        <li class="<?= $this->queries['time'] > 1 ? ($this->queries['time'] > 3 ? 'ko' : 'warn') : 'ok' ?>"><a href="#queries_long">Query time: <?= round( 1000 * $this->queries['time']) ?> ms</a></li>
        <li class="<?= ($this->headers['response_code'] === 200 && $duration < 2) ? ($duration < 1 ? 'ok' : 'warn') : ($this->headers['response_code'] >= 500 ? 'ko' : 'warn') ?>"><a href="#session">HTTP <?= $this->headers['response_code'] ?> &nbsp; Render: <?= round( 1000 * $duration) ?> ms</a></li>

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
        <h2>PHP Errors:</h2>
        <?php if($this->errors): ?>
            <ul>
            <?php foreach($this->raw('errors') as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach ?>
            </ul>
        <?php else: ?>
            Great, None found!
        <?php endif; ?>

        <h2>Text copies errors:</h2>
        <?php if($this->texts): ?>
            <ul>
            <?php foreach($this->texts as $id => $err): ?>
                <li><strong><?= $id ?></strong>: <?= $err ?></li>
            <?php endforeach ?>
            </ul>
        <?php else: ?>
            Great, None found!
        <?php endif; ?>
    </div>
    <div class="session">
        <p>PHP max memory: <strong><?= number_format(memory_get_peak_usage(true)/1024/1024, 2, '.', ' ') ?> MB</strong></p>
        <p>Session start time: <strong><?= date("r", $this->session['start_time']) ?></strong></p>
        <p>Session expires in: <strong><?= date("r", $this->session['expires_in']) ?></strong></p>
        <p>Session expiration time: <strong><?= number_format($this->session['expire_time']/60, 2, '.', ' ') ?> minutes</strong></p>

        <h2>Redirections:</h2>
        <span class="pre">
        <?php foreach($this->a('redirections') as $redirection): ?>
            <?= "$redirection\n" ?>
        <?php endforeach ?>
        </span>

        <h2>Views theme "<?= $this->view_theme ?>":</h2>
        <span class="pre"><?php foreach($this->view_paths as $name => $folder) {
            echo "[$name] => $folder\n";
            } ?></span>

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
    <?php

function print_sql($sqls) {
    $ret = '';
    $processed = [];
    foreach($sqls as $sql) {
        $query = trim($sql[1]);
        if($processed[$query]) {
            $processed[$query][1][] = $sql[2];
            $processed[$query][2] += $sql[3];
            $processed[$query][3]++;
            continue;
        }
        $processed[$query] = [$sql[0], [$sql[2]], $sql[3], 1];
    }
    uasort($processed, function($a, $b) {
        if( $a[2] === $b[2]) return 0;
        return ($a[2] > $b[2]) ? -1 : 1;
    });
    foreach($processed as $query => $sql) {
        // $clas = 'pre';
        $c1 = $c2 = '';
        if(strpos($query, 'SELECT') === 0) $c1 = 'text-success';
        elseif(strpos($query, 'UPDATE') === 0) $c1 = 'text-danger';
        elseif(strpos($query, 'DELETE') === 0) $c1 = 'text-warning';
        else $c1 .= 'text-info';
        if($sql[3] > 1) $c2 = 'text-warning';
        if($sql[3] > 10) $c2 = 'text-danger';
        $ret .= "<li>
        Num: <strong>{$sql[0]}</strong> Time: <strong>" . round( 1000 * $sql[2]) . " ms</strong> Repetitions: <strong class=\"$c2\">{$sql[3]}</strong><br>
        <span class=\"code $c1\">{$query}</span><br>
        <div class=\"pre\">";

        $values = [];
        foreach($sql[1] as $i => $s) {
            $vals = [];
            foreach($s as $k => $v) {
                $vals[] = "$k => <strong>$v</strong>";
            }
            $val = '[' . implode(', ', $vals) . ']';

            if(isset($values[$val])) {
                $values[$val]++;
                continue;
            }
            $values[$val] = 1;
        }
        foreach($values as $val => $n) {
            $ret .= "<strong>$n</strong> times:\t$val\n";
        }
        $ret .= "</div>
        </li>";
    }
    return $ret;
}
    ?>
    <div class="queries_cached">
        <h2>Master queries</h2>
        <ul>
        <?= print_sql($this->queries['sql_master_cached']) ?>
        </ul>
        <h2>Replica queries</h2>
        <ul>
        <?= print_sql($this->queries['sql_replica_cached']) ?>
        </ul>
    </div>
    <div class="queries_non_cached">
        <h2>Master queries</h2>
        <ul>
        <?= print_sql($this->queries['sql_master_non_cached']) ?>
        </ul>
        <h2>Replica queries</h2>
        <ul>
        <?= print_sql($this->queries['sql_replica_non_cached']) ?>
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

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
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
// @license-end
</script>
