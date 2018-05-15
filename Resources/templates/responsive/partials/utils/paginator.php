<?php

// How many results are?
$total = (int) $this->total;
// Number of results per page
$limit = (int) $this->limit;
if (empty($limit)) $limit = 10;

// the page var to be added into the URL: ie: ?pag=2
$page_var =  (string) $this->page_var;
if (empty($page_var)) $page_var = 'pag';

// Which page are we now?
// Default search from current query
$pag = (int) $this->page;
if(empty($pag)) $pag = (int) $this->get_query($page_var);

// URL to be added the page_var variable
$baselink =  (string) $this->baselink;
if(empty($baselink)) {
    $baselink = $this->get_pathinfo() . '?' . $this->get_querystring();
}

// max number of pages without resuming in ... dots
$max_pages =  (string) $this->max_pages;
if (empty($max_pages)) $max_pages = 10;

// Texts for next/prev result
$t_prev =  (string) $this->text_prev;
if(empty($t_prev)) $t_prev = '&laquo;';
$t_next =  (string) $this->text_next;
if(empty($t_next)) $t_next = '&raquo;';

if (strpos($baselink,'?') === false) $baselink .= '?';
else {
    list($baselink, $query) = explode('?', $baselink);
    parse_str($query, $parts);

    $query_removal =  $this->query_removal;
    if (empty($query_removal) || !is_array($query_removal)) {
        $query_removal = ['pronto'];
    }
    $query_removal[] = $page_var;

    if($query_removal) {
        $parts = array_diff_key($parts, array_flip($query_removal));
    }

    $query = http_build_query($parts, '', '&amp;');
    $join = '?';
    if($query) {
        $join = '&amp;';
        $baselink = "$baselink?$query";
    }
}

$a_extra =  (string) $this->raw('a_extra');
if($a_extra) $a_extra = " $a_extra";

$total_pags = ceil($total / $limit);
$anterior = -1;
$seguent = 0;

$nums = array();
if ($total_pags > 1) {
    for ($i = 0; $i < $total_pags; $i++) {
        $nextpart = "$join$page_var=$i";

        if ($pag != $i) {
            if ($total_pags < $max_pages) {
                $nums[] = '<li><a href="' . $baselink . $nextpart . '"' . $a_extra . '>' . ($i + 1) . '</a></li>';
            }
            elseif (in_array($i, array(0, 1, 2, 3, $pag - 2, $pag - 1, $pag, $pag + 1, $pag + 2, $total_pags - 4, $total_pags - 3, $total_pags - 2, $total_pags - 1))) {
                $nums[] = '<li><a href="' . $baselink . ($i > 0 ? $nextpart : '') . '"' . $a_extra . '>' . ($i + 1) . '</a></li>';
            }
            else {
                if (in_array($i, array(4, $pag + 3))) $nums[] = '<li class="disabled"><a href="' . $baselink . ($i > 0 ? $nextpart : '') . '"' . $a_extra . '>' . '...</a></li>';
            }
        }
        else {
            $nums[] = '<li class="selected active"><a href="' . $baselink . $nextpart . '"' . $a_extra . '>' . ($i + 1) . '</a></li>';
            if ($pag > 0)             $anterior = $i - 1;
            if ($i + 1 < $total_pags) $seguent  = $i + 1;
        }
    }
    if ($anterior >= 0) array_unshift($nums, '<li><a title="' . $this->ee($this->text('regular-previous')) . '" href="' . $baselink . ($anterior > 0 ? "$join$page_var=$anterior" : '') . '"' . $a_extra . '>' . $t_prev . '</a></li>');
    if ($seguent > 0 )  $nums[] = '<li><a title="' . $this->ee($this->text('regular-next')) . '"  href="' . $baselink . "$join$page_var=$seguent" . '"' . $a_extra . '>' . $t_next . "</a></li>\n";
}

if ($nums) echo '<ul class="pagination">' . implode("\n", $nums) . '</ul>';

