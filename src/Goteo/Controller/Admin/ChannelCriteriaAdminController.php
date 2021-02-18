<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Library\Text;

use Goteo\Model\Node;
use Goteo\Model\Questionnaire;
use Goteo\Model\Questionnaire\Question;
use Goteo\Model\Questionnaire\Answer;
use Goteo\Model\Project;
use Goteo\Model\Contract\BaseDocument;

class ChannelCriteriaAdminController extends AbstractAdminController
{
  protected static $icon = '<i class="fa fa-2x fa-question-circle-o"></i>';

  public static function getGroup() {
    return 'channels';
  }

  public static function getRoutes() {
    return [
      new Route(
				'/',
				['_controller' => function () {
                    return new RedirectResponse("/admin/channelcriteria/" . Config::get('node'));
                }]
      ),
      new Route(
        '/{id}',
        ['_controller' => __CLASS__ . "::listAction"]
      ),
      new Route(
        '/{id}/export',
        ['_controller' => __CLASS__ . "::exportAction"]
      )
      ];
  }

  function saveQuestionnaire($request, $form, $model)
  {
      if(!$form) { 
          $form = $this->getBuilder()->getForm();
      }

      $data = $request->request->get('autoform');
      $ids = [];
      $errors = [];
      $order = 1;

      foreach($data as $key=>$val) {
          list($id, $field) = explode('_', $key);
          if(!in_array($field, ['typeofquestion', 'required', 'question'])) continue;

          if (!$ids[$id]) {
              $ids[$id] = $id;
              $question = Question::get($id);
              $question->title = $data[$id . "_question"];
              $question->vars->type = $data[$id . "_typeofquestion"];
              $question->vars->required = $data[$id . "_required"] ? true : false;
              $question->max_score = $data[$id . "_max_score"];
              $question->order = $order++;
              if (!$question->save($errors)) {
                  throw new FormModelException(Text::get('form-sent-error', implode(', ', $errors)));
              }
          };
      }

      return $this;
  }

  public function listAction($id, Request $request) {
    try {
			$channel = Node::get($id);
		} catch (ModelNotFoundException $e) {
			Message::error($e->getMessage());
			return $this->redirect('/admin');
    }
    
    $questionnaire = Questionnaire::getByChannel($id);

    if (!$questionnaire) {
      $questionnaire = new Questionnaire();
      $questionnaire->channel = $channel->id;
      $questionnaire->lang = Config::get('lang');
      $questionnaire->save();
  }

  $processor = $this->getModelForm('QuestionnaireCreate', $questionnaire, (array) $questionnaire, [], $request);
  $processor->createForm()->getBuilder()
    ->add(
    'add-question', 'submit', [
    'label' => Text::get('questionnaire-add-question'),
    'attr' => ['class' => 'btn btn-lg btn-cyan text-uppercase add-question'],
    'icon_class' => 'fa fa-plus'
    ]);

  $form = $processor->getForm();
  $form->handleRequest($request);

  if ($form->isSubmitted() && $request->isMethod('post')) {
      if($request->isXmlHttpRequest()) {
          $button = $form->getClickedButton()->getName();
          if($button === 'add-question') {
              $question = new Question();
              $question->channel = $id;
              $question->lang = $questionnaire->lang;
              $question->questionnaire = $questionnaire->id;
              $question->order = 1;
              $question->save();

              $processor->addQuestion($question);
              return $this->viewResponse(
                  'dashboard/partials/question_item', [
                  'form' => $processor->getBuilder()->getForm()->createView(),
                  'question' => $question
                  ]
              );
          }
          if(strpos($button, '_remove')) {
              try {
                  $question = Question::get(explode('_', $button)[0]);
                  $question->dbDelete();
                  
                  return $this->rawResponse('deleted ' . $questionnaire->id);
              } catch(\PDOExpection $e) {
                  return $this->rawResponse(Text::get('form-sent-error', Text::get('question-save-error')), 'text/plain', 403);
              }
          }        
      }
          
      $this->saveQuestionnaire($request, $form, $questionnaire);
      Message::info(Text::get('admin-edit-entry-ok'));
    }

    return $this->viewResponse('admin/channelcriteria/list', [
      'current_node' => $id,
      'nodes' => $this->user->getNodeNames(),
      'form' => $form->createView(),
      'questionnaire' => $questionnaire
    ]);
  }

  public function exportAction($id, Request $request) {

    try {
			$channel = Node::get($id);
		} catch (ModelNotFoundException $e) {
			Message::error($e->getMessage());
			return $this->redirect('/admin/channelcriteria');
    }

    $questionnaire = Questionnaire::getByChannel($id);
    $questions = $questionnaire->questions;
    
    $total = Project::getList(['node' => $id], $cid, 0, 0, true);
    $projects = Project::getList(['node' => $id], $cid, 0, $total);

    $response = new StreamedResponse(function () use ($questions, $projects) {
      $buffer = fopen('php://output', 'w');

      $header = ['ID'];

      foreach ($questions as $question) {
        array_push($header, $question->id . ' - ' . $question->title);
      }

      fputcsv($buffer, $header);
      flush();
      fclose($buffer);
      $offset = 0;

      foreach ($projects as $project) {
        $answers = Answer::getList(['questionnaire' => $questionnaire->id, 'project' => $project->id]);
        if (empty($answers))
          continue;

        $project_answers = [$project->id];

        foreach ($answers as $index => $answer) {
          if ($questions[$index]->vars->type == "dropfiles") {
            $document = BaseDocument::getByName($answer->answer);
            array_push($project_answers, Config::get('url.main') . $document->getLink());
          } else {
            array_push($project_answers, $answer->answer); 
          }
        }

        $buffer = fopen('php://output', 'w');
        fputcsv($buffer, $project_answers);
        flush();
        fclose($buffer);
      }
    });

    $d = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			'channel_' . $id .'_answers_.csv'
		);

		$response->headers->set('Content-Disposition', $d);
		$response->headers->set('Content-Type', 'text/csv');

		return $response;
  }


}