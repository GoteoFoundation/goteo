<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Goteo\Model\Project;
use Goteo\Model\User\Pool;
use Goteo\Model\Invest;
use Goteo\Model\Post;

/**
 * Userful tools for check & repair several database potential issues
 */
class ToolkitCommand extends AbstractCommand {

    protected function configure()
    {
        // Old command, old notice hidding
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

        $this->setName("toolkit")
             ->setDescription("Check & Repair toolkit for database common issues")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the repair action, read-only operation otherwise'),
                      new InputOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project ID. Does the operations on the specified project'),
                      new InputArgument('scope', InputArgument::REQUIRED, 'Operation scope: [project|projectid|poolstatus|poolamount|investstatus]'),
                ))
             ->setHelp(<<<EOT
This script checks & repairs several database issues

Usage:

Check project amounts issues
<info>./console toolkit project</info>

Repair project amounts issues
<info>./console toolkit project --update</info>

Check project ID issues
<info>./console toolkit projectid</info>

Repair project ID issues
<info>./console toolkit projectid --update</info>

Check/fix project unreturned invests
<info>./console toolkit unreturned</info>

Check/fix pool amount issues
<info>./console toolkit poolamount [--update]</info>

Check/fix pool invests statuses issues
<info>./console toolkit poolstatus [--update]</info>

Check/fix normal invests statuses issues
<info>./console toolkit investstatus [--update]</info>

Check/fix number of comments in blogs
<info>./console toolkit comments [--update]</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scope = $input->getArgument('scope');
        $update = $input->getOption('update');
        $project = $input->getOption('project');

        $index = $fixes = 0;

        $sql_failed_projects = "SELECT id FROM project WHERE status NOT IN (" . Project::STATUS_IN_CAMPAIGN . ',' . Project::STATUS_FUNDED. ',' . Project::STATUS_FULFILLED . ")";
        $sql_funded_projects = "SELECT id FROM project WHERE status IN (" . Project::STATUS_FUNDED. ',' . Project::STATUS_FULFILLED . ")";

        $values = [];
        if($project) {
            $add_sql = " AND project.id = :project";
            $values = [':project' => $project];
        }

        if($scope === 'project') {
            $output->writeln("Checking project amounts in projects currently in campaign");
            $output->writeln("Checking project costs...");

            $sql = "SELECT
                id,
                name,
                status,
                mincost AS oldmincost,
                maxcost AS oldmaxcost,
                (SELECT  SUM(amount) FROM cost WHERE project = project.id AND required = 1) as `mincost`,
                (SELECT  SUM(amount) FROM cost WHERE project = project.id) as `maxcost`
                FROM project WHERE";

            if($project) $sql .= ' id = :project';
            else $sql .= ' status IN (' . Project::STATUS_IN_CAMPAIGN . ')';

            $sql .= ' HAVING (mincost != oldmincost OR maxcost != oldmaxcost)';
            $query = Project::query($sql, $values);
            foreach($query->fetchAll(\PDO::FETCH_CLASS) as $prj) {
                $output->writeln("Found project <info>{$prj->name}</info> with ID <info>{$prj->id}</info> having costs mismatch:");
                $t = "\tMincost (expected / currently): ";
                $v = "{$prj->mincost} / {$prj->oldmincost}";
                $t .= ($prj->mincost != $prj->oldmincost) ? "<error>$v</error>" : "<comment>$v</comment>";
                $t .= "\tMaxcost (expected / currently): ";
                $v = "{$prj->maxcost} / {$prj->oldmaxcost}";
                $t .= ($prj->maxcost != $prj->oldmaxcost) ? "<error>$v</error>" : "<comment>$v</comment>";
                $output->writeln($t);

                if($update) {
                    if(Project::query("UPDATE project SET mincost = :mincost, maxcost = :maxcost WHERE id = :id",
                        [':id' => $prj->id, ':mincost' => $prj->mincost, ':maxcost' => $prj->maxcost])) {
                        $fixes++;
                    }
                }

                $index++;

            }

            $output->writeln("Checking project invested amounts");
            $values = [':s0' => Invest::STATUS_PENDING,
                    ':s1' => Invest::STATUS_CHARGED,
                    ':s3' => Invest::STATUS_PAID,
                    ':s4' => Invest::STATUS_RETURNED,
                    ':s5' => Invest::STATUS_TO_POOL];

            $sql = "SELECT id,status,amount,amount_users,amount_call,
            (SELECT IFNULL(SUM(invest.amount), 0) FROM invest WHERE invest.project=project.id AND invest.status IN (:s0, :s1, :s3, :s4, :s5)) AS invest_total,
            (SELECT IFNULL(SUM(invest.amount), 0) FROM invest WHERE invest.project=project.id AND invest.status IN (:s0, :s1, :s3, :s4, :s5) AND method = 'drop') AS invest_drop,
            (SELECT IFNULL(SUM(invest.amount), 0) FROM invest WHERE invest.project=project.id AND invest.status IN (:s0, :s1, :s3, :s4, :s5) AND method != 'drop') AS invest_user
            FROM project
            WHERE";
            if($project) {
                $sql .= ' id = :project';
                $values[':project'] = $project;
            } else {
                $sql .= ' status IN (' . Project::STATUS_IN_CAMPAIGN . ')';
            }
            $sql .= ' HAVING (amount != invest_total OR amount_users != invest_user OR amount_call != invest_drop)';
            $query = Project::query($sql, $values);
            foreach($query->fetchAll(\PDO::FETCH_CLASS) as $prj) {
                $output->writeln("Found project <info>{$prj->name}</info> with ID <info>{$prj->id}</info> having amounts mismatch:");
                $t = "\tTotal amount (expected / currently): ";
                $v = (int) $prj->invest_total . ' / ' . (int)$prj->amount;
                $t .= ($prj->amount != $prj->invest_total) ? "<error>$v</error>" : "<comment>$v</comment>";
                $t .= "\tUsers amount (expected / currently): ";
                $v = (int) $prj->invest_user . ' / ' . (int)$prj->amount_users;
                $t .= ($prj->amount_users != $prj->invest_user) ? "<error>$v</error>" : "<comment>$v</comment>";
                $t .= "\tDrop amount (expected / currently): ";
                $v = (int) $prj->invest_drop . ' / ' . (int)$prj->amount_call;
                $t .= ($prj->amount_call != $prj->invest_drop) ? "<error>$v</error>" : "<comment>$v</comment>";

                $output->writeln($t);

                if($update) {
                    if(Project::query("UPDATE project SET amount = :amount, amount_users = :amount_users, amount_call = :amount_call WHERE id = :id",
                        [':id' => $prj->id, ':amount' => (int)$prj->invest_total, ':amount_users' => (int)$prj->invest_user, ':amount_call' => (int)$prj->invest_drop])) {
                        $fixes++;
                    }
                }
                $index++;
            }
        }
        elseif($scope === 'projectid') {
            $output->writeln("Checking project ID's...");
            $sql = "SELECT * FROM project WHERE id REGEXP '[0-9a-f]{32}' AND status>1$add_sql";
            $query = Project::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Project') as $prj) {
                $output->writeln("Found project <error>{$prj->name}</error> with ID <error>{$prj->id}</error> and status <error>{$prj->status}</error>");
                $newid = Project::checkId(Project::idealiza($prj->name));
                $output->writeln("<comment>ID will be changed to [$newid]</comment>");
                if($update) {
                    $prj->rebase($newid);
                    $fixes++;
                }
                $index++;
            }
        }
        elseif($scope == 'unreturned') {
            $output->writeln("Checking failed project with unreturned invests...");

            $sql = "SELECT count(invest.id) as count,SUM(invest.amount) as amount,invest.project,
            GROUP_CONCAT(DISTINCT invest.method SEPARATOR ', ') AS methods,
            MIN(invest.invested) AS min_date,
            MAX(invest.invested) AS max_date,
            project.status AS projectStatus
            FROM invest
            INNER JOIN project ON project.id=invest.project
            WHERE invest.status IN (" . Invest::STATUS_PAID .','. Invest::STATUS_CHARGED . ") AND invest.project IN ($sql_failed_projects$add_sql) GROUP BY invest.project";
            $subquery = Invest::query($sql, $values);
            foreach($subquery->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest') as $invest) {
                $project = $invest->project;
                $output->writeln("Failed project: <info>$project</info> Project Status: <info>{$invest->projectStatus}</info> Num of Invests: <info>{$invest->count}</info> Total Amount: <comment>{$invest->amount}</comment> Methods: <info>{$invest->methods}</info> Date range: <info>[{$invest->min_date} - {$invest->max_date}]</info>");
                $command = "<comment>".GOTEO_PATH . "bin/console refund -p {$invest->project}</comment> (with options -auf if needed)";
                if($update) {
                    $output->writeln("<error>NON REPAIRABLE</error> please run the command: $command");
                    $fixes++;
                } else {
                    $output->writeln("Command to refund: $command");
                }
                $index++;
            }
        }
        elseif($scope == 'poolamount') {
            $output->writeln("Checking pool amounts...");

            $returned_status = Invest::STATUS_CANCELLED .','. Invest::STATUS_RETURNED .','. Invest::STATUS_TO_POOL;
            $paid_status = Invest::STATUS_PAID .','. Invest::STATUS_CHARGED;


            $sql_total_to_pool = "SELECT SUM(amount) FROM invest i1 WHERE i1.user=u.id
                # AND status IN ($returned_status)
                AND status > 0
                AND pool=1
                AND (project IN ($sql_failed_projects) OR ISNULL(project) OR status=".Invest::STATUS_TO_POOL.")
                AND method!='pool'";

            $sql_total_from_pool = "SELECT SUM(amount) FROM invest i2 WHERE i2.user=u.id
                AND status IN ($paid_status)
                AND method='pool'";

            $sql = "SELECT DISTINCT u.id as user, p.amount,
                ($sql_total_to_pool) as total_to_pool,
                ($sql_total_from_pool) as total_from_pool
             FROM user u
             LEFT JOIN user_pool p ON p.user=u.id
             ";
             // echo $sql;
            $query = Pool::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\User\Pool') as $pool) {
                $amount = (int)$pool->amount;
                $total_to_pool = (int)$pool->total_to_pool;
                $total_from_pool = (int)$pool->total_from_pool;
                $diff = $total_to_pool - $total_from_pool;
                if($amount == 0 && $total_to_pool == 0 && $total_from_pool == 0) {
                    continue;
                }
                // pool inconsistences
                if($amount != $diff) {
                    $output->write("USER <info>{$pool->user}</info> TOTAL TO POOL: <info>{$total_to_pool}</info> TOTAL FROM POOL: <info>{$total_from_pool}</info> DIFF: <error>{$diff}</error> AMOUNT: <comment>{$amount}</comment> ");
                    if($update) {
                        $output->writeln("<comment>Pool amount changed to $diff</comment>");
                        $errors = [];
                        if(!$pool->calculate()->save($errors)) {
                            throw new \Exception(implode('\n', $errors));
                        }

                        $fixes++;
                    } else {
                        $output->write("<error>Amount should be $diff");
                        if($diff < 0) {
                            $output->write(" can be corrected to 0 only, loosing money here");
                        }
                        $output->writeln("</error>");
                    }
                    $index++;
                }
            }
        }
        elseif($scope == 'poolstatus') {
            $output->writeln("Checking pool statuses related to project statuses...");
            $output->writeln("<comment>NOTE: Not all issues found here are necessarily problems. Use with caution.</comment>");
            if($project) {
                $sqladd = ' AND project=:project';
            } else {
                $values = [];
            }

            $sql = "SELECT * FROM invest WHERE status>0 AND pool=1 AND (project IN ($sql_failed_projects) OR ISNULL(project))$sqladd";
            // die(\sqldbg($sql, $values));
            $subquery = Invest::query($sql, $values);
            foreach($subquery->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest') as $invest) {
                $project = $invest->project;
                if(empty($project)) $project = 'POOL-PAYMENT';
                $info = "User: <info>{$invest->user}</info> Failed project: <info>$project</info> Invest: {$invest->id} Amount: <comment>{$invest->amount}</comment> Method: <comment>{$invest->method}</comment> Status: <comment>{$invest->status}</comment> ";
                if($invest->status != Invest::STATUS_TO_POOL) {
                    $output->write($info);
                    if($update) {
                        $output->writeln("<comment>Status changed to " . Invest::STATUS_TO_POOL . "</comment>");
                        $invest->setStatus(Invest::STATUS_TO_POOL);
                        $fixes++;
                    } else {
                        $output->writeln("<error>Status should be " . Invest::STATUS_TO_POOL . "</error>");
                    }
                    $index++;
                } else {
                    if($output->isVerbose()) {
                        $output->write($info);
                        $output->writeln("<info>OK</info>");
                    }
                }
            }

            $output->writeln("Checking pool statuses related to invests statuses...");
            $sql = "SELECT * FROM invest WHERE status=" . Invest::STATUS_TO_POOL. " AND (project NOT IN ($sql_failed_projects) OR ISNULL(project))$sqladd";

            $subquery = Invest::query($sql, $values);
            foreach($subquery->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest') as $invest) {
                if(!$invest->getProject()) {
                    continue;
                }
                $output->write("User: <info>{$invest->user}</info> Active project: <info>{$invest->project}</info> Invest: {$invest->id} Amount: <comment>{$invest->amount}</comment> Method: <comment>{$invest->method}</comment> Status: <comment>{$invest->status}</comment> ");

                $status = Invest::STATUS_CHARGED;
                if($invest->getProject()->status == Project::STATUS_FUNDED) $status = Invest::STATUS_PAID;

                if($update) {
                    $output->writeln("<comment>Status changed to $status</comment>");
                    $invest->setStatus($status);
                    $fixes++;
                } else {
                    $output->writeln("<error>Status should be $status</error>");
                }
                $index++;
            }

            $output->writeln("Checking pool statuses for invest-to-pool...");
            $sql = "SELECT * FROM invest WHERE status IN (" . Invest::STATUS_CHARGED . ',' . Invest::STATUS_PAID . ',' . Invest::STATUS_TO_POOL. ") AND ISNULL(project)";
            $subquery = Invest::query($sql);
            foreach($subquery->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest') as $invest) {
                if($invest->isOnPool() && $invest->pool) continue;
                $output->write("User: <info>{$invest->user}</info> Active project: <info>{$invest->project}</info> Invest: {$invest->id} Amount: <comment>{$invest->amount}</comment> Method: <comment>{$invest->method}</comment> Status: <comment>{$invest->status}</comment> ");

                $status = Invest::STATUS_TO_POOL;

                if($update) {
                    $output->writeln("<comment>Status changed to $status, pool changted to true</comment>");
                    $invest->setPoolOnFail(true);
                    $invest->setStatus($status);
                    $fixes++;
                } else {
                    $output->writeln("<error>Status should be $status, pool property should be true</error>");
                }
                $index++;
            }
        }
        elseif($scope == 'investstatus') {
            $output->writeln("Checking normal invests statuses related to project statuses...");
            // no direct-to-pool invests
            $sql = "SELECT * FROM invest WHERE status=" . Invest::STATUS_CHARGED . " AND project IN ($sql_funded_projects$add_sql)";
            $query = Invest::query($sql, $values);
            foreach($query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest') as $invest) {
                $output->write("Invest: {$invest->id} Method: <info>{$invest->method}</info> Status: <comment>{$invest->status}</comment> Date {$invest->invested} ");
                $status = Invest::STATUS_PAID;
                if($update) {
                    $output->writeln("<comment>Status changed to $status</comment>");
                    $invest->setStatus($status);
                    $fixes++;

                } else {
                    $output->writeln("<error>Status should be $status</error>");
                }
                $index++;
            }
        }
        elseif($scope == 'comments') {
            $output->writeln("Checking number of comments calculated");
            $sql = "SELECT post.id,post.date,post.title,post.num_comments,COUNT(comment.id) AS real_num
                    FROM post JOIN comment ON comment.post=post.id
                    GROUP BY post.id
                    HAVING real_num!=post.num_comments";
                        $query = Post::query($sql);
            foreach($query->fetchAll(\PDO::FETCH_OBJ) as $post) {
                $output->write("Post {$post->id} has <comment>{$post->real_num}</comment> of comments but shows <error>{$post->num_comments}</error>");
                $index++;
                if($update) {
                    Post::query("UPDATE post SET num_comments=:num WHERE id=:id", [':num' => $post->real_num, ':id' => $post->id]);
                    $fixes++;
                }
                $output->writeln("");
            }

        }
        else {
            throw new \Exception("Scope [$scope] not available!");
        }

        if($index == 0) {
            $output->writeln("<info>No problems found</info>");
        }
        else {
            $output->writeln("<error>Found $index problems!</error>");
            if($fixes) {
                $output->writeln("<info>Repaired $fixes projects</info>");
            } else {
                $output->writeln("<info>Execute with --update option to fix the problems</info>");
                return 1;
            }
        }
        return;
    }
}
