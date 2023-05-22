<?php

$url = substr($_SERVER["REQUEST_URI"], 1);
$http_origin = array_key_exists("HTTP_ORIGIN", $_SERVER) ? $_SERVER["HTTP_ORIGIN"] : null;
$origin = str_replace("/", "", $http_origin);
if(str_starts_with("http:", $origin))
    $origin = substr($origin, 5);
else if(str_starts_with("https:", $origin))
    $origin = substr($origin, 6);
$allowed_origins = [];
foreach(getenv() as $env => $val) {
    if(str_starts_with($env, "ALLOWED_ORIGIN_")) {
        $origin = str_replace("_", ".", strtolower(substr($env, 15)));
        $allowed_origins[$origin] = explode(";", str_replace(" ", "", $val));
    }
}

if($http_origin != null) {
    header("Access-Control-Allow-Origin: $http_origin");
    header("Access-Control-Request-Method: GET");
    if(array_key_exists($origin, $allowed_origins)) {
        foreach($allowed_origins[$origin] as $host)
            if(str_starts_with($url, $host)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                echo(curl_exec($ch));
                curl_close($ch);      
                return;
            }
    }
}
header("HTTP/1.1 403 Forbidden");