<?php

// How many results are?
$total = (int) $this->total;
// Number of results per page
$limit = (int) $this->limit;
if (empty($limit)) $limit = 10;
$hash = $this->hash ? '#'.$this->hash : '';

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
    $baselink = $this->get_pathinfo();

    if($gets = parse_str($this->get_querystring())) {
        $query_removal =  $this->query_removal;
        if (empty($query_removal) || !is_array($query_removal)) {
            $query_removal = ['pronto'];
        }

        if($query_removal) {
            $gets = array_diff_key($gets, array_flip($query_removal));
        }

         $baselink .= '?'. http_build_query($gets, '', '&amp;');
    }
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
    unset($parts[$page_var]);
    $query = http_build_query($parts);
    $join = '?';
    if($query) {
        $join = '&';
        $baselink = "$baselink?$query";
    }
}

$total_pags = ceil($total / $limit);
$anterior = -1;
$seguent = 0;

$nums = array();
if ($total_pags > 1) {
    for ($i = 0; $i < $total_pags; $i++) {
        $nextpart = "$join$page_var=$i";

        if ($pag != $i) {
            if ($total_pags < $max_pages) {
                $nums[] = '<li><a href="' . $baselink . $nextpart . $hash .'">' . ($i + 1) . '</a></li>';
            }
            elseif (in_array($i, array(0, 1, 2, 3, $pag - 2, $pag - 1, $pag, $pag + 1, $pag + 2, $total_pags - 4, $total_pags - 3, $total_pags - 2, $total_pags - 1))) {
                $nums[] = '<li><a href="' . $baselink . ($i > 0 ? $nextpart : '') . $hash . '">' . ($i + 1). '</a></li>';
            }
            else {
                if (in_array($i, array(4, $pag + 3))) $nums[] = '<li class="disabled"><a href="' . $baselink . ($i > 0 ? $nextpart : '') . $hash . '">' . '...</a></li>';
            }
        }
        else {
            $nums[] = '<li class="selected"><a href="' . $baselink . $nextpart . $hash . '">' . ($i + 1) . '</a></li>';
            if ($pag > 0)             $anterior = $i - 1;
            if ($i + 1 < $total_pags) $seguent  = $i + 1;
        }
    }
    if ($anterior >= 0) array_unshift($nums, '<li><a title="Previous" href="' . $baselink . ($anterior > 0 ? "$join$page_var=$anterior" : '') . $hash . '">' . $t_prev . '</a></li>');
    if ($seguent > 0 )  $nums[] = '<li><a title="Next"  href="' . $baselink . "$join$page_var=$seguent" . $hash . '">' . $t_next . '</a></li>';
}

if ($nums) echo '<ul class="pagination">' . implode("\n", $nums) . '</ul>';
