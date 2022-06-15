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

use DateTime;
use Goteo\Application\Config;
use Goteo\Model\Image;

class DataSet {

    const TYPE_PROJECTS = 'projects';
    const TYPE_INVESTS = 'invests';

    private ?int $id = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?string $type = null;
    private string $lang;
    private string $url;
    private ?Image $image = null;
    private string $created_at;
    private string $modified_at;

    public function __construct()
    {
        $this->lang = Config::get('lang');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): DataSet
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): DataSet
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): DataSet
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): DataSet
    {
        $this->type = $type;
        return $this;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function setLang(string $lang): DataSet
    {
        $this->lang = $lang;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): DataSet
    {
        $this->url = $url;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return date_create($this->created_at);
    }

    public function getModifiedAt(): DateTime
    {
        return date_create($this->modified_at);
    }

    public function getImage(): ?Image {
        if (!$this->image)
            return null;

        if ($this->image instanceof Image)
            return $this->image;

        return Image::get($this->image);
    }

    public function setImage(Image $image): DataSet {
        $this->image = $image;
        return $this;
    }
}
