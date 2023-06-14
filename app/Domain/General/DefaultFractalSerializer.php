<?php

namespace App\Domain\General;

use Spatie\Fractalistic\ArraySerializer;

/**
 * Class DefaultSerializer
 * @package App\Library
 */
class DefaultFractalSerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data): array
    {
        if ($resourceKey) {
            return $resourceKey == 'include' ? $data : [$resourceKey => $data];
        }
        return ['data' => $data];
    }

    public function item($resourceKey, array $data): array
    {
        if ($resourceKey) {
            return $resourceKey == 'include' ? $data : [$resourceKey => $data];
        }
        return $data;
    }

}
