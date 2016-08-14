<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Serializer\Commenter as CommenterSerializer;
use Psr\Http\Message\ServerRequestInterface as Request;
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
    public function getCommenter(Request $req, Response $res)
    {
        $commenter = $this->container
            ->get('commenterModel')
            ->findById($req->getAttribute('commenter_id'));

        $commenterSerializer = new CommenterSerializer;
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
        $limit = 0;
        $offset = 0;

        $query = $req->getQueryParams();
        if (array_key_exists('per_page', $query)) {
            $limit = $query['per_page'];
        }
        if (array_key_exists('page', $query)) {
            $offset = ($query['page'] - 1) * $query['per_page'];
        }

        $commenters = $this->container
            ->get('commenterModel')
            ->getCommenters($limit, $offset);

        $commenterSerializer = new CommenterSerializer;
        $commenters = array_map($commenterSerializer, $commenters);
        $commenters = json_encode($commenters);

        $res->getBody()->write($commenters);
        return $res;
    }
}
