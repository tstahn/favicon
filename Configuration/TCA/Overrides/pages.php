<?php
defined('TYPO3_MODE') || die();

call_user_func(
    function () {
        $temporaryColumns = [
            'tx_favicon_favicon' => [
                'displayCond' => 'FIELD:is_siteroot:=:1',
                'exclude' => true,
                'label' => 'LLL:EXT:favicon/Resources/Private/Language/locallang_db.xlf:pages.tx_favicon_favicon',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'tx_favicon_favicon',
                    [
                        'behaviour' => [
                            'allowLanguageSynchronization' => true,
                        ],
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                        ],
                        'overrideChildTca' => [
                            'types' => [
                                \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                    'showitem' => '--palette--;;filePalette'
                                ],
                            ],
                        ],
                        'minitems' => 0,
                        'maxitems' => 1,
                    ],
                    'png'
                ),
            ],
        ];

        $GLOBALS['TCA']['pages']['palettes']['favicon']['showitem'] = 'tx_favicon_favicon';

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $temporaryColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'pages',
            '--palette--;LLL:EXT:favicon/Resources/Private/Language/locallang_db.xlf:pages.palette.favicon;favicon,',
            '1,3,4',
            'after:lastUpdated'
        );
    }
);
