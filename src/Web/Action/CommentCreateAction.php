<?php

namespace Jacobemerick\CommentService\Web\Action;

use Aura\Web\Request;
use Aura\Filter\FilterFactory;

use Jacobemerick\CommentService\Domain\Comment;
use Jacobemerick\CommentService\Domain\Commenter;
use Jacobemerick\CommentService\Action\Responder\CommentCreateResponder;

class CommentCreateAction
{

    protected $request;
    protected $comment;
    protected $commenter;
    protected $filterFactory;
    protected $responder;

    public function __construct(
        Request $request,
        Comment $comment,
        Commenter $commenter,
        FilterFactory $filterFactory,
        CommentCreateResponder $reponder
    ) {
        $this->request = $request;
        $this->comment = $comment;
        $this->commenter = $commenter;
        $this->filterFactory = $filterFactory;
        $this->responder = $responder;
    }

    public function __invoke()
    {
        $this->validateRequest($this->request);
        // create new comment object
        // set response
        // return response
    }

    protected function validateRequest(
        Request $request,
        FilterFactory $filterFactory
    ) {
        $filter = $filterFactory->newInstance();
        $error_list = [];

        if (empty($_POST['commenter'])) {
            array_push($error_list, 'Missing a commenter set of params.');
        } else {
            $filter = $filterFactory->newInstance();

            $filter->addHardRule('name', $filter::IS_NOT, 'blank');
            $filter->addSoftRule('name', $filter::IS, 'string');
            $filter->addSoftRule('name', $filter::IS, 'strlenMax', 100);
            $filter->useFieldMessage('name', 'Name is a required field and cannot be longer than 100 chars.');

            $filter->addHardRule('email', $filter::IS_NOT, 'blank');
            $filter->addSoftRule('email', $filter::IS, 'email');
            $filter->addSoftRule('email', $filter::IS, 'strlenMax', 100);
            $filter->useFieldMessage('email', 'Email is a required field and cannot be longer than 100 chars.');

            if (!empty($_POST['commenter']['url'])) {
                $filter->addSoftRule('url', $filter::IS, 'url');
                $filter->addSoftRule('url', $filter::IS, 'strlenMax', 100);
                $filter->useFieldMessage('url', 'URL must be a valid URL and cannot be longer than 100 chars.');
            }

            if (!empty($_POST['commenter']['key'])) {
                $filter->addSoftRule('key', $filter::IS, 'alnum');
                $filter->addSoftRule('key', $filter::IS, 'strlen', 10);
                $filter->useFieldMessage('key', 'Commenter key was not recognized.');
            }

            $success = $filter->values($_POST['commenter']);
            if (!$success) {
                $errors = $filter->getMessages();
                foreach ($errors as $key_list) {
                    foreach ($key_list as $error_message) {
                        array_push($error_list, $error_message);
                    }
                }
            }
        }

        $filter = $filterFactory->newInstance();

        $filter->addHardRule('body', $filter::IS_NOT, 'blank');
        $filter->addSoftRule('body', $filter::IS, 'string');
        $filter->useFieldMessage('body', 'Comment must have a body attached');

        $success = $filter->values($_POST);
        if (!$success) {
            $errors = $filter->getMessages();
        }
    }

}

