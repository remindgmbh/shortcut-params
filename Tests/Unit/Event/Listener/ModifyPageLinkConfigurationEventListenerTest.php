<?php

declare(strict_types=1);

namespace Remind\ShortcutParams\Tests\Unit\Event\Listener;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;
use Remind\ShortcutParams\Event\Listener\ModifyPageLinkConfigurationEventListener;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Frontend\Event\ModifyPageLinkConfigurationEvent;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ModifyPageLinkConfigurationEventListener::class)]
final class ModifyPageLinkConfigurationEventListenerTest extends UnitTestCase
{
    private mixed $originalTypo3Request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalTypo3Request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        unset($GLOBALS['TYPO3_REQUEST']);
    }

    protected function tearDown(): void
    {
        if ($this->originalTypo3Request === null) {
            unset($GLOBALS['TYPO3_REQUEST']);
        } else {
            $GLOBALS['TYPO3_REQUEST'] = $this->originalTypo3Request;
        }

        parent::tearDown();
    }

    #[Test]
    public function invokeReturnsEarlyWhenNoRequestIsAvailable(): void
    {
        $pageRepository = $this->createMock(PageRepository::class);
        $pageRepository->expects(self::never())->method('getPage');

        $event = new ModifyPageLinkConfigurationEvent(
            [],
            ['pageuid' => 123],
            ['_SHORTCUT_ORIGINAL_PAGE_UID' => 123],
            ['keep' => 'value'],
            ''
        );

        $listener = new ModifyPageLinkConfigurationEventListener($pageRepository);
        $listener($event);

        self::assertSame(['keep' => 'value'], $event->getQueryParameters());
    }

    #[Test]
    public function invokeSetsQueryParametersForShortcutPage(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createMock(ServerRequestInterface::class);

        $pageRepository = $this->createMock(PageRepository::class);
        $pageRepository
            ->expects(self::once())
            ->method('getPage')
            ->with(123)
            ->willReturn([
                'doktype' => PageRepository::DOKTYPE_SHORTCUT,
                'shortcut_params' => '{"foo":"bar"}',
            ]);

        $event = new ModifyPageLinkConfigurationEvent(
            [],
            ['pageuid' => 123],
            ['_SHORTCUT_ORIGINAL_PAGE_UID' => 123],
            [],
            ''
        );

        $listener = new ModifyPageLinkConfigurationEventListener($pageRepository);
        $listener($event);

        self::assertSame(['foo' => 'bar'], $event->getQueryParameters());
    }
}
