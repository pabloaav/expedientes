<?php

    if (!function_exists('getDominioApi')) {

        function getDominioApi()
        {
            $url = "https://".$_SERVER['SERVER_NAME']."/api";
            return $url;
        }
    }
