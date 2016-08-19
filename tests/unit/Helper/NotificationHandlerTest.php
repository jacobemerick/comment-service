<?php

namespace Jacobemerick\CommentService\Helper;

use Aura\Sql\ExtendedPdo;
use Jacobemerick\Archangel\Archangel;
use PHPUnit_Framework_TestCase;

class NotificationHandlerTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfNotificationHandler()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockArchangel = $this->createMock(Archangel::class);
        $notificationHandler = new NotificationHandler($mockPdo, $mockArchangel);

        $this->assertInstanceOf(NotificationHandler::class, $notificationHandler);
    }

    public function testConstructSetsServices()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockArchangel = $this->createMock(Archangel::class);
        $notificationHandler = new NotificationHandler($mockPdo, $mockArchangel);

        $this->assertAttributeSame($mockPdo, 'dbal', $notificationHandler);
        $this->assertAttributeSame($mockArchangel, 'mailer', $notificationHandler);
    }
}
