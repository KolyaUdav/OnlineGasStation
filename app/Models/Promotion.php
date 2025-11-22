<?php

namespace App\Models;

class Promotion extends BaseModel
{
    const FIELD_TITLE = 'title';
    const FIELD_TEXT = 'text';
    const FIELD_SALE_PERCENT = 'sale_percent';
    const FIELD_DATE_START = 'date_start';
    const FIELD_DATE_END = 'date_end';
    
    public $timestamps = false;

    protected $guarded = ['id'];
}
