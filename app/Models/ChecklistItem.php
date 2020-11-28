<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;

class ChecklistItem extends Model
{

    protected $table = 'checklistitems';    
    protected $fillable = [
        'checklist_id', 'description', 'due', 'urgency', 'is_completed', 'completed_at', 'deleted_at', 'assignee_id', 'created_by', 'updated_by', 'completed_by', 'due_interval', 'due_unit'
    ];

    protected $attributes = [
        'is_completed' => 0,
    ];
    
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function checklist()
    {
        return $this->belongsTo('App\Models\Checklist', 'checklist_id', 'id');
    }

    public static function queryParamater($paramaters)
    {

        $filter = '';
        $sort = '';
        $limit = $paramaters['limit'];
        $offset = $paramaters['offset'];

        if (!empty($paramaters['filter'])) {
            
            foreach($paramaters['filter'] as $filters) {
                $filter .= $filters;
            }
        }

        if(!empty($paramaters['sort'])) {
            $sort = $paramaters['sort'];
        }

        $filter = Helper::conditionQuery($filter);

        $count = DB::select("SELECT count(ci.id) as total FROM checklistitems ci JOIN checklists c ON ci.checklist_id = c.id $filter");
        $datas = DB::select("SELECT ci.*, c.task_id FROM checklistitems ci JOIN checklists c ON ci.checklist_id = c.id $filter $sort LIMIT $limit OFFSET $offset");
        
        $values = array(
            'total' => $count[0]->total,
            'data'  => $datas
        );

        return $values;

    }
}