<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $file_service = new FileService();
        $img = !(is_null($this->image_profile_path)) ? $file_service->read($this->image_profile_path) : null;
        return [
          'role' => $this->role,
          'name' => $this->name,
          'username' => $this->username,
          'phone' => Auth::user()->phone,
          'image' => $img
        ];
    }
}
