<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Comment
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
     * @param integer $commenter
     * @param integer $body
     * @param integer $location
     * @param integer $request
     * @param integer $notify
     * @param integer $display
     * @param integer $create_time
     * @returns integer
     */
    public function create(
        $commenter,
        $body,
        $location,
        $request,
        $notify,
        $display,
        $create_time
    ) {
        $query = "
            INSERT INTO
                `comment` (`commenter`, `comment_body`, `comment_location`, `comment_request`,
                           `notify`, `display`, `create_time`)
            VALUES
                (:commenter, :body, :location, :request, :notify, :display, :create_time)";

        $bindings = [
            'commenter' => $commenter,
            'body' => $body,
            'location' => $location,
            'request' => $request,
            'notify' => $notify,
            'display' => $display,
            'create_time' => date('Y-m-d H:i:s', $create_time),
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param integer $id
     */
    public function findById($id)
    {
        $query = "
            SELECT
                `comment`.`id`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `comment`.`id` = :id AND
                  `comment`.`is_deleted` = :not_deleted
            LIMIT 1";

        $bindings = [
            'id' => $id,
            'not_deleted' => 0,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }

    /**
     * @param integer $id
     */
    public function deleteById($id)
    {
        $query = "
            UPDATE `comment`
            SET `is_deleted` = :deleted
            WHERE `id` = :id
            LIMIT 1";

        $bindings = [
            'deleted' => 1,
            'id' => $id,
        ];

        return $this->extendedPdo->perform($query, $bindings);
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @returns array
     */
    public function getComments($limit = 0, $offset = 0)
    {
        $query = "
            SELECT
                `comment`.`id`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted";

        if ($limit > 0) {
            $query .= "
                LIMIT {$offset}, {$limit}";
        }

        $bindings = [
            'not_deleted' => 0,
        ];

        return $this->extendedPdo->fetchAll($query, $bindings);
    }
}
