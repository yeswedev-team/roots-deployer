<?php

namespace Deployer;

desc('Sage : runs composer install on remote server');
task(
    'sage:vendors',
    fn() => run(
        'cd {{release_path}}/{{theme_path}} && ' .
        '{{bin/composer}} {{composer_action}} {{composer_options}} 2>&1'
    )
);

set('node_bin', '');
set('node_builder', 'yarn');
set('node_script', 'build');

desc('Compile the theme on server');
task(
    'sage:build:assets:server',
    function () {
        run(
            'export PATH={{node_bin}}:$PATH && echo $PATH {{node_bin}} && ' .
            'cd {{release_path}}/{{theme_path}} && {{node_bin}}/{{node_builder}}'
        );
        run(
            'export PATH={{node_bin}}:$PATH && echo $PATH {{node_bin}} && ' .
            'cd {{release_path}}/{{theme_path}} && {{node_bin}}/{{node_builder}} {{node_script}}'
        );
    }
);

desc('Remove nodes_modules folder from previous release');
task(
    'sage:clean:nodemodules',
    function () {
        $releases = get('releases_list');
        $keepReleases = get('keep_releases');

        if ($keepReleases === 1) {
            echo "\033[0;33mNo previous release existing, nothing to remove\n";
        } else {
            run("rm -rf {{deploy_path}}/releases/{$releases[1]}/{{theme_path}}/node_modules");
        }
    }
);

set('option_export', '');
set('bin/wp', 'wp');

task(
    'acorn:discover',
    function () {
        run("cd {{release_path}} && {{bin/wp}} acorn package:discover && {{bin/wp}} acorn optimize:clear");
    }
)->desc('Discover acorn packages');

set('sage/dist_path', '/public');
set('sage/build_command', 'build');

desc('Bedrock build assets locally and upload');
task(
    'sage:build:assets:local',
    function () {
        runLocally("cd {{local_root}}/{{theme_path}} && yarn run {{sage/build_command}}");
        upload('{{local_root}}/{{theme_path}}{{sage/dist_path}}', '{{release_path}}/{{theme_path}}');
        upload('{{local_root}}/{{theme_path}}/node_modules', '{{release_path}}/{{theme_path}}');
    }
);
