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

class ItemController extends Controller
{

    protected $QueryParameterController;

    public function __construct(QueryParameterController $QueryParameterController)
    {
        $this->middleware('auth:api');
        $this->QueryParameterController = $QueryParameterController;
    }

    public function store(Request $req, $checklistId)
    {
        try {
            $checklist = Checklist::findOrFail($checklistId);

            $validator = Validator::make($req->all(), [
                'description'       => 'required',
                'due'               => 'required|date_format:Y-m-d H:i:s',
                'urgency'           => 'integer|nullable',
                'assignee_id'       => 'nullable'

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            DB::beginTransaction();
            $userId = auth()->user()->id;
            $description = $req->description;
            $due = $req->due;
            $urgency = $req->urgency;
            $assigneeId = $req->assignee_id;
            $isCompleted = 0;
            $now = date("Y-m-d H:i:s");
            $type = 'items';
            $action = 'create';

            $item = new ChecklistItem();
            $item->checklist_id = $checklistId;
            $item->description = $description;
            $item->due = $due;
            $item->urgency = $urgency;
            $item->assignee_id = $assigneeId;
            $item->is_completed = $isCompleted;
            $item->created_by = $userId;
            $item->save();

            if (!$item->save()) {
                DB::rollBack();
                return response()->json([
                    'status'    => 400,
                    'error'     => 'Failed insert checklist item'
                ], 400);
            } else {

                $itemId = $item->id;

                $history = new History();
                $history->loggable_type = $type;
                $history->loggable_id = $itemId;
                $history->action = $action;
                $history->value = json_encode($req->all());
                $history->created_by = $userId;
                $history->save();

                if (!$history->save()) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => 400,
                        'message'   => 'Failed insert history'
                    ], 400);
                }

                $attributes = array(
                    'description'   => $description,
                    'is_completed'  => $isCompleted == 0 ? false : true,
                    'completed_at'  => NULL,
                    'due'           => $due,
                    'urgency'       => $urgency,
                    'update_by'     => $userId,
                    'updated_at'    => NULL,
                    'created_at'    => $now
                );

                $links = array(
                    'self'  => route('get.checklist', ['checklistId' => $checklistId])
                );

                $data = array(
                    'type'          => $type,
                    'id'            => $itemId,
                    'attributes'    => $attributes,
                    'links'         => $links
                );
                $response = array(
                    'data' => $data
                );

                DB::commit();
                return response()->json([
                    $response
                ], 200);
            }
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

    public function getChecklistItem($checklistId, $itemId)
    {
        try {
            $checklistItem = ChecklistItem::where('checklist_id', $checklistId)->where('id', $itemId)->first();
            $type = 'items';

            if (empty($checklistItem)) {
                return response()->json([
                    'status'    => 404,
                    'error'     => 'Not Found'
                ], 404);
            } else {
                $attributes = array(
                    'description'   => $checklistItem->description,
                    'is_completed'  => $checklistItem->is_completed == 0 ? false : true,
                    'completed_at'  => $checklistItem->completed_at,
                    'due'           => $checklistItem->due,
                    'urgency'       => $checklistItem->urgency,
                    'updated_by'    => $checklistItem->updated_by,
                    'created_by'    => $checklistItem->created_by,
                    'checklist_id'  => $checklistId,
                    'assignee_id'   => $checklistItem->assignee_id,
                    'task_id'       => $checklistItem->task_id,
                    'deleted_at'    => $checklistItem->deleted_at,
                    'craeted_at'    => $checklistItem->created_at,
                    'updated_at'    => $checklistItem->updated_at
                );

                $links = array(
                    'self'  => route('get.checklist', ['checklistId' => $checklistId])
                );

                $data = array(
                    'type'          => $type,
                    'id'            => $checklistItem->id,
                    'attributes'    => $attributes,
                    'links'         => $links
                );

                $response = array(
                    'data'  => $data
                );

                return response()->json([
                    $response
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function update($checklistId, $itemId, Request $req)
    {
        try {
            $checklistItem = ChecklistItem::where('checklist_id', $checklistId)->where('id', $itemId)->first();
            $type = 'items';

            if (empty($checklistItem)) {
                return response()->json([
                    'status'    => 404,
                    'error'     => 'Not Found'
                ], 404);
            } else {
                $validator = Validator::make($req->all(), [
                    'description'       => 'required',
                    'due'               => 'required|date_format:Y-m-d H:i:s',
                    'urgency'           => 'integer|nullable',
                    'assignee_id'       => 'nullable'

                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }

                DB::beginTransaction();
                $userId = auth()->user()->id;
                $now = date("Y-m-d H:i:s");
                $type = 'items';
                $action = 'update';
                $description = $req->description;
                $due = $req->due;
                $urgency = $req->urgency;
                $asigneeId = $req->assignee_id;
                $isCompleted = $checklistItem->is_completed;
                $completedAt = $checklistItem->completed_at;

                $checklistItem->description = $description;
                $checklistItem->due = $due;
                $checklistItem->urgency = $urgency;
                $checklistItem->assignee_id = $asigneeId;
                $checklistItem->updated_by = $userId;
                $checklistItem->save();

                if (!$checklistItem->save()) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => 400,
                        'error'     => 'Failed insert checklist item'
                    ], 400);
                } else {
                    $history = new History();
                    $history->loggable_type = $type;
                    $history->loggable_id = $itemId;
                    $history->action = $action;
                    $history->value = json_encode($req->all());
                    $history->created_by = $userId;
                    $history->save();

                    if (!$history->save()) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => 400,
                            'message'   => 'Failed insert history'
                        ], 400);
                    }

                    $attributes = array(
                        'description'   => $description,
                        'is_completed'  => $isCompleted == 0 ? false : true,
                        'completed_at'  => $completedAt,
                        'due'           => $due,
                        'urgency'       => $urgency,
                        'update_by'     => $userId,
                        'updated_at'    => $now,
                        'created_at'    => $checklistItem->created_at
                    );

                    $links = array(
                        'self'  => route('get.checklist', ['checklistId' => $checklistId])
                    );

                    $data = array(
                        'type'          => $type,
                        'id'            => $itemId,
                        'attributes'    => $attributes,
                        'links'         => $links
                    );
                    $response = array(
                        'data' => $data
                    );

                    DB::commit();
                    return response()->json([
                        $response
                    ], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function delete($checklistId, $itemId)
    {
        try {
            $checklistItem = ChecklistItem::where('checklist_id', $checklistId)->where('id', $itemId)->first();
            $type = 'items';

            if (empty($checklistItem)) {
                return response()->json([
                    'status'    => 404,
                    'error'     => 'Not Found'
                ], 404);
            } else {

                DB::beginTransaction();
                $userId = auth()->user()->id;
                $now = date("Y-m-d H:i:s");
                $type = 'items';
                $action = 'delete';
                $description = $checklistItem->description;
                $isCompleted = $checklistItem->is_completed;
                $due = $checklistItem->due;
                $urgency = $checklistItem->urgency;
                $createdAt = $checklistItem->created_at;
                $completedAt = $checklistItem->completed_at;

                $checklistItem->deleted_at = $now;
                $checklistItem->updated_by = $userId;
                $checklistItem->save();

                if (!$checklistItem->save()) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => 400,
                        'error'     => 'Failed delete checklist item'
                    ], 400);
                } else {

                    $history = new History();
                    $history->loggable_type = $type;
                    $history->loggable_id = $itemId;
                    $history->action = $action;
                    $history->value = json_encode($checklistId . '-' . $itemId);
                    $history->created_by = $userId;
                    $history->save();

                    if (!$history->save()) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => 400,
                            'message'   => 'Failed insert history'
                        ], 400);
                    }

                    $attributes = array(
                        'description'   => $description,
                        'is_completed'  => $isCompleted == 0 ? false : true,
                        'completed_at'  => $completedAt,
                        'due'           => $due,
                        'urgency'       => $urgency,
                        'update_by'     => $userId,
                        'updated_at'    => $now,
                        'created_at'    => $createdAt
                    );

                    $links = array(
                        'self'  => route('get.checklist', ['checklistId' => $checklistId])
                    );

                    $data = array(
                        'type'          => $type,
                        'id'            => $itemId,
                        'attributes'    => $attributes,
                        'links'         => $links
                    );
                    $response = array(
                        'data' => $data
                    );

                    DB::commit();
                    return response()->json([
                        $response
                    ], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function updateBulk($checklistId, Request $req)
    {

        $message = array();
        $datas = $req->data;
        $type = 'items';

        foreach ($datas as $data) {

            try {

                $id = $data['id'];
                $action = $data['action'];
                $description = $data['attributes']['description'];
                $urgency = $data['attributes']['urgency'];
                $due = $data['attributes']['due'];
                $userId = auth()->user()->id;

                $getChecklistItem = ChecklistItem::where('id', $id)->where('checklist_id', $checklistId)->first();

                if (empty($getChecklistItem)) {

                    $messageEmptyChecklistItem = array(
                        'id'        => $id,
                        'action'    => $action,
                        'status'    => 404
                    );

                    array_push($message, $messageEmptyChecklistItem);
                } else {

                    $getChecklistItem->description = $description;
                    $getChecklistItem->due = $due;
                    $getChecklistItem->urgency = $urgency;
                    $getChecklistItem->updated_by = $userId;
                    $getChecklistItem->save();

                    if (!$getChecklistItem->save()) {

                        $messageFailedUpdate = array(
                            'id'        => $id,
                            'action'    => $action,
                            'status'    => 400
                        );

                        array_push($message, $messageFailedUpdate);
                    } else {

                        $messageSuccess = array(
                            'id'        => $id,
                            'action'    => $action,
                            'status'    => 200
                        );

                        array_push($message, $messageSuccess);

                        $history = new History();
                        $history->loggable_type = $type;
                        $history->loggable_id = $id;
                        $history->action = $action;
                        $history->value = json_encode($data);
                        $history->created_by = $userId;
                        $history->save();
                    }
                }
            } catch (Exception $e) {

                $messageException = array(
                    'id'        => $id,
                    'action'    => $action,
                    'status'    => 500
                );

                array_push($message, $messageException);
            }
        }

        return response()->json([
            'data'    => $message,
        ], 200);
    }

    public function complete(Request $req)
    {
        try {

            $datas = $req->data;
            $type = 'items';
            $now = date("Y-m-d H:i:s");
            $action = 'completed';
            $userId = auth()->user()->id;
            $response = array();

            foreach ($datas as $data) {

                DB::beginTransaction();
                $id = $data['item_id'];
                $getChecklistItems = ChecklistItem::findOrFail($id);

                $getChecklistItems->is_completed = 1;
                $getChecklistItems->completed_at = $now;
                $getChecklistItems->completed_by = $userId;
                $getChecklistItems->updated_by = $userId;
                $getChecklistItems->save();

                if (!$getChecklistItems->save()) {

                    DB::rollBack();
                    return response()->json([
                        'status'    => 400,
                        'error'     => 'Failed completed item'
                    ], 400);
                } else {

                    $history = new History();
                    $history->loggable_type = $type;
                    $history->loggable_id = $id;
                    $history->action = $action;
                    $history->value = json_encode($req->all());
                    $history->created_by = $userId;
                    $history->save();

                    if (!$history->save()) {

                        DB::rollBack();
                        return response()->json([
                            'status'    => 400,
                            'message'   => 'Failed insert history'
                        ], 400);
                    }

                    DB::commit();
                    $messageSuccess = array(
                        'id'            => $id,
                        'item_id'       => $id,
                        'is_completed'  => true,
                        'checklist_id'  => $getChecklistItems->checklist_id
                    );

                    array_push($response, $messageSuccess);
                }
            }

            $response = array(
                'data'  => $response
            );

            return response()->json(
                $response,
                200
            );
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

    public function incomplete(Request $req)
    {
        try {

            $datas = $req->data;
            $type = 'items';
            $action = 'incompleted';
            $userId = auth()->user()->id;
            $response = array();

            foreach ($datas as $data) {

                DB::beginTransaction();
                $id = $data['item_id'];
                $getChecklistItems = ChecklistItem::findOrFail($id);

                $getChecklistItems->is_completed = 0;
                $getChecklistItems->completed_at = null;
                $getChecklistItems->updated_by = $userId;
                $getChecklistItems->save();

                if (!$getChecklistItems->save()) {

                    DB::rollBack();
                    return response()->json([
                        'status'    => 400,
                        'error'     => 'Failed incompleted item'
                    ], 400);
                } else {

                    $history = new History();
                    $history->loggable_type = $type;
                    $history->loggable_id = $id;
                    $history->action = $action;
                    $history->value = json_encode($req->all());
                    $history->created_by = $userId;
                    $history->save();

                    if (!$history->save()) {

                        DB::rollBack();
                        return response()->json([
                            'status'    => 400,
                            'message'   => 'Failed insert history'
                        ], 400);
                    }

                    DB::commit();
                    $messageSuccess = array(
                        'id'            => $id,
                        'item_id'       => $id,
                        'is_completed'  => false,
                        'checklist_id'  => $getChecklistItems->checklist_id
                    );

                    array_push($response, $messageSuccess);
                }
            }

            $response = array(
                'data'  => $response
            );

            return response()->json(
                $response,
                200
            );
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

    public function getAllItems(Request $req)
    {

        try {

            $filters = $req->filter;
            $sort = $req->sort;
            $type = 'items';
            $resultData = array();
            $pageLimit = empty($req->page['limit']) ? 10 : $req->page['limit'];
            $pageOffset = empty($req->page['offset']) ? 0 : $req->page['offset'];

            $getParamater = $this->QueryParameterController->getParamater($filters, $sort);

            $paramaters = array(
                'filter'    => $getParamater['queryParams'],
                'sort'      => $getParamater['sortParam'],
                'limit'     => $pageLimit,
                'offset'    => $pageOffset
            );

            $getValues = $this->QueryParameterController->queryChecklistItemParameter($paramaters);

            $total = $getValues['total'];

            foreach ($getValues['data'] as $data) {
                $itemId =  $data->id;
                $description =  $data->description;
                $isCompleted =  $data->is_completed == 0 ? false : true;
                $completedAt =  $data->completed_at;
                $completedBy =  $data->completed_by;
                $due =  $data->due;
                $urgency = $data->urgency;
                $updatedBy = $data->updated_by;
                $createdBy = $data->created_by;
                $checklistId = $data->checklist_id;
                $assigneeId = $data->assignee_id;
                $taskId = $data->task_id;
                $deletedAt = $data->deleted_at;
                $createdAt = $data->created_at;
                $updatedAt = $data->updated_at;
                $linkSelf = $req->url() . "/" . $itemId;

                $attributes = array(
                    'description'   => $description,
                    'is_completed'  => $isCompleted,
                    'completed_at'  => $completedAt,
                    'completed_by'  => $completedBy,
                    'due'           => $due,
                    'urgency'       => $urgency,
                    'updated_by'    => $updatedBy,
                    'created_by'    => $createdBy,
                    'checklist_id'  => $checklistId,
                    'assignee_id'   => $assigneeId,
                    'task_id'       => $taskId,
                    'deleted_at'    => $deletedAt,
                    'created_at'    => $createdAt,
                    'updated_at'    => $updatedAt
                );

                $linksSelf = array(
                    'self'  => $linkSelf
                );

                $resultData[] = array(
                    'type'          => $type,
                    'id'            => $itemId,
                    'attributes'    => $attributes,
                    'links'         => $linksSelf
                );
            }

            $firstPageOffset = 0;
            $lastPageOffset = floor(($total - $pageOffset) / $pageLimit);
            $lastPageOffset = $lastPageOffset * $pageLimit;
            $fullUrl = $req->fullUrl();

            $getLinks = $this->QueryParameterController->pageOffset($firstPageOffset, $lastPageOffset, $pageOffset, $fullUrl, $pageLimit);

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
                'meta'      => $meta,
                'data'      => $resultData,
                'links'     => $links
            );

            return response()->json($response, 200);
        } catch (Exception $e) {

            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function itemsInGivenChecklists($checklistId)
    {

        try {

            $type = 'items';

            $checklistItems = ChecklistItem::where('checklist_id', $checklistId)->with('checklist')->get();
            $attributes = array();
            $items = array();

            foreach ($checklistItems as $checklistItem) {

                $items[] = array(
                    'id'                => $checklistItem->id,
                    'name'              => $checklistItem->description,
                    'user_id'           => auth()->user()->id,
                    'is_completed'      => $checklistItem->is_completed == 0 ? false : true,
                    'due'               => $checklistItem->due,
                    'urgency'           => $checklistItem->urgency,
                    'checklist_id'      => $checklistId,
                    'assignee_id'       => $checklistItem->assignee_id,
                    'task_id'           => $checklistItem->task_id,
                    'completed_at'      => $checklistItem->completed_at,
                    'last_update_by'    => $checklistItem->updated_by,
                    'updated_at'        => $checklistItem->updated_at,
                    'created_at'        => $checklistItem->created_at
                );

                $attributes = array(
                    'object_domain'     => $checklistItem->checklist->object_domain,
                    'object_id'         => $checklistItem->checklist->object_id,
                    'description'       => $checklistItem->checklist->description,
                    'is_completed'      => $checklistItem->checklist->is_completed == 0 ? false : true,
                    'due'               => $checklistItem->checklist->due,
                    'urgency'           => $checklistItem->checklist->urgency,
                    'completed_at'      => $checklistItem->checklist->completed_at,
                    'last_update_by'    => $checklistItem->checklist->updated_by,
                    'updated_at'        => $checklistItem->checklist->updated_at,
                    'created_at'        => $checklistItem->checklist->created_at,
                    'items'             => $items
                );
            }

            $links = array(
                'self'  => route('get.checklist', ['checklistId' => $checklistId])
            );

            $data = array(
                'type'          => $type,
                'id'            => $checklistId,
                'attributes'    => $attributes,
                'links'         => $links
            );

            $response = array(
                'data'  => $data
            );

            return response()->json($response, 200);
        } catch (Exception $e) {

            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function summaryItem(Request $req)
    {

        try {

            $date = date("Y-m-d H:i:s", strtotime($req->date));
            $week = date("Y-m-d H:i:s", strtotime($req->date . '-7 days'));
            $lastWeek = date("Y-m-d H:i:s", strtotime($req->date . '-14 days'));
            $lastMonth = date("Y-m-d H:i:s", strtotime($req->date . '-1 month'));

            $todayItems = ChecklistItem::whereDate('created_at', $date)->count();
            $pastDueItems = ChecklistItem::whereNotNull('due')->where('due', '<', $date)->where('is_completed', 0)->count();
            $thisWeekItems = ChecklistItem::whereBetween('created_at', [$week, $date])->count();
            $pastWeekItems = ChecklistItem::whereBetween('created_at', [$lastWeek, $week])->count();
            $thisMonthItems = ChecklistItem::whereMonth('created_at', $date)->count();
            $pastMonthItems = ChecklistItem::whereMonth('created_at', $lastMonth)->count();
            $total = $todayItems + $pastDueItems + $thisWeekItems + $pastWeekItems + $thisMonthItems + $pastMonthItems;

            $data = array(
                'today'         => $todayItems,
                'past_due'      => $pastDueItems,
                'this_week'     => $thisWeekItems,
                'past_week'     => $pastWeekItems,
                'this_month'    => $thisMonthItems,
                'past_month'    => $pastMonthItems,
                'total'         => $total
            );

            $response = array(
                'data'  => $data
            );

            return response()->json($response, 500);
        } catch (Exception $e) {

            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }
}
