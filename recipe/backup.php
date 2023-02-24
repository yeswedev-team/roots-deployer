<?php

namespace Deployer;

desc('Remote database backup');
task('db:backup', function () {

    $checkCurrent = run("test -L {{deploy_path}}/current && echo '0' || echo '1' ");

    if($checkCurrent === 0) {
        $exportFilename = get('application').'.' . date('Y-m-d-H:i') . '.sql';
        $exportAbsPath  = get('backup_path');
        $exportAbsFile  = $exportAbsPath . '/' . $exportFilename;

        run("mkdir --parents  {$exportAbsPath}");
        run("cd {{current_path}} && {{bin/wp}} db export {{option_export}} {$exportAbsFile}");
        run("gzip {$exportAbsFile}");
        run("find {$exportAbsPath} -name \"{{application}}.????-??-??-??:??.sql.gz\" | sort | head --lines=\"-10\" | xargs rm --force");

        writeln("<comment>Exporting server DB to {$exportAbsFile}</comment>");
    } else {
        writeln("<comment>No previous release existing, can't backup</comment>");
    }
} );
