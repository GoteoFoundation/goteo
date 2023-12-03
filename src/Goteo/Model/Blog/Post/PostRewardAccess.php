<?php

namespace Goteo\Model\Blog\Post;

use Goteo\Core\Model;
use Goteo\Model\Blog\Post;
use Goteo\Model\Project\Reward;

class PostRewardAccess extends Model
{
    public ?int $post_id = null;
    public ?int $reward_id = null;

    private Post $post;
    private Reward $reward;

    protected $Table = 'post_reward_access';
    static protected $Table_static = 'post_reward_access';
    public function __construct()
    {
        if ($this->post_id)
            $this->post = Post::get($this->post_id);

        if ($this->reward_id)
            $this->reward = Reward::get($this->reward_id);
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): PostRewardAccess
    {
        $this->post = $post;
        return $this;
    }

    public function getReward(): Reward
    {
        return $this->reward;
    }

    public function setReward(Reward $reward): PostRewardAccess
    {
        $this->reward = $reward;
        return $this;
    }

    static public function count(array $filters = []): int
    {
        $sqlWhere = [];
        $values = [];

        if ($filters['post_id']) {
            $sqlWhere[] = "post_reward_access.post_id = :post_id";
            $values[':post_id'] = $filters['post_id'];
        }

        if ($filters['reward_id']) {
            $sqlWhere[] = "post_reward_access.reward_id = :reward_id";
            $values[':reward_id'] = $filters['reward_id'];
        }

        $where = '';
        if (!empty($sqlWhere)) {
            $where = "WHERE " . implode(" AND ", $sqlWhere);
        }

        $sql = "SELECT count(*)
            FROM post_reward_access
            $where";

        return self::query($sql, $values)->fetchColumn();
    }

    /**
     * @return PostRewardAccess[]
     */
    static public function getList(array $filters = [], int $offset = 0, int $limit = 10): array
    {
        $sqlWhere = [];
        $values = [];

        if ($filters['post_id']) {
            $sqlWhere[] = "post_reward_access.post_id = :post_id";
            $values[':post_id'] = $filters['post_id'];
        }

        if ($filters['reward_id']) {
            $sqlWhere[] = "post_reward_access.reward_id = :reward_id";
            $values[':reward_id'] = $filters['reward_id'];
        }

        $where = '';
        if (!empty($sqlWhere)) {
            $where = "WHERE " . implode(" AND ", $sqlWhere);
        }

        $sql = "SELECT *
            FROM post_reward_access
            $where
            LIMIT $offset, $limit";

        $query = static::query($sql, $values);

        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    public function dbDelete(array $where = ['post_id']): bool
    {
        return parent::dbDelete($where);
    }

    public function save(&$errors = array()): bool
    {
        $this->validate($errors);

        if (!empty($errors)) return false;

        $query = static::query("REPLACE INTO post_reward_access (post_id, reward_id) VALUES (:post_id, :reward_id)", [
            ':post_id' => $this->post_id,
            ':reward_id' => $this->reward_id
        ]);

        return $query->rowCount() === 1;
    }

    public function validate(&$errors = array())
    {
        if (!$this->post_id) {
            $errors[] = 'No post id';
        }

        if (!$this->reward_id) {
            $errors[] = 'No reward id';
        }
    }
}
