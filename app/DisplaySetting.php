<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisplaySetting extends Model
{
    protected $fillable = ['screen_type', 'type', 'value'];

    public function scopeForScreen($query, $screenType)
    {
        return $query->where('screen_type', $screenType);
    }
}
