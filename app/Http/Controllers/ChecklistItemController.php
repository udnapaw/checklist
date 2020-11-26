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

class ChecklistItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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
            $item->asignee_id = $assigneeId;
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
                    'self'  => url() . '/checklists/' . $checklistId
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
                    'self'  => url() . '/checklists/' . $checklistId
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
                $checklistItem->asignee_id = $asigneeId;
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
                        'self'  => url() . '/checklists/' . $checklistId
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
                    $history->value = json_encode($checklistId.'-'.$itemId);
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
                        'self'  => url() . '/checklists/' . $checklistId
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
}
