<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Reporting,
        Goteo\Model;

    class Reports {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            switch ($action)  {
                case 'paypal':
                    // calculamos lo que debería haber ahora en PayPal:
                    $data = new \stdClass;

                    /*
                     * Aportes en estado preapproval:
                     *      En paypal aun no hay nada de estos
                     */

                    /* Aportes en estado cobrado por goteo:
                     *      En paypal debería haber el 100% de estos
                     *      (menos comision)
                     */
                    $sql = "
                        SELECT SUM(amount) as amount, COUNT(id) as num
                        FROM invest
                        WHERE method = 'paypal' AND status = 1
                    ";
                    $query = Model\Invest::query($sql);
                    $charged = $query->fetchObject();
                    $charged->fee = $charged->amount * 0.034 + $charged->num * 0.35;
                    $charged->net = $charged->amount - $charged->fee;
                    $charged->goteo = $charged->net;
                    $data->charged = $charged;

                    /* Aportes en estado pagado al proyecto:
                     *      En paypal debería haber el 8% de estos
                     *      (menos comision)
                     */
                    $sql = "
                        SELECT SUM(amount) as amount, COUNT(id) as num
                        FROM invest
                        WHERE method = 'paypal' AND status = 3
                    ";
                    $query = Model\Invest::query($sql);
                    $paid = $query->fetchObject();
                    $paid->fee = $paid->amount * 0.034 + $paid->num * 0.35;
                    $paid->net = $paid->amount - $paid->fee;
                    $paid->goteo = $paid->net * 0.08;

                    $data->paid = $paid;

                    $data->total = $charged->goteo + $paid->goteo;

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'reports',
                            'file'   => 'paypal',
                            'data'   => $data
                        )
                    );

                    break;
            }

            $reports = Reporting::getList();

            if (!empty($filters['report'])) {
                $data = Reporting::getReport($filters['report'], $filters);
            } else {
                $data = null;
            }


            return new View(
                'view/admin/index.html.php',
                array(
                    'folder'  => 'reports',
                    'file'    => 'list',
                    'reports' => $reports,
                    'filters' => $filters,
                    'data'    => $data
                )
            );

        }

    }

}
