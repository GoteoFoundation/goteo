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

use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Model\Project;
use Goteo\Model\SocialCommitment;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;

class ProjectTranslateOverviewForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm() {

        $this->getBuilder()

            ;
        return $this;
    }

}
