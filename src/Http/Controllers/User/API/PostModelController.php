<?php

namespace Weblebby\Framework\Http\Controllers\User\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Weblebby\Framework\Facades\PostModels;

class PostModelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $model = PostModels::find($request->input('model'));
        abort_if(is_null($model), 404);

        return response()->json($model::getTaxonomies());
    }
}
