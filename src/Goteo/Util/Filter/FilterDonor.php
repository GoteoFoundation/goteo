<?php

namespace Goteo\Util\Filter;

use DateTime;
use Goteo\Model\Filter;
use Goteo\Model\Filter\FilterLocation;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Model\User\DonorLocation;

class FilterDonor
{
    private Filter $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    private function getFilters($lang = null): array
    {
        $filter = $this->filter;

        $values = [];
        $sqlInner  = '';
        $sqlFilter = '';
        $sqlInnerWhere = '';
        $investStatus = Invest::$RAISED_STATUSES;

        if ($filter->invest_status) {
            $investStatus = [$filter->invest_status];
        }

        if (isset($filter->foundationdonor)) {
            $sqlFilter .= " AND user.id ";
            $sqlFilter .= ($filter->foundationdonor)? "" : "NOT ";
            $sqlFilter .= " IN (
                SELECT i.`user`
                FROM invest i
                WHERE
                i.status= :status_donated
                )";
            $values[':status_donated'] = Invest::STATUS_DONATED;
        }

        $filter->projects = $filter->getFilterProject($filter->id);
        $filter->calls = $filter->getFilterCall($filter->id);
        $filter->channels = $filter->getFilterNode($filter->id);
        $filter->matchers = $filter->getFilterMatcher($filter->id);
        $filter->social_commitments = $filter->getFilterSocialCommitments($filter->id);

        $sqlInner .= "INNER JOIN (
            SELECT invest.user FROM invest ";

        if (!empty($filter->calls)) {
            $sqlInner .= "INNER JOIN call_project
            ON call_project.project = invest.project
            ";
            $parts = [];
            foreach(array_keys($filter->calls) as $index => $id) {
                $parts[] = ':calls_' . $index;
                $values[':calls_' . $index] = $id;
            }
            if($parts) $sqlInner .= " AND call_project.call IN (" . implode(',', $parts) . ") ";
        }

        if (!empty($filter->channels)) {
            $sqlInner .= "LEFT JOIN node_project
                ON node_project.project_id = invest.project
            INNER JOIN project
                ON project.id = invest.project
            ";
            $parts = [];
            foreach(array_keys($filter->channels) as $index => $id) {
                $parts[] = ':nodes_' . $index;
                $values[':nodes_' . $index] = $id;
            }
            if($parts) $sqlInner .= " AND ( node_project.node_id IN (" . implode(',', $parts) . ") OR  project.node IN (" . implode(',', $parts) . ") ) ";
        }

        if (!empty($filter->matchers)) {

            $sqlInner .= "INNER JOIN matcher_project
            ON matcher_project.project_id = invest.project
            ";

            $parts = [];
            foreach(array_keys($filter->matchers) as $index => $id) {
                $parts[] = ':matchers_' . $index;
                $values[':matchers_' . $index] = $id;
            }
            if($parts) $sqlInner .= " AND matcher_project.matcher_id IN (" . implode(',', $parts) . ") ";
        }

        if (isset($filter->project_status) && $filter->project_status > -1 && !empty($sqlInner)) {
            $sqlInner .= "INNER JOIN project p2 ON p2.id = invest.project AND p2.status = :project_status ";
            $values[':project_status'] = $filter->project_status;
        }

        $sqlInnerWhere .= "WHERE  invest.status IN ";

        $parts = [];
        foreach($investStatus as $index => $status) {
            $parts[] = ':invest_status' . $index;
            $values[':invest_status' . $index] = $status;
        }
        $sqlInnerWhere .= " (" . implode(',', $parts) . ") ";

        if (!empty($filter->projects)) {
            $parts = [];
            foreach(array_keys($filter->projects) as $index => $id) {
                $parts[] = ':project_' . $index;
                $values[':project_' . $index] = $id;
            }
            if($parts) $sqlInnerWhere .= " AND invest.project IN (" . implode(',', $parts) . ") ";
        }

        if (!empty($filter->social_commitments)) {
            $parts = [];
            foreach(array_keys($filter->social_commitments) as $index => $id) {
                $parts[] = ':socialcommitment_' . $index;
                $values[':socialcommitment_' . $index] = $id;
            }
            if($parts) {
                $sqlInner .= "INNER JOIN project p_sc ON invest.project = p_sc.id ";

                $sqlInnerWhere .= " AND p_sc.social_commitment IN (". implode(',', $parts) . ") ";
            }
        }

        if (isset($filter->startdate) && !isset($filter->cert)) {

            $sqlInnerWhere .= " AND  invest.status IN ";
            $parts = [];
            foreach($investStatus as $index => $status) {
                $parts[] = ':invest_status_' . $index;
                $values[':invest_status_' . $index] = $status;
            }
            $sqlInnerWhere .= " (" . implode(',', $parts) . ") ";

            $sqlInnerWhere .= " AND invest.invested BETWEEN :startdate ";
            $values[':startdate'] = $filter->startdate;

            if(isset($filter->enddate)) {
                $sqlInnerWhere .= " AND :enddate ";
                $values[':enddate'] = $filter->enddate;
            } else {
                $sqlInnerWhere .= " AND curdate() ";
            }
        } else if (isset($filter->enddate) && !isset($filter->cert)) {
            $sqlInnerWhere .= "AND invest.invested < :enddate
                         AND  invest.status IN ";
            $parts = [];
            foreach($investStatus as $index => $status) {
                $parts[] = ':invest_status_' . $index;
                $values[':invest_status_' . $index] = $status;
            }
            $sqlInnerWhere .= " (" . implode(',', $parts) . ") ";

            $values[':enddate'] = $filter->enddate;
        }


        if (isset($filter->amount)) {
            $sqlInnerWhere .= "AND invest.amount >= :amount ";
            $values[':amount'] = $filter->amount;
        }

        $sqlInnerWhere .= "GROUP BY invest.user
                     ) AS invest_user ON invest_user.user = user.id ";

        $sqlInner .= $sqlInnerWhere;

        if (isset($filter->typeofdonor)) {
            if ($filter->typeofdonor == Filter::UNIQUE) {
                $sqlFilter .= "  AND 1 = (SELECT count(*) FROM invest WHERE invest.user = user.id AND invest.status IN ('0','1','3','4','6') and invest.project IS NOT NULL)
            ";
            } else if ($filter->typeofdonor == Filter::MULTIDONOR) {
                $sqlFilter .= " AND 1 < (SELECT count(*) FROM invest WHERE invest.user = user.id AND invest.status IN ('0','1','3','4','6') and invest.project IS NOT NULL)
            ";
            }
        }

        if (isset($filter->donor_status)) {
            $sqlInner .= " INNER JOIN donor
            ON donor.user = user.id";
            $sqlFilter .= " AND donor.status = :donor_status";
            $values[':donor_status'] = $filter->donor_status;

            if (isset($filter->startdate)) {
                $sqlFilter .= " AND donor.year BETWEEN :startyear ";
                $values[':startyear'] = DateTime::createFromFormat("Y-m-d",$filter->startdate)->format("Y");

                if(isset($filter->enddate)) {
                    $sqlFilter .= " AND :endyear ";
                    $values[':endyear'] = DateTime::createFromFormat("Y-m-d",$filter->enddate)->format("Y");
                } else {
                    $sqlFilter .= " AND YEAR(CURDATE())";
                }
            } else if (isset($filter->enddate)) {
                $sqlFilter .= " AND donor.year <= :endyear ";
                $values[':enddate'] = DateTime::createFromFormat("Y-m-d",$filter->enddate)->format("Y");;
            }
        }

        if (isset($filter->wallet)) {
            $sqlFilter .= " AND user.id ";
            $sqlFilter .= ($filter->wallet)? "IN " : "NOT IN ";
            $sqlFilter .= " ( SELECT user_pool.user
                              FROM user_pool
                              WHERE user_pool.amount > 0 )";
        }

        if (isset($filter->cert)) {
            $sqlInner .= " INNER JOIN donor donor_cert
            ON donor_cert.user = user.id AND donor_cert.confirmed = :cert ";
            $values[':cert'] = $filter->cert;


            if (isset($filter->startdate)) {
                $sqlInner .= " AND donor_cert.year BETWEEN :startyear ";
                $values[':startyear'] = DateTime::createFromFormat("Y-m-d",$filter->startdate)->format("Y");

                if(isset($filter->enddate)) {
                    $sqlInner .= " AND :endyear ";
                    $values[':endyear'] = DateTime::createFromFormat("Y-m-d",$filter->enddate)->format("Y");
                } else {
                    $sqlFilter .= " AND YEAR(CURDATE())";
                }
            } else if (isset($filter->enddate)) {
                $sqlFilter .= " AND donor_cert.year <= :endyear ";
                $values[':enddate'] = DateTime::createFromFormat("Y-m-d",$filter->enddate)->format("Y");;
            }
        }

        if (isset($lang)) {
            $parts = [];
            $sqlFilter .= " AND user.lang ";
            foreach($lang as $key => $value) {
                $parts[] = ':lang' . $key;
                $values[':lang' . $key] = $value;
            }
            if($parts) $sqlFilter .= " IN (" . implode(',', $parts) . ") ";
        }

        if ($filter->filter_location) {
            $loc = FilterLocation::get($filter->id);
            $loc = new DonorLocation($loc);
            $loc->location = $filter->donor_location;
            $distance = $loc->radius ?: 50; // search in 50 km by default

            $sqlInner .= " INNER JOIN donor
            ON donor.user = user.id ";

            $sqlInner .= " INNER JOIN donor_location
                            ON donor_location.id = donor.id ";
            $location_parts = DonorLocation::getSQLFilterParts($loc, $distance, true, $loc->city, 'donor.location');
            $values[":location_minLat"] = $location_parts['params'][':location_minLat'];
            $values[":location_minLon"] = $location_parts['params'][':location_minLon'];
            $values[":location_maxLat"] = $location_parts['params'][':location_maxLat'];
            $values[":location_maxLon"] = $location_parts['params'][':location_maxLon'];
            $values[":location_text"] = $location_parts['params'][':location_text'];
            $sqlFilter .= " AND ({$location_parts['firstcut_where']})" ;
        }

        $sqlFilter = ($filter->forced) ? $sqlFilter : " AND (user_prefer.mailing = 0 OR user_prefer.`mailing` IS NULL) " . $sqlFilter;

        return [$sqlInner, $sqlFilter, $values];
    }

    public function calculate(?string $lang = null): int {

        [$sqlInner, $sqlFilter, $values] = $this->getFilters($lang);

        $sql = "SELECT COUNT(user.id)
                FROM user
                LEFT JOIN user_prefer
                ON user.id = user_prefer.user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter";

        return (int) User::query($sql, $values)->fetchColumn();
    }

    /**
     * @return User[]
     */
    public function getDonors(int $offset = 0, int $limit = 0, string $lang = null): array
    {

        $receivers =  [];
        [$sqlInner, $sqlFilter, $values] = $this->getFilters($lang);

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                LEFT JOIN user_prefer
                ON user_prefer.user = user.id
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        if ($limit) $sql .= "LIMIT $offset, $limit ";

        if ($query = User::query($sql, $values)) {
            $receivers = $query->fetchAll(PDO::FETCH_CLASS, User::class);
        }

        return $receivers;
    }

    public function getSqlFilter($lang = null, $prefix = ''): array
    {

        [$sqlInner, $sqlFilter, $values] = $this->getFilters($lang);

        $values[':prefix'] = $prefix;

        $sql = "SELECT
                    :prefix,
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                LEFT JOIN user_prefer
                ON user_prefer.user = user.id
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                ";

        return [$sql, $values];
    }

}
