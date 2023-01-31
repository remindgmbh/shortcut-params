<?php

declare(strict_types=1);

namespace Remind\ShortcutParams\Hooks;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\TypolinkModifyLinkConfigForPageLinksHookInterface;

class TypolinkModifyLinkConfigForPageLinksHook implements TypolinkModifyLinkConfigForPageLinksHookInterface
{
    private PageRepository $pageRepository;
    public function __construct()
    {
        $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);
    }

    public function modifyPageLinkConfiguration(array $conf, array $linkDetails, array $page): array
    {
        if (isset($page['_SHORTCUT_ORIGINAL_PAGE_UID'])) {
            $shortcutPageUid = (int) $linkDetails['pageuid'];
            $shortcutPage = $this->pageRepository->getPage($shortcutPageUid);
            $shortcutParams = $shortcutPage['shortcut_params'] ?? null;
            if ($shortcutParams) {
                if (!str_starts_with($shortcutParams, '&')) {
                    $shortcutParams = '&' . $shortcutParams;
                }
                $conf['additionalParams'] = $shortcutParams;
            }
        }
        return $conf;
    }
}
