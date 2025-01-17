<?
/**
 * @var $arGadgetParams array
 */

use Bitrix\Main\Loader;
use IBS\Migration\Locale;
use IBS\Migration\Module;
use IBS\Migration\SchemaManager;
use IBS\Migration\VersionConfig;
use IBS\Migration\VersionManager;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

try {

    if (!Loader::includeModule('ibs.migration')) {
        Throw new Exception('need to install module ibs.migration');
    }

    if ($APPLICATION->GetGroupRight('ibs.migration') == 'D') {
        Throw new Exception(Locale::getMessage("ACCESS_DENIED"));
    }

    Module::checkHealth();

    $arGadgetParams['SELECT_CONFIGS'] = is_array($arGadgetParams['SELECT_CONFIGS']) ? $arGadgetParams['SELECT_CONFIGS'] : [];
    $arGadgetParams['CHECK_SCHEMAS'] = is_array($arGadgetParams['CHECK_SCHEMAS']) ? $arGadgetParams['CHECK_SCHEMAS'] : [];

    $results = [];

    $configs = (new VersionConfig())->getList();
    foreach ($configs as $config) {

        if (!empty($arGadgetParams['SELECT_CONFIGS'])) {
            if (!in_array($config['name'], $arGadgetParams['SELECT_CONFIGS'])) {
                continue;
            }
        }

        $versionManager = new VersionManager(
            $config['name']
        );
        $hasNewVersions = count($versionManager->getVersions([
            'status' => 'new',
        ]));

        $results[] = [
            'title' => $config['title'],
            'text' => ($hasNewVersions) ? Locale::getMessage('GD_MIGRATIONS_RED') : Locale::getMessage('GD_MIGRATIONS_GREEN'),
            'state' => ($hasNewVersions) ? 'red' : 'green',
            'buttons' => [
                [
                    'text' => Locale::getMessage('GD_SHOW'),
                    'title' => Locale::getMessage('GD_SHOW_MIGRATIONS'),
                    'url' => '/bitrix/admin/ibs_migrations.php?' . http_build_query([
                            'config' => $config['name'],
                            'lang' => LANGUAGE_ID,
                        ]),
                ],
            ],
        ];

        if (!empty($arGadgetParams['CHECK_SCHEMAS'])) {

            $schemaManager = new SchemaManager(
                $config['name']
            );

            $modifiedCnt = 0;
            $enabledSchemas = $schemaManager->getEnabledSchemas();
            foreach ($enabledSchemas as $schema) {
                if (!in_array($schema->getName(), $arGadgetParams['CHECK_SCHEMAS'])) {
                    continue;
                }

                if ($schema->isModified()) {
                    $modifiedCnt++;
                }
            }

            $results[] = [
                'title' => $config['schema_title'],
                'text' => ($modifiedCnt) ? Locale::getMessage('GD_SCHEMA_RED') : Locale::getMessage('GD_SCHEMA_GREEN'),
                'state' => ($modifiedCnt) ? 'red' : 'green',
                'buttons' => [
                    [
                        'text' => Locale::getMessage('GD_SHOW'),
                        'title' => Locale::getMessage('GD_SHOW_SCHEMAS'),
                        'url' => '/bitrix/admin/ibs_migrations.php?' . http_build_query([
                                'schema' => $config['name'],
                                'lang' => LANGUAGE_ID,
                            ]),
                    ],
                ],
            ];
        }
    }

    include __DIR__ . '/includes/style.php';
    include __DIR__ . '/includes/interface.php';

} catch (Exception $e) {
    include __DIR__ . '/includes/style.php';
    include __DIR__ . '/includes/errors.php';
}