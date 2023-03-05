<?php

namespace Deployer;

use Exception;

/**
 * @throws Exception
 */
function checkURLs(array $urls)
{
    $max_concurrent_requests = 40;

    $chunks = array_chunk($urls, $max_concurrent_requests);

    foreach ($chunks as $chunk) {
        info("Launch $max_concurrent_requests requests");
        check100URLs($chunk);
    }
}

function check100URLs(array $urls)
{
    foreach ($urls as $url) {
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        if ($extension === 'xml') {
            $sitemap = run("curl {{curl_options}} $url");
            preg_match_all('/<loc>(.*?)<\/loc>/', $sitemap, $matches);
            $urls = $matches[1];
            checkURLs($urls);
        }
    }

    $multi_handle = curl_multi_init();

    $curl_handles = array_map(
        static function ($url) use ($multi_handle) {
            $curl_handle = curl_init($url);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            $userpwd = get('auth_basic');
            if ($userpwd && $userpwd !== '') {
                curl_setopt($curl_handle, CURLOPT_USERPWD, $userpwd);
            }
            curl_multi_add_handle($multi_handle, $curl_handle);
            return $curl_handle;
        },
        $urls
    );

    do {
        $status = curl_multi_exec($multi_handle, $active);
        if ($active) {
            curl_multi_select($multi_handle);
        }
    } while ($active && $status == CURLM_OK);

    foreach ($curl_handles as $curl_handle) {
        $url = curl_getinfo($curl_handle, CURLINFO_EFFECTIVE_URL);
        $http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
        info('http_code:' . $http_code);
        if ($http_code !== 200) {
            throw new Exception("$url returned $http_code");
        }
        curl_multi_remove_handle($multi_handle, $curl_handle);
        curl_close($curl_handle);
    }

    curl_multi_close($multi_handle);
}

desc('Check sitemap URLs HTTP status');
task(
    'check:http',
    function () {
        $base_url = get('url');
        $sitemap_url = $base_url . '/' . get('sitemap_filename', 'sitemap.xml');
        $sitemap = run("curl {{curl_options}} $sitemap_url");
        preg_match_all('/<loc>(.*?)<\/loc>/', $sitemap, $matches);
        $urls = $matches[1];

        checkURLs($urls);
    }
);
