<?php

function do_autoload(string $dir)
{

    if (!$dir || !$files = glob($dir . '/*.php')) {
        return;
    }

    foreach ($files as $name) {
        require_once $name;
    }

}

function respond($data = [], $http_code = 200) {
	http_response_code($http_code);
	header('Content-type: application/json');
	echo json_encode($data);
	exit();
}