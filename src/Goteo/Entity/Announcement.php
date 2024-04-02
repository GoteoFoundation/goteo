<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Entity;

use Goteo\Model\Project;

class Announcement {

    const TYPE_GENERAL = 'general';
    const TYPE_DONATION = 'donation';
    const TYPE_PROJECT = 'project';

    private int $id;
    private string $title;
    private string $description;
    private ?Project $project;
    private string $type = self::TYPE_GENERAL;
    private string $lang;
    private string $cta_url;
    private string $cta_text;
    private bool $active;
    private ?\DateTime $start_date;
    private ?\DateTime $end_data;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Announcement
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Announcement
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Announcement
    {
        $this->description = $description;
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): Announcement
    {
        $this->project = $project;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Announcement
    {
        $this->type = $type;
        return $this;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function setLang(string $lang): Announcement
    {
        $this->lang = $lang;
        return $this;
    }

    public function getCtaUrl(): string
    {
        return $this->cta_url;
    }

    public function setCtaUrl(string $cta_url): Announcement
    {
        $this->cta_url = $cta_url;
        return $this;
    }

    public function getCtaText(): string
    {
        return $this->cta_text;
    }

    public function setCtaText(string $cta_text): Announcement
    {
        $this->cta_text = $cta_text;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Announcement
    {
        $this->active = $active;
        return $this;
    }

    public function getStartDate(): \DateTime
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTime $start_date): Announcement
    {
        $this->start_date = $start_date;
        return $this;
    }

    public function getEndData(): \DateTime
    {
        return $this->end_data;
    }

    public function setEndData(\DateTime $end_data): Announcement
    {
        $this->end_data = $end_data;
        return $this;
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
}
