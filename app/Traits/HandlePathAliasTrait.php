<?php

namespace App\Traits;

use App\Models\PathAlias;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait HandlePathAliasTrait
{
    public function createPathAlias($alias)
    {
        $path = $this->getOriginalPath();

        return PathAlias::updateOrCreate(
            ['path' => $path],
            [
                'alias' => $alias,
                'status' => true
            ]
        );
    }

    abstract public function getOriginalPath(): string;
}
