<?php

namespace Jacobemerick\CommentService\Helper;

use Aura\Sql\ExtendedPdo;
use DateTime;
use Jacobemerick\Archangel\Archangel;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

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

    public function testInvokeFetchesListOfNotificationRecipients()
    {
        $this->markTestIncomplete();
    }

    public function testInvokeFiltersNotificationRecipients()
    {
        $this->markTestIncomplete();
    }

    public function testInvokeBailsIfNotificationRecipientsIsEmpty()
    {
        $this->markTestIncomplete();
    }

    public function testInvokeHydratesSubject()
    {
        $this->markTestIncomplete();
    }

    public function testInvokeHydratesMessage()
    {
        $this->markTestIncomplete();
    }

    public function testInvokeSetsUpEmail()
    {
        $this->markTestIncomplete();
    }

    public function testInvokeSendsEmailForEachRecipient()
    {
        $this->markTestIncomplete();
    }

    public function testGetTemplateParametersForBlog()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockArchangel = $this->createMock(Archangel::class);
        $notificationHandler = new NotificationHandler($mockPdo, $mockArchangel);

        $reflectedGetTemplateParameters = (new ReflectionClass($notificationHandler))
            ->getMethod('getTemplateParameters');
        $reflectedGetTemplateParameters->setAccessible(true);

        $templateParameters = $reflectedGetTemplateParameters->invokeArgs(
            $notificationHandler,
            [ 'blog.jacobemerick.com' ]
        );

        $this->assertEquals(
            [
                'pageType' => 'post',
                'domainTitle' => "Jacob Emerick's Blog",
            ],
            $templateParameters
        );
    }

    public function testGetTemplateParametersForWaterfalls()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockArchangel = $this->createMock(Archangel::class);
        $notificationHandler = new NotificationHandler($mockPdo, $mockArchangel);

        $reflectedGetTemplateParameters = (new ReflectionClass($notificationHandler))
            ->getMethod('getTemplateParameters');
        $reflectedGetTemplateParameters->setAccessible(true);

        $templateParameters = $reflectedGetTemplateParameters->invokeArgs(
            $notificationHandler,
            [ 'waterfallsofthekeweenaw.com' ]
        );

        $this->assertEquals(
            [
                'pageType' => 'page',
                'domainTitle' => 'Waterfalls of the Keweenaw',
            ],
            $templateParameters
        );
    }

    public function testGetTemplateParametersForDefault()
    {
        $undefinedDomain = 'domain.tld';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockArchangel = $this->createMock(Archangel::class);
        $notificationHandler = new NotificationHandler($mockPdo, $mockArchangel);

        $reflectedGetTemplateParameters = (new ReflectionClass($notificationHandler))
            ->getMethod('getTemplateParameters');
        $reflectedGetTemplateParameters->setAccessible(true);

        $templateParameters = $reflectedGetTemplateParameters->invokeArgs(
            $notificationHandler,
            [ $undefinedDomain ]
        );

        $this->assertEquals(
            [
                'pageType' => 'page',
                'domainTitle' => $undefinedDomain,
            ],
            $templateParameters
        );
    }

    public function testGetSubject()
    {
        $domainTitle = 'Waterfalls of the Keweenaw';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockArchangel = $this->createMock(Archangel::class);
        $notificationHandler = new NotificationHandler($mockPdo, $mockArchangel);

        $reflectedNotificationHandler = new ReflectionClass($notificationHandler);
        $reflectedSubjectTemplate = $reflectedNotificationHandler->getProperty('subjectTemplate');
        $reflectedSubjectTemplate->setAccessible(true);
        $reflectedGetSubject = $reflectedNotificationHandler->getMethod('getSubject');
        $reflectedGetSubject->setAccessible(true);

        $subjectTemplate = $reflectedSubjectTemplate->getValue($notificationHandler);
        $expectedSubject = sprintf($subjectTemplate, $domainTitle);

        $subject = $reflectedGetSubject->invokeArgs($notificationHandler, [ $domainTitle ]);

        $this->assertEquals($expectedSubject, $subject);
    }

    public function testGetMessage()
    {
        $pageType = 'post';
        $domainTitle = 'some blog';
        $commentDate = new DateTime('4:13 pm August 21, 2015');
        $commenterName = 'Jack Black';
        $comment = 'this is a comment';
        $commentUrl = 'blog.tld/path';
 
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockArchangel = $this->createMock(Archangel::class);
        $notificationHandler = new NotificationHandler($mockPdo, $mockArchangel);

        $reflectedNotificationHandler = new ReflectionClass($notificationHandler);
        $reflectedMessageTemplate = $reflectedNotificationHandler->getProperty('messageTemplate');
        $reflectedMessageTemplate->setAccessible(true);
        $reflectedGetMessage = $reflectedNotificationHandler->getMethod('getMessage');
        $reflectedGetMessage->setAccessible(true);

        $messageTemplate = $reflectedMessageTemplate->getValue($notificationHandler);
        $expectedMessage = sprintf($messageTemplate,
            $pageType,
            $domainTitle,
            $commentDate->format('F j, Y g:i a'),
            $commenterName,
            $comment,
            $commentUrl
        );

        $message = $reflectedGetMessage->invokeArgs($notificationHandler, [
            $pageType,
            $domainTitle,
            $commentDate,
            $commenterName,
            $comment,
            $commentUrl
        ]);

        $this->assertEquals($expectedMessage, $message);
    }
}
