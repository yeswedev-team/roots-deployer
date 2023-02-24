<?php

namespace Deployer;

set('curl_options', '');
set('public_folder', 'web');

set('opcache_reset_url', 'opcache_reset');

desc('Opcache reset');
task('php:opcache_reset', function () {
    info('deprecated, see https://deployer.org/docs/7.x/avoid-php-fpm-reloading');
    $releases = get('releases_list');
    if (has('previous_release')) {
        run('echo "<?php opcache_reset(); ?>" > {{deploy_path}}/releases/'.$releases[1].'/{{public_folder}}/{{opcache_reset_url}}.php');
        run('echo "<?php opcache_reset(); ?>" > {{release_path}}/{{public_folder}}/{{opcache_reset_url}}.php');
        run('curl {{curl_options}} {{url}}/{{opcache_reset_url}}.php');
        run('rm {{release_path}}/{{public_folder}}/{{opcache_reset_url}}.php');
    }
});

set('clearstatcache_url', 'clearstatcache');

desc('Clear Stat Cache');
task('php:clearstatcache', function () {
    info('deprecated, see https://deployer.org/docs/7.x/avoid-php-fpm-reloading');
    $releases = get('releases_list');
    if (has('previous_release')) {
        run('echo "<?php clearstatcache(); ?>" > {{deploy_path}}/releases/'.$releases[1].'/{{public_folder}}/{{clearstatcache_url}}.php');
        run('echo "<?php clearstatcache(); ?>" > {{release_path}}/{{public_folder}}/{{clearstatcache_url}}.php');
        runLocally('curl {{curl_options}} {{url}}/{{clearstatcache_url}}.php');
        run('rm {{release_path}}/{{public_folder}}/{{clearstatcache_url}}.php');
    }
});

desc ( 'Reload fpm task' );
task ( 'php:opcache:clear', [
    'php:opcache_reset',
    'php:clearstatcache'
] );
