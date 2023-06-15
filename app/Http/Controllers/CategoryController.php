<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HasApiResponse;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->paginate(request('per_page', config('settings.per_page')));

        $data = fractal($categories, new CategoryTransformer())
            ->parseIncludes(request('includes'))
            ->parseIncludes(request('excludes'))
            ->toArray();

        return $this->respondSuccess(
            data: $data
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        $data = fractal($category, new CategoryTransformer())
            ->parseIncludes(request('includes'))
            ->parseIncludes(request('excludes'))
            ->toArray();

        return $this->respondSuccess(
            data: $data
        );
    }
}
