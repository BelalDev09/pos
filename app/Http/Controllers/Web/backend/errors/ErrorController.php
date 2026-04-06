<?php

namespace App\Http\Controllers\Web\backend\errors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ErrorController extends Controller
{
    /**
     * Show 403 Forbidden
     */
    public function forbidden()
    {
        return response()->view('backend.layout.errors.custom-403', [], 403);
    }

    /**
     * Show 404 Not Found
     */
    public function notFound()
    {
        return response()->view('backend.layout.errors.custom-404', [], 404);
    }

    /**
     * Show 500 Internal Server Error
     */
    public function internalError()
    {
        return response()->view('backend.layout.errors.custom-500', [], 500);
    }

    /**
     * Show offline/maintenance page
     */
    public function offline()
    {
        return response()->view('backend.layout.errors.offline', [], 503);
    }
}
