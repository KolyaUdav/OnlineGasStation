<?php

namespace App\Models;

use App\Enums\Fuels;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends BaseModel
{
    /**
     * Оставлю констаты, чтобы не поломать ничего, но больше не прописываем такое
     */
    const FIELD_FUEL_NAME = 'fuel_name';
    const FIELD_FUEL_TYPE = 'fuel_type';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_COST_IN_TIME = 'cost_in_time';
    const FIELD_COST = 'cost';
    const FIELD_USER_ID = 'user_id';
    const FIELD_CHECK_PATH = 'check_path';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    const FIELD_USER = 'user';

    protected $guarded = ['id'];

    protected $casts = [
        'fuel_type' => Fuels::class,
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::FIELD_USER_ID);
    }
}
