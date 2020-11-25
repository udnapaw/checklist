<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $table = 'checklistitems';    
    protected $fillable = [
        'checklist_id', 'description', 'due', 'urgency', 'is_completed', 'completed_at', 'deleted_at', 'asignee_id', 'created_by', 'updated_by'
    ];

    protected $attributes = [
        'is_completed' => false,
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
}