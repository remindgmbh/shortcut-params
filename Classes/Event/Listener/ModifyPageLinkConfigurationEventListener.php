<?php

declare(strict_types=1);

namespace Remind\ShortcutParams\Event\Listener;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Frontend\Event\ModifyPageLinkConfigurationEvent;

class ModifyPageLinkConfigurationEventListener
{
    public function __construct(
        private readonly PageRepository $pageRepository,
    ) {
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

    public function __invoke(ModifyPageLinkConfigurationEvent $event): void
    {
        $request = $this->getRequest();

        /** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $frontendController */
        $frontendController = $request->getAttribute('frontend.controller');

        $originalPageUid = null;

        if (isset($event->getPage()['_SHORTCUT_ORIGINAL_PAGE_UID'])) {
            $originalPageUid = $event->getLinkDetails()['pageuid'];
        } elseif (isset($frontendController->page['_SHORTCUT_ORIGINAL_PAGE_UID'])) {
            /** @var \TYPO3\CMS\Core\Routing\PageArguments $pageArguments */
            $pageArguments = $request->getAttribute('routing');
            $originalPageUid = $pageArguments->getPageId();
        }

        if ($originalPageUid) {
            $originalPage = $this->pageRepository->getPage($originalPageUid);

            if ($originalPage['doktype'] === PageRepository::DOKTYPE_SHORTCUT) {
                $shortcutParams = $originalPage['shortcut_params'] ?? null;
                if ($shortcutParams) {
                    $queryParameters = json_decode($shortcutParams, true) ?? [];
                    $event->setQueryParameters($queryParameters);
                }
            }
        }
    }
}
