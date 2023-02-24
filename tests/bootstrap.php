<?php


use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Currency;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Core\Model;
use Goteo\Model\Footprint;
use Goteo\Model\ImpactData;
use Goteo\Model\ImpactItem\ImpactItem;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Model\Node;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Model\User;


//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/public/');

require_once __DIR__ . '/../src/autoload.php';

App::debug(true);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_DEPRECATED);
ini_set("display_errors", 1);


// Config file...
$config = getenv('GOTEO_TEST_CONFIG_FILE');
if(!is_file($config)) $config = getenv('GOTEO_CONFIG_FILE');
if(!is_file($config)) $config = __DIR__ . '/../config/test-settings.yml';
if(!is_file($config)) $config = __DIR__ . '/../config/settings.yml';
Config::load($config);
//TODO: to be deprecate
define('HTTPS_ON', false);
define('LANG', Config::get('lang'));
define('SITE_URL', Config::getMainUrl());

Config::set('db.cache.time', 1);
Model::factory();
// TODO: mock service container logger...


// //SQL cleaning
// foreach(file(__DIR__ . '/sql_cleaner.sql') as $sql) {
//     $sql = trim($sql);
//     echo "SQL Cleaning: $sql\n";
//     \Goteo\Core\Model::query($sql);
// }
delete_test_reward();
delete_test_project();
delete_test_invest();
delete_test_matcher();
delete_test_user();
delete_test_node();

// usefull stuff
// User, Project, Node creation
function get_test_node() {
    $data = array(
        'id' => 'testnode',
        'name' => 'Test node - please delete me',
        'email' => 'simulated-node-test@goteo.org',
        'url' => ''
    );
    // if exists, return the node
    try {
        return Node::get($data['id']);
    }
    catch(ModelNotFoundException $e) {
    }
    $errors = array();
    $node = new Node($data);
    if( ! $node->create($errors)) {
        error_log("Error creating test node! " . print_r($errors, 1));
        return false;
    }
    try {
        return Node::get($data['id']);
    }
    catch(ModelNotFoundException $e) {
        error_log("unknow error getting test node! " . $e->getMessage());
    }
    return false;
}
function delete_test_node() {
    try {
        $node= Node::get('testnode');
        $node->dbDelete();
    }
    catch(ModelNotFoundException $e) {
    }
    catch(\PDOException $e) {
        error_log('PDOException on deleting test node! ' . $e->getMessage());
    }

    try {
        Node::get('testnode');
    }
    catch(ModelNotFoundException $e) {
        return true;
    }

    return false;
}

function get_test_user(): ?User
{
    $data = array(
        'userid' => '012-simulated-user-test-210',
        'name' => 'Test user - please delete me',
        'email' => 'simulated-user-test@goteo.org'
    );
    $data['node'] = get_test_node()->id;

    if($user = User::get($data['userid'])) {
        return $user;
    }
    $errors = array();
    $user = new User($data);
    if ( ! $user->save($errors, array('password')) ) {
        error_log("Error creating test user! " . print_r($errors, 1));
    }

    if($user = User::get($data['userid'])) {
        return $user;
    } else {
        error_log('Unknown error getting user id');
    }

    return null;
}

function delete_test_user() {
    if($user = User::get('012-simulated-user-test-210')) {
        try {
            $user->dbDelete();
        } catch (ModelNotFoundException $e) {
            error_log($e->getMessage());
        }

        if(User::get($user->id)) {
            error_log("Error deleting test user!");
            return false;
        }
    }
    return true;
}
function get_test_project() {
    $data = array(
        'id' => '012-simulated-project-test-210',
        'name' => '012 Simulated Project Test 210',
        'node' => get_test_node()->id,
        'owner' => get_test_user()->id
    );
    // if exists, return the project
    try {
        return Project::get($data['id']);
    }
    catch(ModelNotFoundException $e) {
//        error_log('Project [' . $data['id'] . '] not found, creating...');
    }
    catch(\PDOException $e) {
        error_log('PDOException on deleting test project! ' . $e->getMessage());
    }

    $errors = array();
    $project = new Project($data);
    if( ! $project->create($data, get_test_node()->id, $errors)) {
        error_log("Error creating test project! " . print_r($errors, 1));
        return false;
    }
    $project->name = $data['name'];
    if ( ! $project->save($errors) ) {
        error_log("Error saving test project! " . print_r($errors, 1));
        return false;
    }
    if ( ! $project->rebase($data['id'], $errors) ) {
        error_log("Error rebasing test project! " . print_r($errors, 1));
        return false;
    }

    try {
        return Project::get($data['id']);
    }
    catch(ModelNotFoundException $e) {
        error_log('unknow error getting test project ' . $e->getMessage());
        return false;
    }
}

function delete_test_project() {
    try {
        $project = Project::get('012-simulated-project-test-210');
        $project->dbDelete();
    }
    catch(ModelNotFoundException $e) {
//        error_log('Already deleted test project');
    }

    try {
        Project::get('012-simulated-project-test-210');
        error_log('unknow error deleting test project');
    }
    catch(ModelNotFoundException $e) {
        return true;
    }

    return false;
}

function get_test_invest() {
    $data = array(
        'user' => get_test_user()->id,
        'amount' => 20,
        'method' => 'cash',
        'currency' => Currency::current(),
        'currency_rate' => 1,
        'status' => 1
    );

    $errors = array();
    $invest = new Invest($data);
    try {
        if ( ! $invest->dbInsertUpdate(['user', 'amount', 'method', 'currency', 'currency_rate', 'status'], $errors) ) {
            error_log("Error saving invest! " . print_r($errors, 1));
            return false;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }

    try {
        return Invest::get($invest->id);
    }
    catch(ModelNotFoundException $e) {
        error_log('unknown error getting test invest ' . $e->getMessage());
        return false;
    }
}

function delete_test_invest(): bool
{
    $invests = Invest::getList(['users' => get_test_user()->id ]);

    if (empty($invests)) {
        return true;
    }

    $ok = true;
    foreach ($invests as $invest) {
        $ok = $ok && $invest->dbDelete();
    }

    return $ok;
}

function get_test_matcher(): Matcher
{
    $data = [
        'id' => 'test',
        'name' => 'Test Matcher',
        'owner' => get_test_user()->id,
    ];

    $matcher = new Matcher($data);
    try {
        if ( ! $matcher->dbInsert(['id', 'name', 'owner']) ) {
            error_log("Error saving Matcher! ");
            return false;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }

    try {
        return Matcher::get($matcher->id);
    }
    catch(ModelNotFoundException $e) {
        error_log('unknown error getting test matcher ' . $e->getMessage());
        return false;
    }
}

function delete_test_matcher(): bool
{
    $matcher = Matcher::get('test');

    if (empty($matcher)) {
        return true;
    }

    return $matcher->dbDelete();
}

function get_test_reward(): ?Reward
{
    $data = [
        'id' => 1,
        'name' => 'Reward Test',
        'project' => get_test_project()->id
    ];
    $reward = new Reward($data);

    try {
        if ( ! $reward->dbInsert(['id', 'name', 'project']) ) {
            error_log("Error saving reward!");
            return null;
        }
    } catch (\PDOException $e) {
        error_log($e->getMessage());
    }

    try {
        return Reward::get($data['id']);
    } catch(\ModelException $e) {
        error_log('unknown error getting test reward ' . $e->getMessage());
        return null;
    }
}

function delete_test_reward(): bool
{
    $reward = Reward::get(1);

    if (empty($reward)) return true;

    return $reward->dbDelete();
}

function get_test_footprint(): ?Footprint
{
    $data = [
        'id' => 1,
        'name' => 'test Footprint',
        'icon' => '',
        'title' => 'test title',
        'description' => 'test description'
    ];

    $footprint = new Footprint($data);

    try {
        if ( ! $footprint->dbInsert(['id', 'name', 'icon', 'title', 'description']) ) {
            error_log("Error saving footprint!");
            return null;
        }
    } catch (\PDOException $e) {
        error_log($e->getMessage());
    }

    try {
        return Footprint::get($data['id']);
    } catch(\ModelException $e) {
        error_log('unknown error getting test footprint ' . $e->getMessage());
        return null;
    }
}

function delete_test_footprint(): bool
{
    $footprint = Footprint::get(1);

    if (empty($footprint)) return true;

    return $footprint->dbDelete();
}

function get_test_impact_data(): ?ImpactData
{
    $data = [
        'id' => 1,
        'title' => 'Test post',
	    'data' => 'Test data',
        'data_unit' => 'Test unit',
    	'description' => 'Test description'
    ];

    $impactData = new ImpactData($data);

    try {
        $errors = [];
        if ( ! $impactData->dbInsert(['id', 'title', 'data', 'data_nit', 'description']) ) {
            error_log("Error saving Impact Data! ");
            return null;
        }
    } catch (\PDOException $e) {
        error_log($e->getMessage());
    }

    try {
        return ImpactData::get($data['id']);
    } catch(ModelNotFoundException $e) {
        error_log('unknown error getting test Impact Data ' . $e->getMessage());
        return null;
    }
}

function delete_test_impact_data(): bool
{
    $impactData = ImpactData::get(1);

    if (empty($impactData)) return true;

    return $impactData->dbDelete();
}

function get_test_impact_item(): ?ImpactItem
{
    $data = [
        'id' => 1,
        'name' => 'Test impact item name',
        'description' => 'Test impact item description',
        'unit' => 'Test unit',
    ];

    $impactItem = new ImpactItem();
    $impactItem
        ->setId($data['id'])
        ->setName($data['name'])
        ->setDescription($data['description'])
        ->setUnit($data['unit']);

    try {
        $errors = [];
        $impactItem->save($errors);
    } catch(ModelException $e) {
        error_log('unknown error getting test Impact Item ' . $e->getMessage());
        return null;
    }

    return $impactItem;
}

function delete_test_impact_item(): bool
{

    try {
        $impactItem = ImpactItem::getById(1);
    } catch (ModelNotFoundException $e) {
        return true;
    }

    try {
        $impactItem->dbDelete();
    } catch (ModelException $e) {
        return false;
    }

    return true;
}
