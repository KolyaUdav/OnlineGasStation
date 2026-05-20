<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Promotions\Index as PromotionsIndex;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/promotions', PromotionsIndex::class);
