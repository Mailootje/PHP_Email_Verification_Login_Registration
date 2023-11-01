<?php

function redirect($page, $status)
{
    $_SESSION['status'] = "$status";
    header("Location: $page");
    exit(0);
}

?>