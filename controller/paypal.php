<?php

namespace Goteo\Controller {

    use Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model;

    class Paypal extends \Goteo\Core\Controller {
        
        public function index () {

            $content = "As the normal process in this App runs for 40 days (at least), we have prepared this shortcut board for you to do all the process in a painless way.

From this board you will: Initiate the process, Invest on the target project (accepting a pre-approval) and Execute the charge (with a Chained Payment).
You can do this process as many times as you need but you must always <strong>Initate</strong> it at first.

- <strong>Initiating the process</strong>: All Invests on the target project will be cancelled, the date of the target project will be changed to accept Investing, you will be redirected to the Investing page.
<a href='/paypal/init' target='_blank'>Click here to proceed and start the process</a>

- <strong>Investing on the target project</strong>: You will accept a pre-approval with a test account (password: 12345678) assigned to this user account as many times as you want (just click the <strong>\"APÓYALO\"</strong> violet button or <a href='".SITE_URL."/project/fixie-per-tothom/invest'>here</a>). Invest at least an amount of 400 &euro; for the target project to be successful. You will be redirected to PayPal and then to the post-invest page. You can return to this board by clicking the <strong>\"MI PANEL\"</strong> on the top-right ( or by this url <a href='".SITE_URL."/paypal'>".SITE_URL."/paypal</a> manualy).

- <strong>Executing the charge</strong>: The date of the target project will change to the final date. An automated script will do a Chained Payment on your Invest/s. 
<a href='/paypal/execute' target='_blank'>Click here to proceed and finish the process</a> (it will take a while)

- <strong>Executing the secondary payments</strong>: An automated script will do execute the pending Chained Payments. You can return to this board by this url <a href='".SITE_URL."/paypal'>".SITE_URL."/paypal</a> manualy ( or by clicking \"Back\").
<a href='/cron/dopay/fixie-per-tothom' target='_blank'>Click here to execute secondaries</a> (it will take a while)


You can return to this board by this url <a href='".SITE_URL."/paypal'>".SITE_URL."/paypal</a> manualy ( or by clicking \"Back\").

Please contact our chief developer <a href='mailto:jcanaves@doukeshi.org'>jcanaves@doukeshi.org</a> if you need any support.

Thank you for reviewing our App.";

            return new View(
                'view/about/sample.html.php',
                array(
                    'name' => 'Hi, wellcome to your board',
                    'description' => 'PayPal Tester Home',
                    'content' => nl2br($content)
                )
             );

        }

        public function init () {
            $project = Model\Project::get('fixie-per-tothom');

            // cancelar los aportes del proyecto 'fixie per tothom'
            $invests = Model\Invest::getAll('fixie-per-tothom');

            foreach ($invests as $key => $invest) {
                $invest->cancel();
            }

            // pasarlo a campaña
            $project->publish();

            // cambiar la fecha de campña a hace un mes
            $dateTo = date('Y-m-d', mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
            $sql = "UPDATE project SET published='{$dateTo}' WHERE id = :id";
            Model\Project::query($sql, array(':id'=>'fixie-per-tothom'));

            // saltar a la página de aportar
            throw new Redirection('/project/fixie-per-tothom/invest');
        }

        public function execute () {
            // cambiar la fecha a hoy menos 41 dias
            $dateTo = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-41,   date("Y")));
            $sql = "UPDATE project SET published='{$dateTo}' WHERE id = :id";
            Model\Project::query($sql, array(':id'=>'fixie-per-tothom'));

            // ejecutar cron para 'fixie per tothom'
            throw new Redirection('/cron/execute');
        }



    }
    
}