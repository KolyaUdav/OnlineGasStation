<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;

class PromotionController extends BaseController
{
    const DEFAULT_MESSAGE_ERROR = 'Promotion error';
    const DEFAULT_MESSAGE_SUCCESS = 'Promotion success';

    const CONF_MSG_KEY_SUCCESS = 'promotion';
    const CONF_MSG_KEY_ERROR = 'promotion';

    protected $model = Promotion::class;

    public function create(PromotionRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($createdPromotion = Promotion::apiAdd($data)) {
            return $this->success(['data' => $createdPromotion]);
        }

        return $this->error($this->getErrorMessage('not_created'), 500);
    }
}
