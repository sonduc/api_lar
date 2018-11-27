<?php

namespace App\Http\Controllers\Api;

class LanguageController extends ApiController
{

    /**
     * Get all languages from config file
     * @return json response
     */
    public function index()
    {
        $languages = config('languages');
        return response()->json($languages, 200);
    }
}
