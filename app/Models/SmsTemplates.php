<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplates extends Model
{

    protected $fillable = ['title', 'slug', 'content'];


    use HasFactory;
}
