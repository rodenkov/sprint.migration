<?php

namespace IBS\Migration\Builders;

use IBS\Migration\AbstractBuilder;
use IBS\Migration\Enum\VersionEnum;
use IBS\Migration\Locale;
use IBS\Migration\VersionManager;

class MarkerBuilder extends AbstractBuilder
{

    protected function isBuilderEnabled()
    {
        return true;
    }


    protected function initialize()
    {
        $this->setTitle(Locale::getMessage('MARK'));
        $this->setGroup('Tools');

        $this->addField('mark_version', [
            'title' => Locale::getMessage('MARK_FIELD1'),
            'placeholder' => Locale::getMessage('MARK_VERSION'),
            'width' => 250,
        ]);

        $this->addField('mark_status', [
            'title' => Locale::getMessage('MARK_FIELD2'),
            'placeholder' => '',
            'width' => 250,
            'select' => [
                [
                    'title' => Locale::getMessage('MARK_AS_INSTALLED'),
                    'value' => VersionEnum::STATUS_INSTALLED,
                ],
                [
                    'title' => Locale::getMessage('MARK_AS_NEW'),
                    'value' => VersionEnum::STATUS_NEW,
                ],
            ],
        ]);

    }

    protected function execute()
    {
        $version = $this->getFieldValue('mark_version');
        $status = $this->getFieldValue('mark_status');

        $versionManager = new VersionManager(
            $this->getVersionConfig()->getName()
        );

        $markresult = $versionManager->markMigration(
            $version,
            $status
        );

        $this->outMessages($markresult);
    }
}
