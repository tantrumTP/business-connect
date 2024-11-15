<?php

namespace App\Traits;

use App\Models\PathAlias;

trait HandlePathAliasTrait
{
    public function createPathAlias($alias)
    {
        $alias = '/' . trim($alias, '/');
        $path = $this->getOriginalPath();

        return PathAlias::updateOrCreate(
            ['path' => $path],
            [
                'alias' => $alias,
                'status' => true
            ]
        );
    }

    public function getPathAlias()
    {
        $path = $this->getOriginalPath();
        return PathAlias::where('path', $path)->first();
    }

    abstract public function getOriginalPath(): string;
}
