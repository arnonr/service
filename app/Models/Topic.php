<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{

    use HasFactory;
    protected $table = 'topic';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'color',
        'is_publish',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
    ];
}
