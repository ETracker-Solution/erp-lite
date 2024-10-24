<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginateResource extends ResourceCollection
{

    public function __construct($resource, $resource_class)
    {
        $this->collects = $resource_class;
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'base' => $this->url(1),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page_number' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total()
            ]
        ];
    }
}
