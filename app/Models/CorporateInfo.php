<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'business_name',
    'sms_signature'
])]
class CorporateInfo extends Model
{
    protected $table = 'corporate_info';

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
