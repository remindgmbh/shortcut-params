<?php

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die;

ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'shortcut_params' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_shortcut_params/Resources/Private/Language/locallang.xlf:shortcut_params',
            'config' => [
                'type' => 'input',
            ],
        ],
    ]
);

ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    'shortcut_params',
    (string) PageRepository::DOKTYPE_SHORTCUT,
    'after:shortcut'
);
