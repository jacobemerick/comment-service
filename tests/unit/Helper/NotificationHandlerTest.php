<?php

namespace Jacobemerick\CommentService\Helper;

use DateTime;
use Jacobemerick\Archangel\Archangel;
use Jacobemerick\CommentService\Model\Commenter;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class NotificationHandlerTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfNotificationHandler()
    {
        $mockArchangel = $this->createMock(Archangel::class);
        $mockCommenterModel = $this->createMock(Commenter::class);
        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

        $this->assertInstanceOf(NotificationHandler::class, $notificationHandler);
    }

    public function testConstructSetsServices()
    {
        $mockArchangel = $this->createMock(Archangel::class);
        $mockCommenterModel = $this->createMock(Commenter::class);
        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

        $this->assertAttributeSame($mockArchangel, 'mailer', $notificationHandler);
        $this->assertAttributeSame($mockCommenterModel, 'commenterModel', $notificationHandler);
    }

    public function testInvokeFetchesListOfNotificationRecipients()
    {
        $locationId = 123;

        $mockArchangel = $this->createMock(Archangel::class);

        $mockCommenterModel = $this->createMock(Commenter::class);
        $mockCommenterModel->expects($this->once())
            ->method('getNotificationRecipients')
            ->with(
                $this->equalTo($locationId)
            )
            ->willReturn([]);

        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);
        $notificationHandler->__invoke($locationId, []);
    }

    public function testInvokeFiltersNotificationRecipients()
    {
        $comment = [
            'id' => 341,
            'commenter_id' => 34,
            'commenter_name' => 'Jack Black',
            'body' => 'this is a comment',
            'domain' => 'blog.jacobemerick.com',
            'date' => date('Y-m-d H:i:s'),
            'url' => 'http://domain.tld/comment-{{id}}',
        ];

        $recipientList = [
            [
                'id' => 12,
                'name' => 'Jane Black',
                'email' => 'jane@black.tld',
            ],
            [
                'id' => 34,
                'name' => 'Jack Black',
                'email' => 'jack@black.tld',
            ],
        ];

        $filteredRecipientList = array_filter($recipientList, function ($recipient) use ($comment) {
            return $recipient['id'] !== $comment['commenter_id'];
        });
        $filteredRecipient = array_pop($filteredRecipientList);

        $mockArchangel = $this->createMock(Archangel::class);
        $mockArchangel->method('setFrom')
            ->will($this->returnSelf());
        $mockArchangel->method('setReplyTo')
            ->will($this->returnSelf());
        $mockArchangel->method('setSubject')
            ->will($this->returnSelf());
        $mockArchangel->expects($this->once())
            ->method('addTo')
            ->with(
                $this->equalTo($filteredRecipient['email']),
                $this->equalTo($filteredRecipient['name'])
            )
            ->will($this->returnSelf());

        $mockCommenterModel = $this->createMock(Commenter::class);
        $mockCommenterModel->method('getNotificationRecipients')
            ->willReturn($recipientList);

        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);
        $notificationHandler->__invoke(123, $comment);
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
        $mockArchangel = $this->createMock(Archangel::class);
        $mockCommenterModel = $this->createMock(Commenter::class);
        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

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
        $mockArchangel = $this->createMock(Archangel::class);
        $mockCommenterModel = $this->createMock(Commenter::class);
        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

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

        $mockArchangel = $this->createMock(Archangel::class);
        $mockCommenterModel = $this->createMock(Commenter::class);
        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

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

        $mockArchangel = $this->createMock(Archangel::class);
        $mockCommenterModel = $this->createMock(Commenter::class);
        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

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
 
        $mockArchangel = $this->createMock(Archangel::class);
        $mockCommenterModel = $this->createMock(Commenter::class);
        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

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
