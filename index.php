<?php

$url = substr($_SERVER["REQUEST_URI"], 1);
$http_origin = array_key_exists("HTTP_ORIGIN", $_SERVER) ? $_SERVER["HTTP_ORIGIN"] : null;
$allowed_origins = [];
foreach(getenv() as $env => $val) {
    if(str_starts_with($env, "ALLOWED_ORIGIN_")) {
        $origin = str_replace("_", "-", str_replace("__", ".", strtolower(substr($env, 15))));
        $allowed_origins[$origin] = explode(";", str_replace(" ", "", $val));
    }
}

if($http_origin != null) {
    header("Access-Control-Allow-Origin: $http_origin");
    header("Access-Control-Request-Method: GET");
    $origin = str_replace("/", "", strtolower($http_origin));
    if(str_starts_with($origin, "http:"))
        $origin = substr($origin, 5);
    else if(str_starts_with($origin, "https:"))
        $origin = substr($origin, 6);
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