<?php

namespace App\Http\Controllers;

use App\DTOs\BaseDto;
use App\DTOs\BaseDtoStatusEnum;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;

use App\Models\Article;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Spatie\QueryBuilder\QueryBuilder;

class UserArticleController extends Controller
{
    public function index(Request $request)
    {

        $articles = QueryBuilder::for(Article::owner())
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
        return response()->json($articles, 200);
//        $tickets = QueryBuilder::for(Ticket::class)
//            ->allowedFilters(['is_locked'])
//            ->defaultSort('created_at')
//            ->allowedSorts(['title', 'is_locked', 'created_at'])
//            ->paginate();

//        return new ArticleCollection($article);
        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"articles successful get",
            new ArticleCollection($articles))]);
    }


    public function show(Request $request, $article_id)
    {
        $article = Article::owner()->find($article_id);

        if(is_null($article)){
            return "article not found";
        }

        return new ArticleResource($article);
    }

    public function store(StoreArticleRequest $request)
    {
        $file_img = new FileService('public');
        $file_content = new FileService('public');
        $validated = $request->validated();

        $imgPath = $file_img->upload($validated['img'], 'images/article-images');

        // $contentPath = $file_content->upload($validated['content'], 'article-contents');

        $article = Auth::user()->articles()->create([
            'title' => $validated['title'],
            'img' => $imgPath,
            'content' =>$validated['content'],
            'creator_name' => Auth::user()->name,
            ]);

        if(key_exists('categories', $validated)){
            $article->categories()->attach($validated['categories']);
        }

        $article->status = 'waiting';
//        return new ArticleResource($article);
        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"articles successful create",
            new ArticleResource($article))]);
    }


    public function update(UpdateArticleRequest $request, $article_id)
    {
        $article = Article::owner()->find($article_id);

        if(is_null($article)){
            return "article not found";
        }

        $validated = $request->validated();

        if (key_exists('content', $validated)) {
            $file_content = new FileService('public');
            $contentPath = $file_content->update($validated, $article->content, 'article-contents');
            $article->content = $contentPath;
        }

        if (key_exists('img', $validated)) {
            $file_img = new FileService('public');
            $imgPath = $file_img->update($validated, $article->img, 'images/article-images');
            $article->img = $imgPath;
        }

        $article->title = $validated['title'];
        if(key_exists('categories', $validated)) {
            $article->categories()->sync($validated['categories']);
        } else {
            $article->categories()->sync(null);
        }
        $article->status = 'waiting';
        $article->save();

        return response()->json([new BaseDto(BaseDtoStatusEnum::OK,"articles successful updated",
            new ArticleResource($article))]);
//        return new ArticleResource($article);
    }

    public function destroy(Request $request, $article_id)
    {
        $article = Article::owner()->find($article_id);

        if(is_null($article)){
            return "article not found";
        }

        $article->delete();

        return response()->noContent();
    }
}
