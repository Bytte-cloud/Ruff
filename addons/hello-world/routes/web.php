<?php

use Illuminate\Support\Facades\Route;

/*
| Routes for the Hello World example addon. These are only registered while the
| addon is enabled. This one returns JSON, so it needs no session/CSRF middleware;
| wrap routes in Route::middleware('web')->group(...) if they render views/forms.
*/
Route::get('/addons/hello-world', function () {
    return response()->json([
        'addon' => 'hello-world',
        'message' => 'Hello from the Hello World addon!',
    ]);
});
