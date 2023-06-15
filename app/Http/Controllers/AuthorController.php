<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Traits\HasApiResponse;
use App\Transformers\AuthorTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $authors = Author::filterBy(request()->all())
            ->paginate(request('per_page', config('settings.per_page')));

        $data = fractal($authors, new AuthorTransformer())
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
    public function show(Author $author): JsonResponse
    {
        $data = fractal($author, new AuthorTransformer())
            ->parseIncludes(request('includes'))
            ->parseIncludes(request('excludes'))
            ->toArray();

        return $this->respondSuccess(
            data: $data
        );
    }
}
