<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model;

use Goteo\Application\Lang;
use Goteo\Core\Model;
use Goteo\Model\Project;
use PDO;
use PDOException;

class Announcement extends Model
{

    const TYPE_GENERAL = 'general';
    const TYPE_DONATION = 'donation';
    const TYPE_PROJECT = 'project';

    public ?int $id = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?Project $project = null;
    public string $type = self::TYPE_GENERAL;
    public string $lang;
    public ?string $cta_url = null;
    public ?string $cta_text = null;
    public bool $active = false;
    public ?\DateTime $start_date;
    public ?\DateTime $end_date;

    public function getList(array $filter = [], int $offset = 0, int $limit = 10, $lang = null): array
    {
        if (!$lang) $lang = Lang::current();

        $sql = "SELECT announcement.*
            FROM announcement
            LIMIT $limit
            OFFSET $offset";

        return $this->query($sql)->fetchAll(PDO::FETCH_CLASS, Announcement::class);
    }

    public static function getLangFields(): array
    {
        return ['title', 'description', 'cta_url', 'cta_text'];
    }

    public function validate(&$errors = []): bool
    {

        if (!$this->title)
            $errors['title'] = 'Missing title';

        if (!$this->description)
            $errors['description'] = 'Missing description';

        return empty($errors);
    }


    public function save(&$errors = []): bool
    {
        if (!$this->validate($errors)) return false;

        $fields = [
            'id',
            'title',
            'description',
            'project',
            'type',
            'lang',
            'cta_url',
            'cta_text',
            'active',
            'start_date',
            'end_date'
        ];

        try {
            $this->dbInsertUpdate($fields);
        } catch (PDOException $e) {
            $errors[] = "Error insert/update announcemenet: " . $e->getMessage();
            return false;
        }
    }

    public function hasCTA(): bool
    {
        return (isset($this->cta_text) && isset($this->cta_url));
    }

    public function isAProjectAnnouncement(): bool
    {
        return self::TYPE_PROJECT == $this->type;
    }

    public function isAGeneralAnnouncement(): bool
    {
        return self::TYPE_GENERAL == $this->type;
    }

    public function isADonationAnnouncement(): bool
    {
        return self::TYPE_DONATION == $this->type;
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_GENERAL,
            self::TYPE_DONATION,
            self::TYPE_PROJECT
        ];
    }
}
