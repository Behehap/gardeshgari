<?php

namespace App\Http\Controllers;

use App\DTOs\BaseDto;
use App\DTOs\BaseDtoStatusEnum;
use App\Http\Requests\UpdateAdminArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\FileService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AdminArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = QueryBuilder::for(Article::class)
            // جستجوی عنوان با استفاده از regex و نادیده گرفتن در صورت خالی بودن
            ->when($request->filled('title'), function ($query) use ($request) {
                $title = $request->input('title');
                $query->where('title', 'REGEXP', "[[:<:]]" . $title . "[[:>:]]");
            })
            // فیلتر بر اساس دسته‌بندی‌ها با OR
            ->when($request->filled('categories'), function ($query) use ($request) {
                $categoryIds = $request->input('categories');
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            })
            ->get();
//        return new ArticleCollection(Article::all());
        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"articles successful get",
            new ArticleCollection($articles))]);
    }

    public function show(Request $request, Article $article)
    {
        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"article successful get",
            new ArticleResource($article))]);
    }

    public function update(UpdateAdminArticleRequest $request, Article $article)
    {

        $validated = $request->validated();
        $article->status = $validated['status'];
        $article->save();
        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"article updated",
            new ArticleResource($article))]);
    }

    public function destroy(Request $request, Article $article)
    {
        $article->delete();
        return response()->json(BaseDtoStatusEnum::OK, "article deleted");
    }
}

