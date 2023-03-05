<?php

namespace Deployer;

set('auth_basic', '');

set(
    'curl_options',
    function () {
        $options = get('auth_basic');

        if ($options && $options !== "") {
            return "-u " . $options;
        }

        return "";
    }
);
