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

set('bin/node', '{{node_bin}}');

desc('Compile the theme on server');
task(
    'sage:build:assets:server',
    function () {
        run(
            'export PATH={{bin/node}}:$PATH && cd {{release_path}}/{{theme_path}} && {{bin/node}}/yarn && cd {{release_path}}/{{theme_path}} && {{bin/node}}/yarn build'
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

set('sage/dist_path', '/public');
set('sage/build_command', 'build');

desc('Bedrock build assets locally and upload');
task(
    'sage:build:assets:local',
    function () {
        runLocally("cd {{local_root}}/{{theme_path}} && yarn");
        runLocally("cd {{local_root}}/{{theme_path}} && yarn build");
        upload('{{local_root}}/{{theme_path}}{{sage/dist_path}}', '{{release_path}}/{{theme_path}}');
        upload('{{local_root}}/{{theme_path}}/node_modules', '{{release_path}}/{{theme_path}}');
    }
);
