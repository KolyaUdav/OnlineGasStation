<?php

namespace App\Models;

class Promotion extends BaseModel
{
    const FIELD_TITLE = 'title';
    const FIELD_TEXT = 'text';
    const FIELD_SALE_PERCENT = 'sale_percent';
    const FIELD_DATE_START = 'date_start';
    const FIELD_DATE_END = 'date_end';
    const FIELD_CONDITIONS = 'conditions';

    const RULE_MIN_BALANCE = 'min_balance';
    const RULE_MIN_REG_DATE = 'min_reg_date';
    const RULE_FUEL_TYPES = 'fuel_types';
    const RULE_MIN_ORDER_SUM = 'min_order_sum';
    
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'conditions' => 'json'
    ];
}
