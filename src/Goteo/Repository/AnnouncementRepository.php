<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with
 */

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model\Announcement;
use Goteo\Model\Project;
use PDO;
use PDOException;

class AnnouncementRepository extends BaseRepository
{
    protected ?string $table = 'announcement';

    public function getById(int $id): Announcement
    {
        $sql = "SELECT announcement.*
                FROM announcement
                WHERE announcement.id = ?";

        $announcement = $this->query($sql, [$id])->fetchObject(Announcement::class);

        if (!$announcement instanceof Announcement)
            throw new ModelNotFoundException("Announcement with id $id not found");

        return $announcement;
    }

    public function count(): int
    {
        $sql = "SELECT count(announcement.id)
                FROM announcement";

        return $this->query($sql)->fetchColumn();
    }

    /**
     * @return Announcement[]
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        $sql = "SELECT announcement.*
                FROM announcement
                LIMIT $limit
                OFFSET $offset";

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, Announcement::class);
    }

    /**
     * @return Announcement[]
     */
    public function getActiveList(int $offset = 0, int $limit = 10): array
    {
        $sql = "SELECT announcement.*
                FROM announcement
                WHERE active
                LIMIT $limit
                OFFSET $offset";

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, Announcement::class);
    }

    public function getActiveWithoutDonationList(int $offset = 0, int $limit = 10): array
    {
        $sql = "SELECT announcement.*
                FROM announcement
                WHERE active and type != ?
                LIMIT $limit
                OFFSET $offset";

        return $this->query($sql, [Announcement::TYPE_DONATION])->fetchAll(PDO::FETCH_CLASS, Announcement::class);
    }

    public function persist(Announcement $announcement, array &$errors = []): Announcement
    {
        if ($announcement->id)
            return $this->update($announcement, $errors);

        return $this->create($announcement, $errors);
    }

    private function create(Announcement $announcement, &$errors = []): Announcement
    {
        $fields = [
            'title' => ':title',
            'description' => ':description',
            'type' => ':type',
            'lang' => ':lang',
            'project_id' => ':project_id',
            'cta_url' => ':cta_url',
            'cta_text' => ':cta_text',
            'active' => ':active'
        ];

        $project = $announcement->project;
        if ($project instanceof Project)
            $project = $project->id;

        $values = [
            ':title' => $announcement->title,
            ':description' => $announcement->description,
            ':type' => $announcement->type,
            ':lang' => $announcement->lang,
            ':project_id' => $project,
            ':cta_url' => $announcement->cta_url,
            ':cta_text' => $announcement->cta_text,
            ':active' => $announcement->active
        ];

        $sql = "INSERT INTO `$this->table` (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
            $announcement->id = $this->insertId();
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
            throw new ModelException($e->getMessage());
        }

        return $announcement;
    }

    private function update(Announcement $announcement, &$errors = []): Announcement
    {
        $fields = [
            'id' => ':id',
            'title' => ':title',
            'description' => ':description',
            'type' => ':type',
            'lang' => ':lang',
            'project_id' => ':project_id',
            'cta_url' => ':cta_url',
            'cta_text' => ':cta_text',
            'active' => ':active'
        ];

        $project = $announcement->project;
        if ($project instanceof Project)
            $project = $project->id;


        $values = [
            ':id' => $announcement->id,
            ':title' => $announcement->title,
            ':description' => $announcement->description,
            'type' => $announcement->type,
            ':lang' => $announcement->lang,
            ':project_id' => $project,
            ':cta_url' => $announcement->cta_url,
            ':cta_text' => $announcement->cta_text,
            ':active' => $announcement->active,
        ];

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";
        try {
            $this->query($sql, $values);
        } catch (PDOException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $announcement;
    }

    public function delete(Announcement $announcement): void
    {
        $sql = "DELETE FROM $this->table WHERE $this->table.id = :id";
        try {
            $this->query($sql, [':id' => $announcement->id]);
        } catch (PDOException $exception) {
            throw new ModelException($exception->getMessage());
        }
    }
}
