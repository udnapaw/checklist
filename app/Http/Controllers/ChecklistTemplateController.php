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
            $userId = auth()->user()->id;

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

            DB::beginTransaction();
            $checklistDescription = $attributes['checklist']['description'];
            $checklistDueUnit = $attributes['checklist']['due_unit'];
            $checklistDueInterval = $attributes['checklist']['due_interval'];

            $checklist = new Checklist();
            $checklist->description = $checklistDescription;
            $checklist->due_interval = $checklistDueInterval;
            $checklist->due_unit = $checklistDueUnit;
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

                $item = new ChecklistItem();
                $item->checklist_id = $checklist->id;
                $item->description = $itemDescription;
                $item->due_interval = $itemDueInterval;
                $item->due_unit = $itemDueUnit;
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

    public function get($templateId)
    {
        try {

            $type = "templates";
            $template = Template::findOrFail($templateId);

            $items = $template->checklist->items;
            $attributeItems = array();

            foreach ($items as $item) {

                $attributeItems[] = array(
                    'urgency'       => $item->urgency,
                    'due_unit'      => $item->due_unit,
                    'description'   => $item->description,
                    'due_interval'  => $item->due_interval
                );
            }

            $attributeChecklist = array(
                'due_unit'      => $template->checklist->due_unit,
                'description'   => $template->checklist->description,
                'due_interval'  => $template->checklist->due_interval
            );

            $attibutes = array(
                'name'      => $template->name,
                'items'     => $attributeItems,
                'checklist' =>  $attributeChecklist
            );

            $links = array(

                'self'  => route('get.templates', ['templateId' => $templateId])
            );

            $data = array(
                'type'          => $type,
                'id'            => $templateId,
                'attributes'    => $attibutes,
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

    public function update($templateId, Request $req)
    {

        try {

            $type = "templates";
            $template = Template::findOrFail($templateId);
            $action = 'update';
            $userId = auth()->user()->id;
            $attributes = $req->data['attributes'];
            $x = 0;

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

            DB::beginTransaction();

            foreach ($attributes['items'] as $item) {

                $itemDueUnit = $item['due_unit'];
                $itemDueInterval = $item['due_interval'];
                $itemDescription = $item['description'];

                $template->checklist->items[$x]->description = $itemDescription;
                $template->checklist->items[$x]->due_interval = $itemDueInterval;
                $template->checklist->items[$x]->due_unit = $itemDueUnit;
                $template->checklist->items[$x]->updated_by = $userId;
                $template->checklist->items[$x]->save();

                if (!$template->checklist->items[$x]->save()) {

                    DB::rollBack();
                    return response()->json([
                        'status'    => 400,
                        'error'     => 'Failed update checklist items'
                    ], 400);
                }

                $x++;
            }

            $checklistDescription = $attributes['checklist']['description'];
            $checklistDueUnit = $attributes['checklist']['due_unit'];
            $checklistDueInterval = $attributes['checklist']['due_interval'];

            $template->checklist->description = $checklistDescription;
            $template->checklist->due_unit = $checklistDueUnit;
            $template->checklist->due_interval = $checklistDueInterval;
            $template->checklist->updated_by = $userId;
            $template->checklist->save();

            if (!$template->checklist->save()) {

                DB::rollBack();
                return response()->json([
                    'status'    => 400,
                    'error'     => 'Failed update checklist items'
                ], 400);
            }

            $template->name = $attributes['name'];
            $template->updated_by = $userId;
            $template->save();

            if (!$template->save()) {

                DB::rollBack();
                return response()->json([
                    'status'    => 400,
                    'error'     => 'Failed update templates'
                ], 400);
            }

            $history = new History();
            $history->loggable_type = $type;
            $history->loggable_id = $templateId;
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

    public function delete($templateId)
    {
        try {

            $type = "templates";
            $template = Template::findOrFail($templateId);
            $action = 'delete';

            $history = new History();
            $history->loggable_type = $type;
            $history->loggable_id = $templateId;
            $history->action = $action;
            $history->value = json_encode($templateId);
            $history->created_by = auth()->user()->id;
            $history->save();

            $template->checklist->delete();

            return response()->json("", 204);

        }catch (ModelNotFoundException $e) {

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
