<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Checklist;
use Illuminate\Support\Facades\DB;
use App\Models\ChecklistItem;
use App\Models\Template;
use App\Models\History;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChecklistTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $req)
    {
        try {

            $attributes = $req->data['attributes'];
            $type = 'template';
            $action = 'create';

            $validator = Validator::make($attributes, [
                'name'                      => 'required',
                'checklist.description'     => 'required',
                'checklist.due_unit'        => 'in:minute,hour,day,week,month',
                'checklist.due_interval'    => 'integer',
                'items.*.description'       => 'required',
                'items.*.due_unit'          => 'in:minute,hour,day,week,month',
                'items.*.due_interval'      => 'integer',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $now = Carbon::now();
            $checklistDescription = $attributes['checklist']['description'];
            $checklistDueUnit = $attributes['checklist']['due_unit'];
            $checklistDueInterval = $attributes['checklist']['due_interval'];
            $userId = auth()->user()->id;

            if ($checklistDueUnit == "minute") {

                $checklistDue = $now->addMinutes($checklistDueInterval);
                $checklistDue = Carbon::parse($checklistDue)->format('Y-m-d H:i:s');
            } else if ($checklistDueUnit == "hour") {

                $checklistDue = $now->addHour($checklistDueInterval);
                $checklistDue = Carbon::parse($checklistDue)->format('Y-m-d H:i:s');
            } else if ($checklistDueUnit == "day") {

                $checklistDue = $now->addDays($checklistDueInterval);
                $checklistDue = Carbon::parse($checklistDue)->format('Y-m-d H:i:s');
            } else if ($checklistDueUnit == "day") {

                $checklistDue = $now->addMonths($checklistDueInterval);
                $checklistDue = Carbon::parse($checklistDue)->format('Y-m-d H:i:s');
            }

            DB::beginTransaction();

            $checklist = new Checklist();
            $checklist->description = $checklistDescription;
            $checklist->due = $checklistDue;
            $checklist->created_by = $userId;
            $checklist->save();

            if (!$checklist->save()) {

                DB::rollBack();
                return response()->json([
                    'status'    => 400,
                    'error'     => 'Failed insert checklist'
                ], 400);
            }

            foreach ($attributes['items'] as $item) {

                $itemDueUnit = $item['due_unit'];
                $itemDueInterval = $item['due_interval'];
                $itemDescription = $item['description'];

                if ($itemDueUnit == "minute") {

                    $itemsDue = $now->addMinutes($itemDueInterval);
                    $itemsDue = Carbon::parse($itemsDue)->format('Y-m-d H:i:s');
                } else if ($itemDueUnit == "hour") {

                    $itemsDue = $now->addHour($itemDueInterval);
                    $itemsDue = Carbon::parse($itemsDue)->format('Y-m-d H:i:s');
                } else if ($itemDueUnit == "day") {

                    $itemsDue = $now->addDays($itemDueInterval);
                    $itemsDue = Carbon::parse($itemsDue)->format('Y-m-d H:i:s');
                } else if ($itemDueUnit == "day") {

                    $itemsDue = $now->addMonths($itemDueInterval);
                    $itemsDue = Carbon::parse($itemsDue)->format('Y-m-d H:i:s');
                }

                $item = new ChecklistItem();
                $item->checklist_id = $checklist->id;
                $item->description = $itemDescription;
                $item->due = $itemsDue;
                $item->created_by = $userId;
                $item->save();

                if (!$item->save()) {

                    DB::rollBack();
                    return response()->json([
                        'status'    => 400,
                        'error'     => 'Failed insert checklist items'
                    ], 400);
                }
            }

            $template = new Template();
            $template->checklist_id = $checklist->id;
            $template->name = $attributes['name'];
            $template->created_by = $userId;
            $template->save();

            if (!$template->save()) {

                DB::rollBack();
                return response()->json([
                    'status'    => 400,
                    'error'     => 'Failed insert template'
                ], 400);
            }

            $history = new History();
            $history->loggable_type = $type;
            $history->loggable_id = $template->id;
            $history->action = $action;
            $history->value = json_encode($req->all());
            $history->created_by = $userId;
            $history->save();

            if (!$history->save()) {

                DB::rollBack();
                return response()->json([
                    'status'    => 400,
                    'error'     => 'Failed insert history'
                ], 400);
            }

            
            $response = $req->all();
            DB::commit();
            
            return response()->json($response, 201);
        } catch (Exception $e) {

            return response()->json([
                'status'    => 500,
                'error'     => $e->getMessage()
            ], 500);
        }
    }
}
