<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventStaticQr extends Model
{
    use HasFactory;
    protected $fillable = ['event_id', 'label', 'code'];
}
