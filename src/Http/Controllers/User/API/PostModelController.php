<?php

namespace Feadmin\Http\Controllers\User\API;

use App\Http\Controllers\Controller;
use Feadmin\Facades\PostModels;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostModelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $model = PostModels::find($request->input('model'));
        abort_if(is_null($model), 404);

        return response()->json($model::getTaxonomies());
    }
}
