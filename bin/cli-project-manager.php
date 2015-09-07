<?php
use Goteo\Model\Project,
    Goteo\Application\Config,
    Goteo\Application\Lang,
    Goteo\Model\Invest,
    Goteo\Model\User\Pool;

if (PHP_SAPI !== 'cli') {
    die('Console access only!');
}

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors", 1);

//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/app/');

require_once __DIR__ . '/../src/autoload.php';

echo "This script gets active projects and process rounds\n";

// Config file...
Config::loadFromYaml('settings.yml');

define('HTTPS_ON', Config::get('ssl') ? true : false); // para las url de project/media
$url = Config::get('url.main');
define('SITE_URL', (Config::get('ssl') ? 'https://' : 'http://') . preg_replace('|^(https?:)?//|i','',$url));
define('SEC_URL', SITE_URL);
// set Lang
Lang::setDefault(Config::get('lang'));
Lang::set(Config::get('lang'));


if (empty($argv[1])) {
    echo help();
    exit(1);
}

$project_id = $argv[1];
$order = $argv[2];
$value = $argv[3];

echo "Processing project: $project_id\n";

$project = Project::get($project_id);
$p_status = Project::status();
$i_status = Invest::status();
$status = $p_status[$project->status];
echo "NAME: {$project->name}\n";
echo "      {$project->subtitle}\n";
echo "STATUS: {$status}\n";
echo "MONEY: {$project->mincost}€ \t {$project->maxcost}€\n";
echo "RAISED: {$project->amount}€ \t {$project->num_investors} investors\n";
echo "DATES: Published {$project->published} \t Success {$project->success} \t Closed {$project->closed} \t Passed {$project->passed}\n";


if($order === '--archive') {
    // fail project
    if ($project->fail($errors)) {
        echo "Project set to failed status\n";
    } else {
        echo "ERROR::" . implode(',', $errors);
    }
}
elseif($order === '--pool') {
    // set invests to pool
    foreach($invests = Invest::getList(['projects' => $project->id, 'status' => [Invest::STATUS_PENDING, Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_RETURNED]], null, 0, 9999) as $invest) {
        echo $invest->id . " ". str_pad($invest->amount,3) . "\t STATUS: " . str_pad($i_status[$invest->status],18) ." \t METHOD: " . str_pad($invest->method, 8). " \t POOL: ".  ((int)$invest->pool) . "\tUSER: {$invest->user}\n";
        if(isset($value)) {
            $invest->setPool($value);
            echo "Pool set to " . ((int) $value) . " ";
            $errors = array();
            if((bool)$value) {
                //increment credit
                Pool::add($invest, $errors);
                if($errors) {
                    echo "ERRORS: " . implode("\n", $errors);
                    exit(1);
                }
                echo "Credit incremented by {$invest->amount}€\n";
            }
            else {
                //decrement credit
                Pool::withdraw($invest->user, $invest->amount, $errors);
                if($errors) {
                    echo "ERRORS: " . implode("\n", $errors);
                    exit(1);
                }
                echo "Credit decremented by {$invest->amount}€\n";
            }
        }
    }
    echo "Total: " . count($invests) . "\n";
}
else {
    echo "Non reconized order [$order]!\n";
    echo help();
    exit(1);
}

function help() {
    return "Please specify project and order:\n" .
           $argv[0] . " [project-id] [order]\n" .
           "Orders:\n" .
           "\t--archive\t\tarchivates a project\n" .
           "\t--pool [null|1|0]\tsets to pool all projects' invests\n";
}
