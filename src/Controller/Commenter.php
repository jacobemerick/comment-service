<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Model\Commenter as CommenterModel;
use Jacobemerick\CommentService\Serializer\Commenter as CommenterSerializer;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Commenter
{

    /** @var Container */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return Response
     */
    public function createCommenter(Request $req, Response $res)
    {
        $body = $req->getParsedBody();

        $commenterModel = new CommenterModel($this->container->get('dbal'));
        $commenterId = $commenterModel->create(
            $body['name'],
            $body['email'],
            $body['website']
        );

        $commenterSerializer = new CommenterSerializer;
        $commenter = $commenterModel->findById($commenterId);
        $commenter = $commenterSerializer($commenter);
        $commenter = json_encode($commenter);

        $res->getBody()->write($commenter);
        return $res;
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return Response
     */
    public function getCommenter(Request $req, Response $res)
    {
        $commenterModel = new CommenterModel($this->container->get('dbal'));
        $commenterSerializer = new CommenterSerializer;
        $commenter = $commenterModel->findById($req->getAttribute('commenter_id'));
        $commenter = $commenterSerializer($commenter);
        $commenter = json_encode($commenter);

        $res->getBody()->write($commenter);
        return $res;
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return Response
     */
    public function getCommenters(Request $req, Response $res)
    {
        $commenterSerializer = new CommenterSerializer();
        $commenterModel = new CommenterModel($this->container->get('dbal'));
        $commenters = $commenterModel->getCommenters();
        $commenters = array_map($commenterSerializer, $commenters);
        $commenters = json_encode($commenters);

        $res->getBody()->write($commenters);
        return $res;
    }
}
