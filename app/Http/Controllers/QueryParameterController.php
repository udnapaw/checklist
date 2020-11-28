<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class QueryParameterController extends Controller
{
    public function pageOffset($firstPageOffset, $lastPageOffset, $pageOffset, $fullUrl, $pageLimit)
    {

        $oldPageUrl = "page%5Boffset%5D=" . $pageOffset;

        $newPageUrlFirstLink = "page%5Boffset%5D=" . $firstPageOffset;
        $newPageUrlLastLink = "page%5Boffset%5D=" . $lastPageOffset;
        $firstLink = str_replace($oldPageUrl, $newPageUrlFirstLink, $fullUrl);
        $lastLink = str_replace($oldPageUrl, $newPageUrlLastLink, $fullUrl);


        if ($pageOffset == 0) {

            $prevLink = null;
        } else {

            $prevPageOffset = $pageOffset - $pageLimit;
            $newPagePrevLink = "page%5Boffset%5D=" . $prevPageOffset;
            $prevLink = str_replace($oldPageUrl, $newPagePrevLink, $fullUrl);
        }

        if ($pageOffset == $lastPageOffset) {

            $nextLink = null;
        } else {

            $nextPageOffset = $pageLimit + $pageOffset;
            $newPageNextLink = "page%5Boffset%5D=" . $nextPageOffset;
            $nextLink = str_replace($oldPageUrl, $newPageNextLink, $fullUrl);
        }

        $result = array(
            'firstLink'     => $firstLink,
            'lastLink'      => $lastLink,
            'prevLink'      => $prevLink,
            'nextLink'      => $nextLink
        );

        return $result;
    }

    public function getParamater($filters, $sort)
    {

        $queryParams = array();

        if (!empty($filters)) {

            foreach ($filters as $field => $value) {

                foreach ($value as $query => $param) {
                    
                    if ($query == "between") {

                        $params = explode(",", $param);

                        $paramFrom = date("Y-m-d H:i:s", strtotime($params[0]));
                        $paramTo = date("Y-m-d H:i:s", strtotime($params[1]));

                        $paramCondition = 'ci.' . $field . " " . $query . " '" . $paramFrom . "' AND '" . $paramTo . "'";
                    }

                    else if ($query == "is") {

                        $query = "=";

                        $paramCondition = 'ci.' . $field . " " . $query . " '" . $param . "'";
                    }else {
                        return response()->json([
                            
                        ], 422);
                    }

                    $queryParam = " WHERE " . $paramCondition;
                    array_push($queryParams, $queryParam);
                }
            }
        }

        if (!empty($sort)) {

            $firstCharacterSort = substr($sort, 0, 1);

            if ($firstCharacterSort == "-") {

                $sortOrder = "DESC";
            } else {

                $sortOrder = "ASC";
            }

            $fieldSort = substr($sort, 1);
            $sortParam = " ORDER BY ci." . $fieldSort . " " . $sortOrder;
        }

        $result = array(
            'queryParams'   => $queryParams,
            'sortParam'     => $sortParam
        );

        return $result;
    }

    public function queryChecklistItemParameter($paramaters)
    {

        $filter = '';
        $sort = '';
        $limit = $paramaters['limit'];
        $offset = $paramaters['offset'];

        if (!empty($paramaters['filter'])) {

            foreach ($paramaters['filter'] as $filters) {
                $filter .= $filters;
            }
        }

        if (!empty($paramaters['sort'])) {
            $sort = $paramaters['sort'];
        }

        $filter = $this->conditionQuery($filter);

        $count = DB::select("SELECT count(ci.id) as total FROM checklistitems ci JOIN checklists c ON ci.checklist_id = c.id $filter");
        $datas = DB::select("SELECT ci.*, c.task_id FROM checklistitems ci JOIN checklists c ON ci.checklist_id = c.id $filter $sort LIMIT $limit OFFSET $offset");

        $result = array(
            'total' => $count[0]->total,
            'data'  => $datas
        );

        return $result;
    }

    public function conditionQuery($filter)
    {

        $ret = '';
        if (!empty($filter)) {
            $query = explode(" WHERE", $filter);
            $query = array_values(array_filter($query));
            foreach ($query as $key => $value) {
                if ($key == 0) {
                    $ret .= " WHERE " . $value;
                } else {
                    $ret .= " AND " . $value;
                }
            }
        }
        return $ret;
    }
}
