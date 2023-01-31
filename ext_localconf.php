<?php

use Remind\ShortcutParams\Hooks\TypolinkModifyLinkConfigForPageLinksHook;

defined('TYPO3_MODE') || die('Access denied.');

(function () {
    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SC_OPTIONS']
        ['typolinkProcessing']
        ['typolinkModifyParameterForPageLinks']
        [TypolinkModifyLinkConfigForPageLinksHook::class] = TypolinkModifyLinkConfigForPageLinksHook::class;
})();
