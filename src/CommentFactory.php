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
     * basic fetch to return a comment by id
     *
     * @return  object  instance of Jacobemerick\CommentService\Comment
     */
    public function getCommentByID($id)
    {
        return new Comment($this->extendedPdo, $id);
    }

}

