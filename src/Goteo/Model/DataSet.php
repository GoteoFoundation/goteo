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

use DateTime;
use Goteo\Application\Config;

class DataSet {

    private int $id;
    private ?string $title = null;
    private ?string $description = null;
    private string $lang;
    private ?string $url = null;
    private ?Image $image = null;
    private ?int $created = null;
    private ?int $modified = null;

    public function __construct()
    {
        $this->lang = Config::get('lang');
    }

    public function getId(): int
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

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): DataSet
    {
        $this->created = $created;
        return $this;
    }

    public function getModified(): DateTime
    {
        return $this->modified;
    }

    public function setModified(DateTime $modified): DataSet
    {
        $this->modified = $modified;
        return $this;
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
