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
class ModelNormalizer {
    protected $model;
    protected $keys;

    public function __construct(CoreModel $model,array $keys = null) {
        $this->model = $model;
        $this->keys = $keys;
    }

    /**
     * Returns the normalized object
     * @return Goteo\Util\ModelNormalizer\TransformerInterface
     */
    public function get() {
        if($this->model instanceOf Model\User) {
            $ob = new Transformer\UserTransformer($this->model, $this->keys);
        }
        elseif($this->model instanceOf Model\Stories) {
            $ob = new Transformer\StoriesTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\Node\NodeStories) {
            $ob = new Transformer\ChannelStoriesTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\Node\NodeResource) {
            $ob = new Transformer\ChannelResourceTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\Node\NodePost) {
            $ob = new Transformer\ChannelPostsTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\Node\NodeProgram) {
            $ob = new Transformer\ChannelProgramTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\Node\NodeSections) {
            $ob = new Transformer\ChannelSectionTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceof Model\Node\NodeProject) {
            $ob = new Transformer\ChannelProjectTransformer($this->model, $this->keys);
        }
        elseif(
            $this->model instanceOf Model\Category
            || $this->model instanceOf Model\Sphere
            || $this->model instanceOf Model\SocialCommitment
            || $this->model instanceOf Model\Footprint
            || $this->model instanceOf Model\Sdg
        ) {
            $ob = new Transformer\CategoriesTransformer($this->model, $this->keys);
        }
        elseif($this->model instanceOf Model\Blog\Post) {
            $ob = new Transformer\PostTransformer($this->model, $this->keys);
        }
        elseif($this->model instanceOf Model\Promote) {
            $ob = new Transformer\PromoteTransformer($this->model, $this->keys);
        }
        elseif($this->model instanceOf Model\Filter) {
            $ob = new Transformer\FilterTransformer($this->model, $this->keys);
        }
        elseif($this->model instanceOf Model\Communication) {
            $ob = new Transformer\CommunicationTransformer($this->model, $this->keys);
        }
        elseif($this->model instanceOf Model\Workshop) {
            $ob = new Transformer\WorkshopTransformer($this->model, $this->keys);
        }
        elseif(
            $this->model instanceOf Model\Mail) {
            $ob = new Transformer\MailTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\ImpactData) {
            $ob = new Transformer\ImpactDataTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\ImpactData\ImpactDataProject) {
            $ob = new Transformer\ImpactDataProjectTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\ImpactItem\ImpactProjectItem) {
            $ob = new Transformer\ImpactProjectItemTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceOf Model\ImpactItem\ImpactProjectItemCost) {
            $ob = new Transformer\ImpactProjectItemCostTransformer($this->model, $this->keys);
        }
        elseif ($this->model instanceof Model\Page) {
            $ob = new Transformer\PageTransformer($this->model, $this->keys);
        }
        else $ob = new Transformer\GenericTransformer($this->model, $this->keys);

        $ob->setUser(Session::getUser())->rebuild();

        return $ob;
    }
}
