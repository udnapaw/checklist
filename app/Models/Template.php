<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates';    
    protected $fillable = [
        'checklist_id', 'name', 'created_by', 'updated_by'
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