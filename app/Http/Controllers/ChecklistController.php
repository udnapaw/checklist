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

class ChecklistController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'object_domain'     => 'required',
                'object_id'         => 'required',
                'description'       => 'required',
                'due'               => 'required|date_format:Y-m-d H:i:s',
                'urgency'           => 'integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            DB::beginTransaction();
            $userId = auth()->user()->id;
            $type = 'checklists';
            $objectDomain = $req->object_domain;
            $objectId = $req->object_id;
            $description = $req->description;;
            $due = $req->due;
            $urgency = $req->urgency;
            $taskId = $req->task_id;
            $now = date("Y-m-d H:i:s");

            $checklist = new Checklist();
            $checklist->object_domain = $objectDomain;
            $checklist->object_id = $objectId;
            $checklist->description = $description;
            $checklist->due = $due;
            $checklist->urgency = $urgency;
            $checklist->task_id = $taskId;
            $checklist->created_by = $userId;
            $checklist->save();

            if (!$checklist->save()) {
                DB::rollBack();
                return response()->json([
                    'status'    => 400,
                    'message'   => 'Failed insert checklist'
                ], 400);
            } else {
                $checklistId = $checklist->id;
                $links = $req->fullUrl() . '/' . $checklistId;

                if (!empty($req->items)) {
                    $items = $req->items;
                    foreach ($items as $item) {
                        $checklistitem = new ChecklistItem();
                        $checklistitem->checklist_id = $checklistId;
                        $checklistitem->description = $item;
                        $checklistitem->created_by = $userId;
                        $checklistitem->save();

                        if (!$checklistitem->save()) {
                            DB::rollBack();
                            return response()->json([
                                'status'    => 400,
                                'message'   => 'Failed insert checklistitems'
                            ], 400);
                        }
                    }
                }

                $history = new History();
                $history->loggable_type = $type;
                $history->loggable_id = $checklistId;
                $history->action = 'create';
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
                return response()->json([
                    'data' => [
                        'type'  => $type,
                        'id'    => $checklistId,
                        'attributes'    => [
                            'object_domain' => $objectDomain,
                            'object_id'     => $objectId,
                            'task_id'       => $taskId,
                            'description'   => $description,
                            'is_completed'  => false,
                            'due'           => $due,
                            'urgency'       => $urgency,
                            'completed_at'  => NULL,
                            'update_by'     => NULL,
                            'created_by'    => $userId,
                            'created_at'    => $now,
                            'updated_at'    => $now
                        ],
                        'links' => [
                            'self'  => $links
                        ]
                    ]
                ], 201);
            }
        } catch (Exception $e) {
            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function getByChecklistId(Request $req, $checklistId)
    {
        try {
            $checklist = Checklist::findOrFail($checklistId);
            $links = $req->fullUrl() . '/' . $checklistId;

            return response()->json([
                'data'  => [
                    'type'  => 'checklist',
                    'id'    => $checklistId,
                    'attributes'    => [
                        'object_domain'     => $checklist->object_domain,
                        'object_id'         => $checklist->object_id,
                        'description'       => $checklist->description,
                        'is_completed'      => $checklist->is_completed == 1 ? true : false,
                        'due'               => $checklist->due,
                        'urgency'           => $checklist->urgency,
                        'completed_at'      => $checklist->completed_at,
                        'last_update_by'    => $checklist->updated_by,
                        'update_at'         => $checklist->update_at,
                        'created_at'        => $checklist->created_at,
                    ],
                    'links' => [
                        'self'  => $links
                    ]
                ]
            ], 200);
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

    public function update(Request $req, $checklistId)
    {
        try {
            $checklist = Checklist::findOrFail($checklistId);
            $links = $req->fullUrl() . '/' . $checklistId;

            $validator = Validator::make($req->data['attributes'], [
                'object_domain'     => 'required',
                'object_id'         => 'required',
                'description'       => 'required',
                'due'               => 'required|date_format:Y-m-d H:i:s',
                'urgency'           => 'integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            DB::beginTransaction();
            $type = $req->data['type'];
            $objectDomain = $req->data['attributes']['object_domain'];
            $objectId = $req->data['attributes']['object_id'];
            $description = $req->data['attributes']['description'];
            $due = $req->data['attributes']['due'];
            $urgency = $req->data['attributes']['urgency'];
            $taskId = $req->data['attributes']['task_id'];
            $userId = auth()->user()->id;
            $now = date("Y-m-d H:i:s");
            $isCompleted = $checklist->is_completed;
            $completedAt = $checklist->completed_at;

            $checklist->object_domain = $objectDomain;
            $checklist->object_id = $objectId;
            $checklist->description = $description;
            $checklist->due = $due;
            $checklist->urgency = $urgency;
            $checklist->task_id = $taskId;
            $checklist->updated_by = $userId;
            $checklist->save();

            if (!$checklist->save()) {
                DB::rollBack();
                return response()->json([
                    'status'    => 400,
                    'message'   => 'Failed update checklist'
                ], 400);
            } else {
                $history = new History();
                $history->loggable_type = $type;
                $history->loggable_id = $checklistId;
                $history->action = 'update';
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
                return response()->json([
                    'data' => [
                        'type'  => $type,
                        'id'    => $checklistId,
                        'attributes'    => [
                            'object_domain' => $objectDomain,
                            'object_id'     => $objectId,
                            'task_id'       => $taskId,
                            'description'   => $description,
                            'is_completed'  => $isCompleted,
                            'due'           => $due,
                            'urgency'       => $urgency,
                            'completed_at'  => $completedAt,
                            'update_by'     => $userId,
                            'created_by'    => $checklist->created_by,
                            'created_at'    => $checklist->created_at,
                            'updated_at'    => $now
                        ],
                        'links' => [
                            'self'  => $links
                        ]
                    ]
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

    public function delete($checklistId)
    {
        try {
            $checklist = Checklist::findOrFail($checklistId);

            $history = new History();
            $history->loggable_type = 'checklists';
            $history->loggable_id = $checklist->id;
            $history->action = 'delete';
            $history->value = json_encode($checklist);
            $history->created_by = auth()->user()->id;
            $history->save();

            $checklist->delete();

            return response()->json("", 204);
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

    public function getList(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'include'       => 'in:items|nullable',
                'page.limit'    => 'nullable',
                'page.offset'   => 'nullable'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $pageLimit = empty($req->page['limit']) ? 10 : $req->page['limit'];
            $pageOffset = empty($req->page['offset']) ? 0 : $req->page['offset'];
            $getLinks = $req->url();
            $total = Checklist::count();
            $checklists = Checklist::with('updatedBy')->skip($pageOffset)->take($pageLimit)->get();
            $data = array();
            $include = $req->include;

            foreach ($checklists as $checklist) {

                $firstPageOffset = 0;
                $lastPageOffset = $total - $pageLimit;

                $firstLink = $getLinks . '?page[limit]=' . $pageLimit . '&page[offset]=' . $firstPageOffset;
                $lastLink = $getLinks . '?page[limit]=' . $pageLimit . '&page[offset]=' . $lastPageOffset;

                if ($pageOffset == 0) {
                    $prevLink = null;
                } else {
                    $prevPageOffset = $pageOffset - $pageLimit;
                    $prevLink = $getLinks . '?page[limit]=' . $pageLimit . '&page[offset]=' . $prevPageOffset;
                }

                if ($pageOffset == $lastPageOffset) {
                    $nextLink = null;
                } else {
                    $nextPageOffset = $pageLimit + $pageOffset;
                    $nextLink = $getLinks . '?page[limit]=' . $pageLimit . '&page[offset]=' . $nextPageOffset;
                }

                $type = 'checklists';
                $checklistId = $checklist->id;
                $description = $checklist->description;
                $isCompleted = $checklist->is_completed == 0 ? false : true;
                $due = $checklist->due;
                $taskId = $checklist->task_id;
                $urgency = $checklist->urgency;
                $completedAt = $checklist->completed_at;
                $updatedBy = $checklist->updated_by;
                $updatedAt = $checklist->updated_at;
                $createdAt = $checklist->created_at;
                $selfLink = $req->url() . '/' . $checklistId;
                $objectDomain = $checklist->object_domain;
                $objectId = $checklist->object_id;

                $attributes = array(
                    'object_domain'     => $objectDomain,
                    'object_id'         => $objectId,
                    'description'       => $description,
                    'is_completed'      => $isCompleted,
                    'due'               => $due,
                    'task_id'           => $taskId,
                    'urgency'           => $urgency,
                    'completed_at'      => $completedAt,
                    'last_update_by'    => $updatedBy,
                    'update_at'         => $updatedAt,
                    'created_at'        => $createdAt
                );

                $selfLink = array(
                    'self'  => $selfLink
                );

                $data[] = array(
                    'type'          => $type,
                    'id'            => $checklistId,
                    'attributes'    => $attributes,
                    'links'         => $selfLink
                );

                if ($include == 'items') {
                    $checklistitems = ChecklistItem::where('checklist_id', $checklistId)->with('updatedBy')->get();

                    foreach ($checklistitems as $item) {
                        $typeItem = 'items';
                        $itemId = $item->id;
                        $descriptionItem = $item->description;
                        $isCompletedItem = $item->is_completed == 0 ? false : true;
                        $completedAtItem = $item->due;
                        $dueItem = $item->due;
                        $urgencyItem = $item->urgency;
                        $updateByItem = $item->updated_by;
                        $deletedAtItem = $item->deleted_at;
                        $createdAtItem = $item->created_at;
                        $updatedAtItem = $item->updated_at;
                        $linkSelfItem = url() . '/items/' . $itemId;

                        $attributesItem = array(
                            'description'   => $descriptionItem,
                            'is_completed'  => $isCompletedItem,
                            'complete_at'   => $completedAtItem,
                            'due'           => $dueItem,
                            'urgency'       => $urgencyItem,
                            'updated_by'    => $updateByItem,
                            'checklist_id'  => $checklistId,
                            'deleted_at'    => $deletedAtItem,
                            'created_at'    => $createdAtItem,
                            'updated_at'    => $updatedAtItem
                        );

                        $linkSelfItem = array(
                            'self'  => $linkSelfItem
                        );

                        $included[] = array(
                            'type'          => $typeItem,
                            'id'            => $itemId,
                            'attributes'    => $attributesItem,
                            'links'         => $linkSelfItem
                        );
                    }
                }
            }

            $meta = array(
                'count' => $pageLimit,
                'total' => $total
            );

            $links = array(
                'first' => $firstLink,
                'last'  => $lastLink,
                'next'  => $nextLink,
                'prev'  => $prevLink
            );

            $response = array(
                'meta'  => $meta,
                'links' => $links,
                'data'  => $data,
            );

            if (!empty($included)) {
                $response = array(
                    'meta'      => $meta,
                    'links'     => $links,
                    'data'      => $data,
                    'included'  => $included
                );
            } else {
                $response = array(
                    'meta'      => $meta,
                    'links'     => $links,
                    'data'      => $data
                );
            }

            return response()->json([
                $response
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }
}
