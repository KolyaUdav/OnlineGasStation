<?php

namespace App\Models;

use App\Enums\Reports\Statuses;
use App\Enums\Reports\Types;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends BaseModel
{
    use HasUuids;

    const FIELD_USER_ID = 'user_id';
    const FIELD_TYPE = 'type';
    const FIELD_STATUS = 'status';
    const FIELD_PAYLOAD = 'payload';
    const FILE_PATH = 'file_path';
    const FIELD_ERROR_MESSAGE = 'error_message';
    const FIELD_COMPLETED_AT = 'completed_at';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        self::FIELD_STATUS => Statuses::class,
        self::FIELD_TYPE => Types::class,
        self::FIELD_PAYLOAD => 'array',
        self::FIELD_COMPLETED_AT => 'datetime',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, self::FIELD_USER_ID);
    }
}
