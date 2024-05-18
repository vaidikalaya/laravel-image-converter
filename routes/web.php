<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConverterController;
use App\Http\Controllers\ImageConverterController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/convert-images',[ImageConverterController::class,'index'])->name('image-converter');

//Route::post('/convert-images',[ConverterController::class,'index'])->name('image-converter');
