            <?php
            use Goteo\Library\Text,
                Goteo\Model\Project;
            ?>
            <div class="status">

                <div id="project-status">
                    <h3><?php echo Text::get('form-project-status-title'); ?></h3>
                    <ul>
                        <?php foreach (Project::status() as $i => $s): ?>
                        <li><?php if ($i == $vars['status']) echo '<strong>' ?><?php echo htmlspecialchars($s) ?><?php if ($i == $vars['status']) echo '</strong>' ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>

                <div id="project-score">
                    <h3><?php echo Text::get('form-project-progress-title'); ?> <code><?php echo number_format($vars['progress'], 0) ?>%</code></h3>
                    <p><?php echo Text::get('explain-project-progress') ?></p>

                    <script type="text/javascript">
                    // Animated scoremeter
                    $(document).ready(function () {

                        var progress = <?php echo number_format($vars['progress'], 2) ?>,
                            code = $('#project-score code'),
                            meter = $('<div class="meter">'),
                            done = $('<div>').addClass('done').css('width', '0');
                            left = $('<div>').addClass('left').css('width', '100%');

                        $('#project-score').append(meter);
                        meter.append(done, left);

                        $(window).load(function() {
                            done.animate({
                                width: progress + '%'
                            }, {
                                step: function (p) {
                                    left.css('width', (100 - p)  + '%');
                                    code.text(Math.round(p) + '%');
                                }
                            }, 2500);
                        });

                    });
                    </script>

                    <noscript>
                    <!-- Static scoremeter -->
                    <div class="meter">
                        <div class="done" style="width: <?php echo number_format($vars['progress'], 2) ?>%"></div>
                        <div class="left" style="width: <?php echo number_format(100 - $vars['progress'], 2) ?>%"></div>
                    </div>
                    </noscript>

                </div>


            </div>
