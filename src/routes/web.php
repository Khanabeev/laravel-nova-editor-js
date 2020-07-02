<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {

    /** Editor */
Route::post('/editor/upload/file', 'ImageUploadController@file')
    ->name('editor-upload-image-by-file');
Route::post('/editor/upload/url', 'Editor\ImageUploadController@url')
    ->name('editor-upload-image-by-url');

Route::get('/editor/video/url', 'App\Http\Controllers\Editor\VideoController@checkUrl')
    ->name('editor-video-check-url');

});


