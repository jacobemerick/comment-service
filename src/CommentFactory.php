<?php

namespace Jacobemerick\CommentService;

use Aura\Sql\ExtendedPdo;

class CommentFactory
{

    // holder for instance of Aura\Sql\ExtendedPdo
    private $extendedPdo;

    /**
     * basic construct
     *
     * @param   object   $extendedPdo  instance of Aura\Sql\ExtendedPdo
     */
    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    /**
     * read request for a comment
     * returns a basic comment object based on id
     *
     * @return  array  representation of the Comment object
     */
    public function read($id)
    {
        $comment = new Comment($this->extendedPdo, $id);
        return $comment->read();
    }

}

