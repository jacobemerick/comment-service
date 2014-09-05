<?php

namespace Jacobemerick\CommentService;

use Aura\Sql\ExtendedPdo;

class Commenter
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
     * on failure, returns an empty array
     *
     * @param   integer  $id  primary key to fetch on
     * @return  array         representation of the Commenter object
     */
    public function read($id)
    {
         $query = '
            SELECT
                commenter.name,
                commenter.url
            FROM
                commenter
            WHERE
                commenter.id = :commenter_id
            LIMIT 1';

        $params = [
            'commenter_id' => $id,
        ];

        $result = $this->extendedPdo->fetchOne($query, $params);
        return $result;
    }

    /**
     * create request for commenter
     * on fail, returns null
     *
     * @param   array    $data  data passed in from front end
     * @return  integer         primary key that represents the commenter entry
     */
    public function create(array $data)
    {
        $query = '
            INSERT INTO
                commenter (name, email, url, `key`)
            VALUES
                (:name, :email, :url, :key)';

        $params = [
            'name'   => $data['commenter']['name'],
            'email'  => $data['commenter']['email'],
            'url'    => $data['commenter']['url'],
            'key'    => $data['commenter']['key'],
        ];

        try {
            $this->extendedPdo->perform($query, $params);
        } catch (PDOException $e) {
            var_dump($e);
        }

        return $this->extendedPdo->insert_id;
    }

    /**
     * more complex fetch to return a commenter by significant params
     *
     * @param   array   $data  list of params passed in by frontend
     * @return  object         instance of Jacobemerick\CommentService\Commenter
     */
    public function getByParams(array $data)
    {
        $query = '
            SELECT
                id
            FROM
                commenter
            WHERE
                commenter.name = :name &&
                commenter.email = :email &&
                commenter.url = :url
            LIMIT 1';

        $params = [
            'name'   => $data['commenter']['name'],
            'email'  => $data['commenter']['email'],
            'url'    => $data['commenter']['url'],
        ];

        $id = $this->extendedPdo->fetchValue($query, $params);
        if (!$id) {
            return;
        }

        return new Commenter($this->extendedPdo, $id);
    }



}

