<?php

namespace App\Http\Controllers;

use App\Services\OraResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OraChatController extends Controller
{
    public function __invoke(Request $request, OraResponder $oraResponder): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        return response()->json($oraResponder->respond($validated['message']));
    }
}
