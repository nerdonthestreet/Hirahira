<?php

require_once "./config.php";

function formatRuntime($total_in_seconds) {
    $s=$total_in_seconds % 60;
    $m=(($total_in_seconds-$s) / 60) % 60;
    $h=floor($total_in_seconds / 3600);
    $d=floor($h / 24);
    $h=$h - ($d * 24);
    return $d . " days, " . $h . " hours, " . $m . " minutes, and " . $s . " seconds";
}

?>
