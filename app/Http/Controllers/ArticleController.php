<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Traits\HasApiResponse;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $articles = Article::filterBY(request()->all())
            ->paginate(request('per_page', config('settings.per_page')));

        $data = fractal($articles, new ArticleTransformer())
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
    public function show(Article $article): JsonResponse
    {
        $data = fractal($article, new ArticleTransformer())
            ->parseIncludes(request('includes'))
            ->parseIncludes(request('excludes'))
            ->toArray();

        return $this->respondSuccess(
            data: $data
        );
    }

}
