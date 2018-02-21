<?php


use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Core\Model;


//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/public/');

require_once __DIR__ . '/../src/autoload.php';

App::debug(true);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_DEPRECATED);
ini_set("display_errors", 1);

//TODO: to be deprecate
define('HTTPS_ON', false);
define('LANG', 'es');
define('SITE_URL', 'http://localhost');

// Config file...
$config = getenv('GOTEO_TEST_CONFIG_FILE');
if(!is_file($config)) $config = getenv('GOTEO_CONFIG_FILE');
if(!is_file($config)) $config = __DIR__ . '/../config/test-settings.yml';
if(!is_file($config)) $config = __DIR__ . '/../config/settings.yml';
Config::load($config);
Config::set('db.cache.time', 1);
Model::factory();
// TODO: mock service container logger...


// //SQL cleaning
// foreach(file(__DIR__ . '/sql_cleaner.sql') as $sql) {
//     $sql = trim($sql);
//     echo "SQL Cleaning: $sql\n";
//     \Goteo\Core\Model::query($sql);
// }
delete_test_project();
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
        return \Goteo\Model\Node::get($data['id']);
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
    }
    $errors = array();
    $node = new \Goteo\Model\Node($data);
    if( ! $node->create($errors)) {
        error_log("Error creating test node! " . print_r($errors, 1));
        return false;
    }
    try {
        return \Goteo\Model\Node::get($data['id']);
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        error_log("unknow error getting test node! " . $e->getMessage());
    }
    return false;
}
function delete_test_node() {
    try {
        $node= \Goteo\Model\Node::get('testnode');
        $node->dbDelete();
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
    }

    try {
        \Goteo\Model\Node::get('testnode');
        error_log('unknow error deleting test project! ' . print_r($errors, 1));
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        return true;
    }

    return false;
}

function get_test_user() {
    $data = array(
        'userid' => '012-simulated-user-test-210',
        'name' => 'Test user - please delete me',
        'email' => 'simulated-user-test@goteo.org'
    );
    $data['node'] = get_test_node()->id;
    // if exists, return the user
    if($user = \Goteo\Model\User::get($data['userid'])) {
        return $user;
    }
    $errors = array();
    $user = new \Goteo\Model\User($data);
    if ( ! $user->save($errors, array('password')) ) {
        error_log("Error creating test user! " . print_r($errors, 1));
        return false;
    }

    if($user = \Goteo\Model\User::get($data['userid'])) {
        return $user;
    }
    else {
        error_log('Unknow error getting user id');
    }
}

function delete_test_user() {
    if($user = \Goteo\Model\User::get('012-simulated-user-test-210')) {
        $user->dbDelete();
        if(\Goteo\Model\User::get($user->id)) {
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
        return \Goteo\Model\Project::get($data['id']);
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        error_log('Project [' . $data['id'] . '] not found, creating...');
    }
    $errors = array();
    $project = new \Goteo\Model\Project($data);
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
        return \Goteo\Model\Project::get($data['id']);
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        error_log('unknow error getting test project ' . $e->getMessage());
        return false;
    }
}

function delete_test_project() {
    try {
        $project = \Goteo\Model\Project::get('012-simulated-project-test-210');
        $project->dbDelete();
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        error_log('Already deleted test project');
    }

    try {
        \Goteo\Model\Project::get('012-simulated-project-test-210');
        error_log('unknow error deleting test project');
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        return true;
    }

    return false;
}
