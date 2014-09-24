
<ul id="pagination">
                
    <?php
            $i = isset($_GET['page']) ? $_GET['page'] : 1;
            error_log($i);
            if ($i != 1) {
                if($currentPage > 4) {
                    echo "<li><a href='?page=1$queryVars' title='".Text::get('regular-first')."'>".Text::get('regular-first')."</a></li><li class='hellip'>&hellip; </li>";
                }
                
                $previousPage = $i - 1;
                echo "<li><a href=\"?page=$previousPage$queryVars\"><  </a> </li>";
            }

            for($j = $i - 3; $j <= $i + 3; $j++) {
                //if i is less than one then continue to next iteration     
                if($j < 1) {
                    continue;
                }

                if($j > $this['pages'] || $this['pages'] == 1) {
                    break;
                }

                if($j == $i) {
                    echo "<li class='selected'>$j</li> ";
                } else {
                    echo "<li><a href=\"?page=$j$queryVars\">$j</a></li> ";
                }
            }//end for

            if($i < $this['pages']) {
                $nextPage = $i + 1;
                echo "<li><a href=\"?page=$nextPage$queryVars\"> ></a></li>";

                if($i < $this['pages'] -3 ) {
                    echo " <li class='hellip'>&hellip;</li><li><a href=\"?page=".$this['pages']."$queryVars\" title=\"".Text::get('regular-last')."\">".Text::get('regular-last')."(".$this['pages'].") </a></li>";
                }
            }
        
    ?>

</ul>