<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        $file_service = new FileService('public');
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'img' => $this->img,
            'content' => $this->content,
            'create_at' => $this->created_at,
            'creator_name' => $this->creator,
            'categories' => $this->categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            }),
            'images' => $this->images->map(function ($image) use ($file_service) {
                return [
                    'id' => $image->id,
                    'path' => $file_service->read($image->path), // خواندن URL تصویر از FileService
                ];
            }),
        ];
    }
}
