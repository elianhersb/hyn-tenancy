<?php

namespace App\Http\Resources\Generic;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GenericListCollection extends ResourceCollection
{
    
     /**
     * @OA\Schema(
     *     schema="Pagination",
     *     type="object",
     *     @OA\Property(property="current_page", type="integer"),
     *     @OA\Property(property="last_page", type="integer"),
     *     @OA\Property(property="per_page", type="integer"),
     *     @OA\Property(property="total", type="integer"),
     * )
     */

     // Otros campos de paginación van dentro del schema...

    public $collects = GenericListResource::class;

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
