<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Commenter
{

    /** @var ExtendedPdo */
    protected $extendedPdo;

    /**
     * @param ExtendedPdo $extendedPdo
     */
    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $website
     * @return integer
     */
    public function create($name, $email, $website)
    {
        $query = "
            INSERT INTO
                `commenter` (`name`, `email`, `website`)
            VALUES
                (:name, :email, :website)";

        $bindings = [
            'name' => $name,
            'email' => $email,
            'website' => $website,
        ];

        if (!$this->extendedPdo->perform($query, $bindings)) {
            return false;
        }

        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param integer $commenterId
     * @return array
     */
    public function findById($commenterId)
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `id` = :id
            LIMIT 1";

        $bindings = [
            'id' => $commenterId,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $website
     * @return array
     */
    public function findByFields($name, $email, $website)
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `name` = :name AND
                  `email` = :email AND
                  `website` = :website
            LIMIT 1";

        $bindings = [
            'name' => $name,
            'email' => $email,
            'website' => $website,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function getCommenters($limit = 0, $offset = 0)
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `is_trusted` = :trusted";

        if ($limit > 0) {
            $query .= "
                LIMIT {$offset}, {$limit}";
        }

        $bindings = [
            'trusted' => 1,
        ];

        return $this->extendedPdo->fetchAll($query, $bindings);
    }

    /**
     * @param integer $locationId
     * @return array
     */
    public function getNotificationRecipients($locationId)
    {
        $query = "
            SELECT `commenter`.`id`, `name`, `email`
            FROM `commenter`
            INNER JOIN `comment` ON `comment`.`commenter` = `commenter`.`id` AND
                                    `comment`.`comment_location` = :location AND
                                    `comment`.`notify` = :should_notify AND
                                    `comment`.`display` = :is_displayed
            GROUP BY `commenter`.`id`";

        $bindings = [
            'location' => $locationId,
            'should_notify' => 1,
            'is_displayed' => 1,
        ];

        return $this->extendedPdo->fetchAll($query, $bindings);
    }
}
