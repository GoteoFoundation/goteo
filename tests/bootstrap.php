<?php


use Goteo\Application\App;
use Goteo\Application\Config;

//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/app/');

require_once __DIR__ . '/../src/autoload.php';

// error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_DEPRECATED);
// ini_set("display_errors", 0);

//ensures we have cache to test
define('SQL_CACHE_TIME', 1);

//TODO: to be deprecate
define('HTTPS_ON', false);
define('LANG', 'es');
define('SITE_URL', 'http://localhost');

// Config file...
Config::loadFromYaml('settings.yml');


// usefull stuff
// User, Project, Node creation
function get_test_user() {
    $data = array(
        'id' => '012-simulated-user-test-210',
        'name' => 'Test user - please delete me',
        'email' => 'simulated-user-test@goteo.org'
    );
    $data['node'] = get_test_node()->id;
    // if exists, return the user
    if($user = \Goteo\Model\User::get($data['id'])) {
        return $user;
    }
    $errors = array();
    $user = new \Goteo\Model\User($data);
    if ( ! $user->save($errors, array('password')) ) {
        error_log("Error creating test user!");
        return false;
    }
    return $user;
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

function get_test_node() {
    $data = array(
        'id' => 'testnode',
        'name' => 'Test node - please delete me',
        'email' => 'simulated-node-test@goteo.org'
    );
    // if exists, return the node
    try {
        return \Goteo\Model\Node::get($data['id']);
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
    }
    $errors = array();
    $node = new \Goteo\Model\Node($data);
    if ( ! $node->save($errors, array('password')) ) {
        error_log("Error creating test node!");
        return false;
    }
    return $node;
}
function delete_test_node() {
    try {
        $project = \Goteo\Model\Node::get('testnode');
        $project->dbDelete();
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
    }

    try {
        return \Goteo\Model\Node::get('testnode');
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        error_log('unknow error deleting test project');
        return false;
    }

    return true;
}

function get_test_project() {
    $data = array(
        'id' => '012-simulated-project-test-210',
        'name' => '012 Simulated Project Test 210'
    );
    $data['node'] = get_test_node()->id;
    $data['owner'] = get_test_user()->id;
    // if exists, return the project
    try {
        return \Goteo\Model\Project::get($data['id']);
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
    }
    $errors = array();
    $project = new \Goteo\Model\Project($data);
    if( ! $project->create($data['node'], $errors)) {
        error_log("Error creating test project!");
        return false;
    }
    $project->name = $data['name'];
    if ( ! $project->save($errors) ) {
        error_log("Error saving test project!");
        return false;
    }
    if ( ! $project->rebase($data['id'], $errors) ) {
        error_log("Error rebasing test project!");
        return false;
    }
    try {
        return \Goteo\Model\Project::get($data['id']);
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        error_log('unknow error getting test project');
        return false;
    }
}

function delete_test_project() {
    try {
        $project = \Goteo\Model\Project::get('012-simulated-project-test-210');
        $project->delete();
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
    }

    try {
        return \Goteo\Model\Project::get('012-simulated-project-test-210');
    }
    catch(\Goteo\Application\Exception\ModelNotFoundException $e) {
        error_log('unknow error deleting test project');
        return false;
    }

    return true;
}

delete_test_project();
delete_test_user();
delete_test_node();
