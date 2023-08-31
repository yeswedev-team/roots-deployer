<?php

namespace Deployer;

function getPhpVersionFromComposer(): array|string|null
{
    $composerJson = file_get_contents('composer.json');
    $composerData = json_decode($composerJson, true);
    if (!isset($composerData['require']['php'])) {
        throw new \RuntimeException("Unable to find PHP version in composer.json");
    }
    $phpVersion = $composerData['require']['php'];
    $cleanPhpVersion = preg_replace('/[^0-9.]/', '', $phpVersion); // Supprime tout sauf les chiffres et le point

    return $cleanPhpVersion;
}

function getNodeVersionFormPackageJson(string $packageJsonPath)
{
    $packageJson = file_get_contents($packageJsonPath);
    $packageData = json_decode($packageJson, true);
    if (!isset($packageData['engines']['node'])) {
        throw new \RuntimeException("Unable to find Node version in package.json");
    }


    return $packageData['engines']['node'];
}