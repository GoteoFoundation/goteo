<?php

namespace Goteo\Command;

use Goteo\Model;
use Goteo\Application\Config;

class DbVerifier {

    public static function process ($debug = false) {

        // eliminamos ACL innecesario
        $sql = "DELETE FROM `acl`
            WHERE id > 1000
            AND role_id = 'user'
            AND user_id != '*'
            AND (url LIKE '%project/edit%'  OR url LIKE '%project/delete%')
            AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`timestamp`)), '%j') > 30
            ";

        // echo $sql . '\n';
        $query = Model\Project::query($sql);
        $count = $query->rowCount();
        if ($debug) echo "Eliminados $count registros de ACL antiguo.\n";

        // eliminamos feed antiguo
        $sql1 = "DELETE
            FROM `feed`
            WHERE type != 'goteo'
            AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`datetime`)), '%j') > 30
            AND (url NOT LIKE '%updates%' OR url IS NULL)
            ";

        // echo $sql . '\n';
        $query1 = Model\Project::query($sql1);
        $count1 = $query1->rowCount();
        if ($debug) echo "Eliminados $count1 registros de feed.\n";

        // eliminamos mail antiguo
        $sql2 = "DELETE
            FROM `mail`
            WHERE (template != 33 OR template IS NULL)
            AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`date`)), '%j') > 30
            ";

        // echo $sql2 . '\n';
        $query2 = Model\Project::query($sql2);
        $count2 = $query2->rowCount();
        if ($debug) echo "Eliminados $count2 registros de mail.\n";

        // eliminamos log antiguo
        $sql3 = "DELETE
            FROM `log`
            WHERE DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`datetime`)), '%j') > 10
            ";

        // echo $sql . '\n';
        $query3 = Model\Project::query($sql3);
        $count3 = $query1->rowCount();
        if ($debug) echo "Eliminados $count3 registros de log.\n";

        // busco aportes incompletos con codigo de autorización
        $sql5 = "SELECT * FROM invest WHERE status = -1 AND transaction IS NOT NULL";
        $query5 = Model\Project::query($sql5);
        foreach ($query5->fetchAll(\PDO::FETCH_OBJ) as $row) {
            @mail(Config::getMail('fail'),
                'Aporte Incompleto con numero de autorización. En ' . SITE_URL,
                'Aporte Incompleto con numero de autorización: <pre>' . print_r($row, true). '</pre>');
        }


        // eliminamos aportes incompletos
        /*
        $sql4 = "DELETE
            FROM `invest`
            WHERE status = -1
            AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`datetime`)), '%j') > 120
            ";

        //echo $sql4 . '\n';
        $query4 = Model\Project::query($sql4);
        $count4 = $query4->rowCount();
        // -- eliminamos registros relativos a aportes no existentes
        Model\Project::query("DELETE FROM `invest_address` WHERE invest NOT IN (SELECT id FROM `invest`)");
        Model\Project::query("DELETE FROM `invest_detail`  WHERE invest NOT IN (SELECT id FROM `invest`)");
        Model\Project::query("DELETE FROM `invest_reward`  WHERE invest NOT IN (SELECT id FROM `invest`)");
        echo "Eliminados $count4 aportes incompletos y sus registros (recompensa, dirección, detalles) relacionados.\n";
        */

        if ($debug) echo "\n-----\nIniciamos caducidad de tokens\n";
        // eliminamos los tokens que tengan más de 4 días
        $sql5 = "SELECT id, token FROM user WHERE token IS NOT NULL AND token != '' AND token LIKE '%¬%'";
        $query5 = Model\Project::query($sql5);
        foreach ($query5->fetchAll(\PDO::FETCH_OBJ) as $row) {
            $parts = explode('¬', $row->token);
            $datepart = strtotime($parts[2]);
            $today = date('Y-m-d');
            $datedif = strtotime($today) - $datepart;
            $days = round($datedif / 86400);
            if ($days > 4 || !isset($parts[2])) {
                if ($debug) echo "User: $row->id  ;  Token: $row->token  ;  Datepart: $parts[2]   =>  $datepart  ;  Compare: $today  =>  $datedif  ;  Days: $days  ;   ";

                if (Model\Project::query("UPDATE user SET token = '' WHERE id = ?", array($row->id))) {
                    if ($debug) echo "Token borrado.";
                } else {
                    if ($debug) echo "Fallo al borrar Token!!!";
                }
                if ($debug) echo "\n";
            }

        }

        echo "\nDone!\n";
    }

}

