<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOriginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
     public function toArray($request)
{
    $data = $this->resource; // the raw array passed to the resource

    return [
        'id' => $data['id'] ?? null,
        'title' => $data['title'] ?? null,
        'status' => $data['status'] ?? null,
        'tags' => $data['tags'] ?? [],

        // Flatten collections
        'collections' => array_map(function ($collection) {
            return [
                'id' => $collection['id'] ?? null,
                'title' => $collection['title'] ?? null,
            ];
        }, array_map(fn($edge) => $edge['node'] ?? [], $data['collections']['edges'] ?? [])),

        // Flatten images (take all src URLs)
        'images' => array_values(array_filter(array_map(
            fn($edge) => $edge['node']['src'] ?? null,
            $data['images']['edges'] ?? []
        ))),

        // Flatten variants (take all prices)
        'variants' => array_map(
            fn($edge) => ['price' => $edge['node']['price'] ?? null],
            $data['variants']['edges'] ?? []
        ),
    ];
}

}
