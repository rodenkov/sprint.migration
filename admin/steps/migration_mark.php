<?php

use IBS\Migration\VersionConfig;
use IBS\Migration\VersionManager;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$existsEvents = (
($_POST["step_code"] == "migration_mark")
);

if ($existsEvents && check_bitrix_sessid('send_sessid')) {

    /** @var $versionConfig VersionConfig */
    $versionManager = new VersionManager($versionConfig);

    $version = !empty($_POST['version']) ? $_POST['version'] : '';
    $status = !empty($_POST['status']) ? $_POST['status'] : '';

    $markresult = $versionManager->markMigration($version, $status);
    IBS\Migration\Out::outMessages($markresult);

    ?>
    <script>
        migrationMigrationRefresh(function () {
            migrationScrollList();
            migrationEnableButtons(1);
        });
    </script><?php
}
