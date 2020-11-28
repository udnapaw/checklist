<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Checklist;
use Illuminate\Support\Facades\DB;
use App\Models\ChecklistItem;
use App\Models\History;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\QueryParameterController;

class HistoryController extends Controller
{
    protected $QueryParameterController;

    public function __construct(QueryParameterController $QueryParameterController)
    {
        $this->middleware('auth:api');
        $this->QueryParameterController = $QueryParameterController;
    }

    public function getList(Request $req)
    {

        try {

            $type = "history";
            $pageLimit = empty($req->page['limit']) ? 10 : $req->page['limit'];
            $pageOffset = empty($req->page['offset']) ? 0 : $req->page['offset'];

            $total = History::count();
            $histories = History::skip($pageOffset)->take($pageLimit)->get();

            $firstPageOffset = 0;
            $lastPageOffset = floor(($total - $pageOffset) / $pageLimit);
            $lastPageOffset = $lastPageOffset * $pageLimit;
            $fullUrl = $req->fullUrl();

            $getLinks = $this->QueryParameterController->pageOffset($firstPageOffset, $lastPageOffset, $pageOffset, $fullUrl, $pageLimit);
            $data = array();

            foreach ($histories as $history) {

                $attributes = array(
                    'loggable_type' => $history->loggable_type,
                    'loggable_id'   => $history->loggable_id,
                    'action'        => $history->action,
                    'kwuid'         => $history->kwuid,
                    'value'         => $history->value,
                    'created_at'    => $history->created_at,
                    'updated_at'    => $history->updated_at
                );

                $links = array(
                    'self'  => route('get.history', ['historyId', $history->id])
                );

                $data[] = array(
                    'type'          => $type,
                    'id'            => $history->id,
                    'attributes'    => $attributes,
                    'links'         => $links
                );
            }

            $meta = array(
                'count' => $pageLimit,
                'total' => $total
            );

            $links = array(
                'first' => $getLinks['firstLink'],
                'last'  => $getLinks['lastLink'],
                'next'  => $getLinks['nextLink'],
                'prev'  => $getLinks['prevLink']
            );

            $response = array(
                'meta'  => $meta,
                'links' => $links,
                'data'  => $data,
            );

            return response()->json($response, 200);
        } catch (Exception $e) {

            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function getById($historyId)
    {

        try {

            $history = History::findOrFail($historyId);

            $attributes = array(
                'loggable_type' => $history->loggable_type,
                'loggable_id'   => $history->loggable_id,
                'action'        => $history->action,
                'kwuid'         => $history->kwuid,
                'value'         => $history->value,
                'created_at'    => $history->created_at,
                'updated_at'    => $history->updated_at
            );

            $links = array(
                'self'  => route('get.history', ['historyId' => $historyId])
            );

            $data = array(
                'type'  => 'history',
                'id'    => $historyId,
                'attributes'    => $attributes,
                'links'         => $links
            );

            $response = array(
                'data'  => $data
            );

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status'    => 404,
                'error'     => $e->getMessage()
            ], 404);
        } catch (Exception $e) {

            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }
}
