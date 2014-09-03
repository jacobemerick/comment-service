<?php

namespace Jacobemerick\CommentService;

use Aura\Sql\ExtendedPdo;

class CommenterFactory
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
     * read request for a commenter
     * returns a basic commenter object based on id
     *
     * @return  array  representation of the Commenter object
     */
    public function read($id)
    {
        $commenter = new Commenter($this->extendedPdo, $id);
        return $commenter->read();
    }

}

