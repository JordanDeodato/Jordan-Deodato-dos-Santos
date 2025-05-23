<?php

namespace App\DTOs;

class PaginatedResponseDto
{
    public array $data;
    public array $meta;

    public function __construct(array $data, int $currentPage, int $perPage, int $total, int $lastPage)
    {
        $this->data = $data;
        $this->meta = [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage
        ];
    }
    
    /**
     * Retrieve array of data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Retrieve meta data of pagination
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }
}

