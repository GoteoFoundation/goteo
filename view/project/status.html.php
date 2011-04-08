            <?php 
            use Goteo\Library\Text,
                Goteo\Model\Project; 
            
            $project->progress = round(30 + rand(0, 70), 2); // @todo Remove me
            ?>            
            <div class="status">
                
                <div id="project-status">
                    <h3>Estado del proyecto:</h3>
                    <ul>
                        <?php foreach (Project::status() as $i => $s): ?>
                        <li><?php if ($i == $project->status) echo '<strong>' ?><?php echo htmlspecialchars($s) ?><?php if ($i == $project->status) echo '</strong>' ?></li>
                        <?php endforeach ?>
                    </ul>                
                </div>
            
                <div id="information-status">
                    <h3>Estado global de la información: <code><?php echo $project->progress ?>%</code></h3>
                    <p><?php echo Text::get('explain project progress') ?></p>
                    
                    <script type="text/javascript">
                    // Animated information-meter 
                    $(document).ready(function () {
                        
                        var progress = <?php echo $project->progress ?>,
                            code = $('#information-status code'),
                            meter = $('<div class="meter">'),
                            done = $('<div>').addClass('done').css('width', '0');
                            left = $('<div>').addClass('left').css('width', '100%');
                            
                            progress = 30 + Math.round(Math.random() * 70, 2);
                                                
                        $('#information-status').append(meter);
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
                    <!-- Static information meter -->
                    <div class="meter">
                        <div class="done" style="width: <?php echo round($project->progress, 2) ?>%"></div>
                        <div class="left" style="width: <?php echo round(100 - $project->progress, 2) ?>%"></div>                        
                    </div>
                    </noscript>
                    
                </div>
                                        
                
            </div>
