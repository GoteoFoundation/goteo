<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\ACL;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Panel principal de administración</h2>
                <?php if (defined('ADMIN_BCPATH')) : ?>
                <blockquote><?php echo ADMIN_BCPATH; ?></blockquote>
                <?php endif; ?>
            </div>
        </div>

        <div id="main">

            <div class="widget feed">
                <a name="feed"></a>
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $('.scroll-pane').jScrollPane({showArrows: true});
                    });
                    </script>
                    <h3 class="title">actividad reciente</h3>
                    <a href="/admin/feed/<?php echo isset($_GET['feed']) ? '?feed='.$_GET['feed'] : ''; ?>">Ver feeds</a>

                    <p class="categories">
                        <a href="/admin/?feed=all#feed" class="light-blue">TODO</a> <a href="/admin/?feed=admin#feed">ADMINISTRADOR</a> <a href="/admin/?feed=user#feed" class="violet">USUARIO</a>                        <a href="/admin/?feed=all#feed">TODO</a> <a href="/admin/?feed=admin#feed">ADMINISTRADOR</a> <a href="/admin/?feed=user#feed">USUARIO</a>

                    </p>
                    
                    <div class="scroll-pane">                    	
                        <div class="subitem odd">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="blue">dummy text </a>of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.  Lorem Ipsum is simply <a href="#" class="blue">dummy text </a>of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>
                        <div class="subitem">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="light-blue">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.  Lorem Ipsum is simply <a href="#" class="blue">dummy text </a>of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>
                         <div class="subitem odd">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>
                         <div class="subitem">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="green">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>
                        <div class="subitem odd">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="grey">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>
                        <div class="subitem">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="blue">dummy text </a>of the printing and typesetting industry. 
                           </div>                           
                        </div>
                        <div class="subitem odd">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="light-blue">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>
                         <div class="subitem">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>
                         <div class="subitem odd">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="green">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry.   Lorem Ipsum is simply <a href="#" class="violet">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>
                        <div class="subitem">                           
                           <span class="datepub">Hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply <a href="#" class="grey">dummy text</a> of the printing and typesetting industry. 
                           </div>                           
                        </div>                                                
                    </div>

            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
