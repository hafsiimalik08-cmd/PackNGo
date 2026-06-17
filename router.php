<?php
$request = $_SERVER['REQUEST_URI'];
$request = ltrim($request, '/');

if ($request == '') {
    include('index.php'); 
} else if (file_exists($request . '.html')) {
    include($request . '.html');
} else if (file_exists($request . '.php')) {
    include($request . '.php');
} else if (file_exists($request)) {
    include($request);
} else {
    include('index.php');
}
?>
