<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Project;
use Goteo\Model\User;
use Goteo\Model\Image;
use Goteo\Model\Project\Image as ProjectImage;

class ProjectTest extends \PHPUnit_Framework_TestCase {

    private static $related_tables = array('project_category' => 'project',
                    'project_account' => 'project',
                    'project_conf' => 'project',
                    'project_data' => 'project',
                    'project_image' => 'project',
                    'project_lang' => 'id',
                    'project_location' => 'id',
                    'project_open_tag' => 'project',
                    // 'banner' => 'project', => investigar, parece que hay banners que pueden no tener proyecto
                    'bazar' => 'project',
                    // 'blog' => 'owner', => el campo type indica la tabla del owner, se deberia cambiar
                    'call_project' => 'project',
                    'contract' => 'project',
                    'cost' => 'project',
                    // 'cost_lang' => 'project', => investigar...
                    'invest' => 'project',
                    'invest_node' => 'project_id',
                    // 'message' => 'project', => borrar con tranquilidad
                    'patron' => 'project',
                    'promote' => 'project',
                    // 'review' => 'project', => borrar con tranquilidad
                    // 'reward' => 'project', => borrar con tranquilidad
                    // 'reward_lang' => 'project', => borrar con tranquilidad
                    'stories' => 'project',
                    // 'support' => 'project', => borrar con tranquilidad
                    'user_project' => 'project',

                    );

    private static $image = array(
                        'name' => 'test.png',
                        'type' => 'image/png',
                        'tmp_name' => '',
                        'error' => '',
                        'size' => 0);

    private static $data = array('id' => '012-simulated-project-test-210', 'owner' => '012-simulated-user-test-210', 'name' => '012 Simulated Project Test 210');

    private static $user = array(
            'userid' => '012-simulated-user-test-210',
            'name' => 'Test user - please delete me',
            'email' => 'simulated-user-test@goteo.org'
        );

    private static $image2;

    public static function setUpBeforeClass() {

       //temp file
        $i = base64_decode('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABkElEQVRYhe3Wv2qDQBgA8LxJH8BXcHLN4pCgBxIOddAlSILorFDaQRzFEHEXUWyXlo6BrkmeI32Hr1PTMyb1rtpIIQff6vdTvz83unt+giFjdAP8awCXZ8Dl2XCAcRjAOAyGA8iaDrKmDwMQ4ggQUgAhBYQ4uj5AMswjQDLM6wJE3zsm/wrR964D4NOkkbzLr2AC8GkC8gxfBMgzDHya/A2AyzOQNf1i8iNC05lmAxWAy7Na0bWFZJjUCCrAdLmoJbDmFlRFCe+bDVhz6yxiulz0AyD7HSEFHu8fgDyu7XQqylbAxP1O4NoOnB6M1YuAiet0B5CF9/by2gC0FWRnAPnAj8OBCYCQ0i+A9vQKIAfPfrtrTb7f7mqDqTOAbMF1vGoFrOMVUyu2AsZhUPukP30F8u0RUqguK1SDiJyCGKtQFWUjeVWUtZakXdFUgHNLCGMVXNsB13Yas4BlKVEvIz5NqJcRy0ZkWsdcnoHoe2dXsjzDIPoe8y3511cyPk1AiCMQ4oj5DtALoK+4AQYHfALaYBdH6m2UnQAAAABJRU5ErkJggg==');
        self::$image2 = self::$image;
        self::$image['tmp_name'] = __DIR__ . '/test-tmp.png';
        self::$image2['tmp_name'] = __DIR__ . '/test-tmp2.png';
        self::$image['name'] = 'other.png';
        file_put_contents(self::$image['tmp_name'], $i);
        file_put_contents(self::$image2['tmp_name'], $i);
        self::$image['size'] = strlen($i);
        self::$image2['size'] = strlen($i);
    }

    public function testInstance() {
        \Goteo\Core\DB::cache(false);

        $ob = new Project();

        $this->assertInstanceOf('\Goteo\Model\Project', $ob);

        return $ob;
    }

    public function testCount() {

        $total = Project::dbCount();
        $campaign = Project::dbCount(array('status' => Project::STATUS_IN_CAMPAIGN));
        $funded = Project::dbCount(array('status' => Project::STATUS_FUNDED));
        $unfunded = Project::dbCount(array('status' => Project::STATUS_UNFUNDED));
        $mainnode = Project::dbCount(array('node' => 'goteo'));

        $this->assertInternalType('integer', $total);
        $this->assertInternalType('integer', $campaign);
        $this->assertInternalType('integer', $funded);
        $this->assertInternalType('integer', $unfunded);
        $this->assertInternalType('integer', $mainnode);
        $this->assertGreaterThanOrEqual($campaign, $total);
        $this->assertGreaterThanOrEqual($funded, $total);
        $this->assertGreaterThanOrEqual($unfunded, $total);
        $this->assertGreaterThanOrEqual($mainnode, $total);
        echo "Projects: [$total] In Campaign: [$campaign] Funded: [$funded]  Unfunded: [$unfunded] Node goteo: [$mainnode]\n";
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }


    public function testCreateUser() {
        // We don't care if exists or not the test user:
        if($user = \Goteo\Model\User::get(self::$user['userid'])) {
            $user->delete();
        }
        $errors = array();
        $user = new \Goteo\Model\User(self::$user);
        $this->assertTrue($user->save($errors, array('password')), print_r($errors, 1));
        $this->assertInstanceOf('\Goteo\Model\User', $user);

        //delete test project if exists
        try {
            $project = Project::get(self::$data['id']);
            $project->delete();
        } catch(\Exception $e) {
            // project not exists, ok
        }
        return $user;
    }

    /**
     * @depends testCreateUser
     */
    public function testCreateProject($user) {
        $this->assertEquals($user->id, self::$data['owner']);

        $errors = array();
        $project = new Project(self::$data);
        $this->assertTrue($project->validate($errors), print_r($errors, 1));
        $this->assertNotFalse($project->create(GOTEO_NODE, $errors), print_r($errors, 1));

        $project = Project::get($project->id);
        $this->assertInstanceOf('\Goteo\Model\Project', $project);
        $this->assertInstanceOf('\Goteo\Model\Image', $project->image);

        $this->assertEquals($project->owner, self::$user['userid']);
        return $project;
    }


    /**
     * @depends testCreateProject
     */
    public function testEditProject($project) {
        $errors = array();
        $project->name = self::$data['name'];
        //add image
        $project->image = self::$image;
        $this->assertTrue($project->save($errors), print_r($errors, 1));

        //add second image
        $project->image = self::$image2;
        $this->assertTrue($project->save());

        $project = Project::get($project->id);
        $this->assertInternalType('array', $project->all_galleries);
        $this->assertInternalType('array', $project->gallery);
        $this->assertCount(2, $project->gallery);
        $this->assertCount(7, $project->all_galleries);
        $this->assertCount(2, $project->all_galleries['']);
        $this->assertEquals($project->image, $project->gallery[0]->imageData);
        $this->assertEquals($project->owner, self::$user['userid']);
        $this->assertEquals($project->name, self::$data['name']);

    }

    /**
     * @depends testCreateProject
     */
    public function testRebaseProject($project) {
        $errors = array();

        $this->assertRegExp('/^[A-Fa-f0-9]{32}$/', $project->id, $project->id);
        $this->assertTrue($project->rebase(null, $errors), print_r($errors, 1));
        $this->assertNotRegExp('/^[A-Fa-f0-9]{32}$/', $project->id, $project->id);
        $this->assertEquals($project->id, self::$data['id']);

        $project = Project::get(self::$data['id']);
        $this->assertEquals($project->id, self::$data['id']);

        $newid = self::$data['id'] . '-2';

        $this->assertTrue($project->rebase($newid, $errors), print_r($errors, 1));
        $this->assertEquals($project->id, $newid);
        $project = Project::get($newid);
        $this->assertEquals($project->id, $newid);

        $newid = self::$data['id'];

        $this->assertTrue($project->rebase($newid, $errors), print_r($errors, 1));
        $this->assertEquals($project->id, $newid);
        $project = Project::get($newid);
        $this->assertEquals($project->id, $newid);

        return $newid;
    }

    /**
     * @depends testRebaseProject
     */
    public function testGetProject($id) {
        $errors = array();
        $project = Project::getMini($id);
        $this->assertInstanceOf('\Goteo\Model\Project', $project);
        $this->assertEquals($project->id, $id);
        $this->assertInstanceOf('\Goteo\Model\Image', $project->image);

        $project = Project::getMedium($id);
        $this->assertInstanceOf('\Goteo\Model\Project', $project);
        $this->assertEquals($project->id, $id);
        $this->assertInstanceOf('\Goteo\Model\Image', $project->image);

        $widget = Project::getWidget($project);
        $this->assertInstanceOf('\Goteo\Model\Project', $widget);
        $this->assertEquals($widget->id, $id);
        $this->assertInstanceOf('\Goteo\Model\Image', $widget->image);


        return $project;
    }
    /**
     * @depends testGetProject
     */
    public function testRemoveImageProject($project) {
        $errors = array();

        $this->assertTrue($project->image->remove($errors, 'project'), print_r($errors, 1));
        $project->all_galleries = ProjectImage::getGalleries($project->id);
        $project->gallery = $project->all_galleries[''];
        $project->image = ProjectImage::setImage($project->id, $project->gallery);

        $this->assertInternalType('array', $project->gallery);
        $this->assertCount(1, $project->gallery, print_r($project->gallery, 1));
        $this->assertEquals($project->image, $project->gallery[0]->imageData, print_r($project->image, 1));

        //remove second image
        $this->assertTrue($project->gallery[0]->imageData->remove($errors, 'project'), print_r($errors, 1));
        $project = Project::get($project->id);
        $this->assertInternalType('array', $project->gallery);
        $this->assertCount(0, $project->gallery);
        $this->assertInstanceOf('\Goteo\Model\Image', $project->image);

        //add image (to check autodelete)
        $project->image = self::$image;

        $this->assertTrue($project->validate($errors));
        $this->assertTrue($project->save());
        $project = Project::get($project->id);
        $this->assertInternalType('array', $project->gallery);
        $this->assertCount(1, $project->gallery);
        $this->assertEquals($project->image, $project->gallery[0]->imageData);
    }
    /**
     * @depends testCreateProject
     */
    public function testDeleteProject($project) {
        $errors = array();
        $this->assertTrue($project->delete($errors), print_r($errors, 1));

        return $project;
    }

    public function testNonExisting() {
        try {
            $ob = Project::get(self::$data['id']);
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Application\Exception\ModelNotFoundException', $e);
        }
        try {
            $ob = Project::get('non-existing-project');
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Application\Exception\ModelNotFoundException', $e);
        }
    }

    public function testCleanProjectRelated() {
        foreach(self::$related_tables as $tb => $field) {
            $this->assertEquals(0, Project::query("SELECT COUNT(*) FROM `$tb`  WHERE `$field` NOT IN (SELECT id FROM project)")->fetchColumn(), "DB incoherences in table [$tb], Please run SQL command:\nDELETE FROM  `$tb` WHERE `$field` NOT IN (SELECT id FROM project)");
        }
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        if($user = \Goteo\Model\User::get(self::$user['userid'])) {
            $user->delete();
        }
        // Remove temporal files on finish
        unlink(self::$image['tmp_name']);
        unlink(self::$image2['tmp_name']);
    }
}
