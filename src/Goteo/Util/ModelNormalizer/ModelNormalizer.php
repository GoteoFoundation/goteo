<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\ModelNormalizer;

use Goteo\Core\Model as CoreModel;
use Goteo\Model;
use Goteo\Util\ModelNormalizer\Transformer;
use Goteo\Application\Session;

/**
 * This class allows to get an object standarized for its use in views
 */
class ModelNormalizer
{
    protected $model;
    protected $keys;

    public function __construct(CoreModel $model, array $keys = null)
    {
        $this->model = $model;
        $this->keys = $keys;
    }

    /**
     * Returns the normalized object
     * @return Goteo\Util\ModelNormalizer\TransformerInterface
     */
    public function get()
    {
        if ($this->model instanceof Model\User) {
            $ob = new Transformer\UserTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Stories) {
            $ob = new Transformer\StoriesTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Node\NodeStories) {
            $ob = new Transformer\ChannelStoriesTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Node\NodeResource) {
            $ob = new Transformer\ChannelResourceTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Node\NodePost) {
            $ob = new Transformer\ChannelPostsTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Node\NodeProgram) {
            $ob = new Transformer\ChannelProgramTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Node\NodeSections) {
            $ob = new Transformer\ChannelSectionTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Node\NodeProject) {
            $ob = new Transformer\ChannelProjectTransformer($this->model, $this->keys);
        } elseif (
            $this->model instanceof Model\Category
            || $this->model instanceof Model\Sphere
            || $this->model instanceof Model\SocialCommitment
            || $this->model instanceof Model\Footprint
            || $this->model instanceof Model\Sdg
        ) {
            $ob = new Transformer\CategoriesTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Blog\Post) {
            $ob = new Transformer\PostTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Promote) {
            $ob = new Transformer\PromoteTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Filter) {
            $ob = new Transformer\FilterTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Communication) {
            $ob = new Transformer\CommunicationTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Workshop) {
            $ob = new Transformer\WorkshopTransformer($this->model, $this->keys);
        } elseif (
            $this->model instanceof Model\Mail
        ) {
            $ob = new Transformer\MailTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\ImpactData) {
            $ob = new Transformer\ImpactDataTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\ImpactData\ImpactDataProject) {
            $ob = new Transformer\ImpactDataProjectTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\ImpactItem\ImpactProjectItem) {
            $ob = new Transformer\ImpactProjectItemTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\ImpactItem\ImpactProjectItemCost) {
            $ob = new Transformer\ImpactProjectItemCostTransformer($this->model, $this->keys);
        } elseif ($this->model instanceof Model\Announcement) {
            $ob = new Transformer\AnnouncementTransformer($this->model, $this->keys);
        } else $ob = new Transformer\GenericTransformer($this->model, $this->keys);

        $ob->setUser(Session::getUser())->rebuild();

        return $ob;
    }
}
