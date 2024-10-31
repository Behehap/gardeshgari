<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleImage;
use App\Services\FileService;
use Illuminate\Http\Request;

class ArticleImageController extends Controller
{
    public function upload(Request $request, $articleId)
    {
        $file_service = new FileService('public');
        $validated = $request->validate(['image' => 'sometimes|mimes:jpeg,png|max:1024']);
        $article = Article::owner()->findOrFail($articleId);

        $path = $file_service->upload($validated['image'], 'article_images');

        $article->images()->create(['path' => $path]);

        return response()->json(['message' => 'Image uploaded successfully.', 'path' => $path]);
    }

    // حذف تصویر از مقاله
    public function delete($id)
    {
        $image = ArticleImage::whereHas('article', function ($query) {
            $query->owner();
        })->findOrFail($id);

        $file_service = new FileService('public');
        $file_service->delete($image->path);
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    // تعویض تصویر
    public function replace(Request $request, $id)
    {
        $validated = $request->validate(['image' => 'sometimes|mimes:jpeg,png|max:1024']);

        $image = ArticleImage::whereHas('article', function ($query) {
            $query->owner();
        })->findOrFail($id);

        $file_service = new FileService('public');
        $newPath = $file_service->update($validated['image'], $image->path, 'article_images');

        $image->update(['path' => $newPath]);

        return response()->json(['message' => 'Image replaced successfully.', 'path' => $newPath]);
    }
}
