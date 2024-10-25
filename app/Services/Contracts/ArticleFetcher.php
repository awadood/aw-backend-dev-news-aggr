<?php

namespace App\Services\Contracts;

interface ArticleFetcher
{
    public function fetchAndTransform(): array;
}
