<?php

namespace Jacobemerick\CommentService;

use Aura\Sql\ExtendedPdo;

class Commenter
{

    // holder for instance of Aura\Sql\ExtendedPdo
    private $extendedPdo;

    // holder for commenter id (primary key)
    private $id;

    /**
     * basic construct
     *
     * @param   object   $extendedPdo  instance of Aura\Sql\ExtendedPdo
     * @param   integer  $id           primary key of desired commenter
     */
    public function __construct(ExtendedPdo $extendedPdo, $id)
    {
        $this->extendedPdo = $extendedPdo;
        $this->id = $id;
    }

    /**
     * read request for a commenter
     * returns a basic commenter object based on id
     * on failure, returns an empty array
     *
     * @return  array  representation of the Commenter object
     */
    public function read()
    {
        // do a query or whatever
        return [];
    }

}

