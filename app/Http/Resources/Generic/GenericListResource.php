<?php

namespace App\Http\Resources\Generic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenericListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    /**
     * @OA\Schema(
     *     schema="GenericListCollection",
     *     type="object",
     *     @OA\Property(property="data", type="array", @OA\Items()),
     *     @OA\Property(property="meta", ref="#/components/schemas/Pagination")
     * )
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
