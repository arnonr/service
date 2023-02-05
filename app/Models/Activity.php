<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'activity';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'detail',
        'start_date',
        'end_date',
        'activity_file',
        'mou_id',
        'is_publish',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];

    // public function host()
    // {
    //     return  $this->belongsTo(Host::Class, 'host_id', 'id');
    // }
}
