<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);

        return $this->collection->map(function ($article) {
            $file_service = new FileService();
            return [
                'id' => $article->id,
                'title' => $article->title,
                'img' => $file_service->read($article->img),
                'creator_id' => $article->creator_id,
                'sender_name' => $article->creator_name,
                'status' => $article->status,
                'created_at' => $article->created_at,
                'categories' => $article->categories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                }),
            ];
        })->toArray();
    }
}
