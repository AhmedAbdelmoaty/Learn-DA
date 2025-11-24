<?php
$lang = (isset($_GET['lang']) && $_GET['lang'] === 'ar') ? 'ar' : 'en';
$target = 'tool.php?slug=excel';
if ($lang === 'ar') {
    $target .= '&lang=ar';
}
header('Location: ' . $target);
exit;
