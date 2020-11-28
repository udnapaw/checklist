<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $table = 'checklists';    
    protected $fillable = [
        'description', 'due', 'urgency', 'object_id', 'object_domain', 'is_completed', 'completed_at', 'task_id', 'created_by', 'updated_by', 'due_interval', 'due_unit'
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

    public function items()
    {
        return $this->hasMany('App\Models\ChecklistItem');
    }

}