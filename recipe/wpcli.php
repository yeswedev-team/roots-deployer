<?php

namespace Deployer;

// Activate maintenance
set('bin/wp', 'wp');

desc('Activate maintenance mode');
task(
    'maintenance:activate',
    function () {
        run("cd {{release_path}} && {{bin/wp}} maintenance-mode activate");
        info(run("cd {{release_path}} && {{bin/wp}} maintenance-mode status"));
    }
);

// Deactivate maintenance
desc('Deactivate maintenance mode ');
task(
    'maintenance:deactivate',
    function () {
        run("cd {{release_path}} && {{bin/wp}} maintenance-mode deactivate");
        info(run("cd {{release_path}} && {{bin/wp}} maintenance-mode status"));
    }
);

task(
    'acorn:discover',
    function () {
        run(
            "cd {{release_path}} && ".
            "{{bin/wp}} acorn package:discover && {{bin/wp}} acorn optimize"
        );
    }
)->desc('Discover acorn packages');
