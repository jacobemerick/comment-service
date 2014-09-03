<?php

namespace Jacobemerick\CommentService;

use Aura\Sql\ExtendedPdo;

class Comment
{

    // holder for instance of Aura\Sql\ExtendedPdo
    private $extendedPdo;

    // holder for comment id (primary key)
    private $id;

    /**
     * basic construct
     *
     * @param   object   $extendedPdo  instance of Aura\Sql\ExtendedPdo
     * @param   integer  $id           primary key of desired comment
     */
    public function __construct(ExtendedPdo $extendedPdo, $id)
    {
        $this->extendedPdo = $extendedPdo;
        $this->id = $id;
    }

    /**
     * read request for a comment
     * returns a basic comment object based on id
     * on failure, returns an empty array
     *
     * @return  array  representation of the Comment object
     */
    public function read()
    {
        // do a query or whatever
        return [];
    }

}

