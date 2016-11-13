<?php

namespace Jacobemerick\CommentService\Controller;

use AvalancheDevelopment\Peel\HttpError\NotFound;
use Interop\Container\ContainerInterface as Container;
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
        $commenterId = $req->getAttribute('swagger')['params']['commenter_id']['value'];

        $commenter = $this->container
            ->get('commenterModel')
            ->findById($commenterId);

        if (!$commenter) {
            throw new NotFound('No commenter found under that id');
        }

        $commenter = $this->container
            ->get('commenterSerializer')
            ->__invoke($commenter);

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

        // todo use swagger params instead
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

        // todo what if the array is empty?
        $commenters = array_map(
            $this->container->get('commenterSerializer'),
            $commenters
        );

        $commenters = json_encode($commenters);
        $res->getBody()->write($commenters);
        return $res;
    }
}
