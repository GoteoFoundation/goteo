<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms\Model;

use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Model\Blog\Post;
use Goteo\Model\Blog\Post\PostRewardAccess;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DatepickerType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\MediaType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TypeaheadType;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\Image;
use Goteo\Application\Session;
use Goteo\Library\Forms\FormModelException;
use Symfony\Component\Form\FormInterface;

class ProjectPostPrivacyForm extends AbstractFormProcessor
{
    public function createForm(): ProjectPostPrivacyForm
    {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();

        /** @var Post $post */
        $post = $this->getOption('post');

        /** @var Project $project */
        $project = $this->getOption('project');

        $builder
            ->add('reward', ChoiceType::class, [
                'required' => false,
                'label' => 'Recompensa',
                'choices' => $this->getProjectRewards(),
                'data' => $this->getProjectRewardAccess(),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'regular-save',
            ])
        ;

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false)
    {
        if (!$form) $form = $this->getBuilder()->getForm();
        if (!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $post = $this->getOption('post');
        $reward = Reward::get($data['reward']);

        if (!$reward)
            throw new FormModelException(Text::get('dashboard-project-rewards-error-reward'));

        $postRewardAccess = new PostRewardAccess();
        $postRewardAccess->post_id = $post->id;
        $postRewardAccess->reward_id = $reward->id;

        try {
            $postRewardAccess->save();
        } catch(\PDOException $e) {
            throw new FormModelException(Text::get('dashboard-project-rewards-error-duplicate'));
        }

        return $this;
    }

    private function getProjectRewards(): array
    {
        $project = $this->getOption('project');
        $rewards = $project->individual_rewards;
        $choices = [];

        foreach ($rewards as $reward) {
            $choices[$reward->reward] = $reward->id;
        }
        return $choices;
    }

    private function getProjectRewardAccess() {
        $post = $this->getOption('post');
        $rewardAccess = current(PostRewardAccess::getList(['post_id' => $post->id], 0, 1));
        if($rewardAccess) {
            return $rewardAccess->reward_id;
        }
        return null;
    }

}
