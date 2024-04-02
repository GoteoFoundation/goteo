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
 use Goteo\Entity\Announcement;
use Goteo\Model\Project;
use PDO;
 use PDOException;

 class AnnouncementRepository extends BaseRepository {
     protected ?string $table = 'announcement';

     public function getById(int $id): Announcement
     {
         $sql = "SELECT announcement.*
                FROM announcement
                WHERE announcement.id = ?";

         $announcement = $this->query($sql, [$id])->fetchObject(Announcement::class);

         if (!$announcement instanceOf Announcement)
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

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, Announcement::class );
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

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, Announcement::class );
    }

     public function persist(Announcement $announcement, array &$errors = []): Announcement
     {
         if ($announcement->getId())
            return $this->update($announcement, $errors);

         return $this->create($announcement, $errors);
    }

    private function create(Announcement $announcement, &$errors = []): Announcement {
        $fields = [
            'title' => ':title',
            'description' => ':description',
            'type' => ':type',
            'lang' => ':lang',
            'project' => ':project',
            'cta_url' => ':cta_url',
            'cta_text' => ':cta_text',
            'active' => ':active'
        ];

        $project = $announcement->getProject();
        if ($project instanceof Project)
            $project = $project->id;

        $values = [
            ':title' => $announcement->getTitle(),
            ':description' => $announcement->getDescription(),
            ':type' => $announcement->getType(),
            ':lang' => $announcement->getLang(),
            ':project' => $project,
            ':cta_url' => $announcement->getCtaUrl(),
            ':cta_text' => $announcement->getCtaText(),
            ':active' => $announcement->isActive(),
        ];

        $sql = "INSERT INTO `$this->table` (" . implode(',', array_keys($fields) ) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
            $announcement->setId($this->insertId());
        } catch (PDOException $exception) {
            $errors[] = $exception->getMessage();
            return false;
        }
        return $announcement;

    }

    private function update(Announcement $announcement, &$errors = []): Announcement {
        $fields = [
            'id' => ':id',
            'title' => ':title',
            'description' => ':description',
            'type' => ':type',
            'lang' => ':lang',
            'project' => ':project',
            'cta_url' => ':cta_url',
            'cta_text' => ':cta_text',
            'active' => ':active'
        ];

        $project = $announcement->getProject();
        if ($project instanceof Project)
            $project = $project->id;


        $values = [
            ':id' => $announcement->getId(),
            ':title' => $announcement->getTitle(),
            ':description' => $announcement->getDescription(),
            'type' => $announcement->getType(),
            ':lang' => $announcement->getLang(),
            ':project' => $project,
            ':cta_url' => $announcement->getCtaUrl(),
            ':cta_text' => $announcement->getCtaText(),
            ':active' => $announcement->isActive(),
        ];

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields) ) . ") VALUES (" . implode(',', array_values($fields)) . ")";
        try {
            $this->query($sql, $values);
        } catch (PDOException $exception) {
            $errors[] = $exception->getMessage();
        }
        return $announcement;
    }

    public function delete(Announcement $announcement): void {
        $sql = "DELETE FROM $this->table WHERE $this->table.id = :id";
        try {
            $this->query($sql, [':id' => $announcement->getId()]);
        } catch (PDOException $exception) {
            throw new ModelException($exception->getMessage());
        }
    }
 }
