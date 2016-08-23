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
        $comment = [
            'commenter_id' => 34,
        ];

        $recipientList = [
            [
                'id' => 34,
                'name' => 'Jack Black',
                'email' => 'jack@black.tld',
            ],
        ];

        $mockArchangel = $this->createMock(Archangel::class);
        $mockArchangel->expects($this->never())
            ->method('setFrom');

        $mockCommenterModel = $this->createMock(Commenter::class);
        $mockCommenterModel->method('getNotificationRecipients')
            ->willReturn($recipientList);

        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);
        $result = $notificationHandler->__invoke(123, $comment);

        $this->assertNull($result);
    }

    public function testInvokeHydratesSubject()
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
        ];

        $mockArchangel = $this->createMock(Archangel::class);
        $mockArchangel->method('setFrom')
            ->will($this->returnSelf());
        $mockArchangel->method('setReplyTo')
            ->will($this->returnSelf());
        $mockArchangel->method('addTo')
            ->will($this->returnSelf());

        $mockCommenterModel = $this->createMock(Commenter::class);
        $mockCommenterModel->method('getNotificationRecipients')
            ->willReturn($recipientList);

        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

        $reflectedNotificationHandler = new ReflectionClass($notificationHandler);
        $reflectedGetTemplateParameters = $reflectedNotificationHandler->getMethod('getTemplateParameters');
        $reflectedGetTemplateParameters->setAccessible(true);
        $templateParameters = $reflectedGetTemplateParameters->invokeArgs(
            $notificationHandler,
            [ $comment['domain'] ]
        );
        $reflectedGetSubject = $reflectedNotificationHandler->getMethod('getSubject');
        $reflectedGetSubject->setAccessible(true);
        $subject = $reflectedGetSubject->invokeArgs(
            $notificationHandler,
            [ $templateParameters['domainTitle'] ]
        );

        $mockArchangel->expects($this->once())
            ->method('setSubject')
            ->with(
                $this->equalTo($subject)
            )
            ->will($this->returnSelf());

        $notificationHandler->__invoke(123, $comment);
    }

    public function testInvokeHydratesMessage()
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
        ];

        $mockArchangel = $this->createMock(Archangel::class);
        $mockArchangel->method('setFrom')
            ->will($this->returnSelf());
        $mockArchangel->method('setReplyTo')
            ->will($this->returnSelf());
        $mockArchangel->method('setSubject')
            ->will($this->returnSelf());
        $mockArchangel->method('addTo')
            ->will($this->returnSelf());

        $mockCommenterModel = $this->createMock(Commenter::class);
        $mockCommenterModel->method('getNotificationRecipients')
            ->willReturn($recipientList);

        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

        $reflectedNotificationHandler = new ReflectionClass($notificationHandler);
        $reflectedGetTemplateParameters = $reflectedNotificationHandler->getMethod('getTemplateParameters');
        $reflectedGetTemplateParameters->setAccessible(true);
        $templateParameters = $reflectedGetTemplateParameters->invokeArgs(
            $notificationHandler,
            [ $comment['domain'] ]
        );
        $reflectedGetMessage = $reflectedNotificationHandler->getMethod('getMessage');
        $reflectedGetMessage->setAccessible(true);
        $message = $reflectedGetMessage->invokeArgs(
            $notificationHandler,
            [
                $templateParameters['pageType'],
                $templateParameters['domainTitle'],
                new DateTime($comment['date']),
                $comment['commenter_name'],
                $comment['body'],
                str_replace('{{id}}', $comment['id'], $comment['url']),
            ]
        );

        $mockArchangel->expects($this->once())
            ->method('setPlainMessage')
            ->with(
                $this->equalTo($message)
            );

        $notificationHandler->__invoke(123, $comment);
    }

    public function testInvokeSetsUpEmail()
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
        ];

        $mockArchangel = $this->createMock(Archangel::class);
        $mockArchangel->method('setSubject')
            ->will($this->returnSelf());
        $mockArchangel->method('addTo')
            ->will($this->returnSelf());

        $mockCommenterModel = $this->createMock(Commenter::class);
        $mockCommenterModel->method('getNotificationRecipients')
            ->willReturn($recipientList);

        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);

        $reflectedNotificationHandler = new ReflectionClass($notificationHandler);
        $reflectedFromProperty = $reflectedNotificationHandler->getProperty('from');
        $reflectedFromProperty->setAccessible(true);
        $from = $reflectedFromProperty->getValue($notificationHandler);
        $reflectedReplyToProperty = $reflectedNotificationHandler->getProperty('replyTo');
        $reflectedReplyToProperty->setAccessible(true);
        $replyTo = $reflectedReplyToProperty->getValue($notificationHandler);

        $mockArchangel->expects($this->once())
            ->method('setFrom')
            ->with(
                $this->equalTo($from['email']),
                $this->equalTo($from['name'])
            ) 
            ->will($this->returnSelf());
        $mockArchangel->expects($this->once())
            ->method('setReplyTo')
            ->with(
                $this->equalTo($replyTo['email']),
                $this->equalTo($replyTo['name'])
            )
            ->will($this->returnSelf());

        $notificationHandler->__invoke(123, $comment);
    }

    public function testInvokeSendsEmailForEachRecipient()
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
                'id' => 56,
                'name' => 'Joe Black',
                'email' => 'joe@black.tld',
            ],
        ];

        $mockArchangel = $this->createMock(Archangel::class);
        $mockArchangel->method('setFrom')
            ->will($this->returnSelf());
        $mockArchangel->method('setReplyTo')
            ->will($this->returnSelf());
        $mockArchangel->method('setSubject')
            ->will($this->returnSelf());
        $mockArchangel->expects($this->exactly(2))
            ->method('addTo')
            ->withConsecutive(
                [
                    $this->equalTo($recipientList[0]['email']),
                    $this->equalTo($recipientList[0]['name']),
                ],
                [
                    $this->equalTo($recipientList[1]['email']),
                    $this->equalTo($recipientList[1]['name']),
                ]
            )
            ->will($this->returnSelf());

        $mockCommenterModel = $this->createMock(Commenter::class);
        $mockCommenterModel->method('getNotificationRecipients')
            ->willReturn($recipientList);

        $notificationHandler = new NotificationHandler($mockArchangel, $mockCommenterModel);
        $notificationHandler->__invoke(123, $comment);
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
