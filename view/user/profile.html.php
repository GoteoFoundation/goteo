<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();

$interests = Interest::getAll();

?>

        <div id="sub-header">
            <div>
                <h2><img src="/image/<?php echo $user->avatar->id; ?>/75/75" /> Perfil de <br /><em><?php echo $user->name; ?></em></h2>
            </div>
        </div>

        <div id="main">
            
            <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'main', 'level' => $user->worth)); ?>
                                    
            <div class="about">
                <h4>Sobre mi</h4>
                <p><?php echo $user->about ?></p>
                <hr />
                <h4>Mis intereses</h4>
                <p><?php
                $c = 0;
                foreach ($user->interests as $interest) {
                    if ($c > 0) echo ', ';
                    echo $interests[$interest];
                    $c++;
                } ?></p>
                <hr />
                <h4>Mis etiquetas</h4>
                <p><?php echo $user->keywords; ?></p>
                <hr />
                <dl>

                    <?php if (!empty($user->webs)): ?>
                    <dt class="links">Webs</dt>
                    <dd class="links">
                        <ul>
                            <?php foreach ($user->webs as $link): ?>
                            <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
                            <?php endforeach ?>
                        </ul>
                    </dd>
                    <?php endif ?>

                    <?php if (isset($user->location)): ?>
                    <dt class="location">Ubicación</dt>
                    <dd class="location"><a href="">Barcelona, ES</a></dd>
                    <?php endif ?>

                </dl>

            </div>


            
            <?php if (isset($user->facebook) || isset($user->linkedin) || isset($user->twitter)): ?>            
            <div class="social">
                <ul>
                    <?php if (isset($user->facebook)): ?>
                    <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>">Facebook</a></li>
                    <?php endif ?>
                    <?php if (isset($user->twitter)): ?>
                    <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>">Twitter</a></li>
                    <?php endif ?>
                    <?php if (isset($user->linkedin)): ?>
                    <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>">LinkedIn</a></li>
                    <?php endif ?>
                </ul>                
            </div>            
            <?php endif ?>

            <div class="widget projects">
                <h2 class="title">Proyectos que apoyo</h2>
                <?php foreach ($this['invested'] as $project) : ?>
                    <div>
                        <?php
                        // es instancia del proyecto
                        echo new View('view/project/widget/project.html.php', array(
                            'project'   => $project
                        )); ?>
                    </div>
                <?php endforeach; ?>
    		</div>

            <div class="widget projects">
                <h2 class="title">Mis proyectos</h2>
                <?php foreach ($this['projects'] as $project) : ?>
                    <div>
                        <?php
                        // es instancia del proyecto
                        echo new View('view/project/widget/project.html.php', array(
                            'project'   => $project
                        )); ?>
                    </div>
                <?php endforeach; ?>
    		</div>

            <hr />
            <!-- lateral -->

            <div>
                <h3>Mis cofinanciadores</h3>
                <div>
                <?php foreach ($this['investors'] as $user=>$investor) {
                    echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy));
    //                echo "{$investor->avatar} {$investor->name} De nivel {$investor->worth}  Cofinancia {$investor->projects} proyectos  Me aporta: {$investor->amount} € <br />";
                } ?>
                </div>
                <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'side')); ?>
            </div>

            <div>
                <h3>Compartiendo intereses</h3>
                <?php foreach ($this['shares'] as $share) {
                    echo '<div style="float:left;margin: 10px;"><img src="/image/' . $share->avatar->id . '/50/50" /><br />';
                    echo '<a href="/user/' . $share->user . '">' . $share->name . '</a><br />';
                    echo "Proyectos(" . $share->projects .")<br/>Aportacion(" . $share->invests ." )";
                    echo '</div>';
                } ?>
                <br clear="all" />
            </div>


        </div>
        
    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>