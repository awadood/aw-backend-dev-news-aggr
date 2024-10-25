<?php

namespace App\Services\Contracts;

interface NewsFetcher
{
    public function fetchAndTransform(): array;
}
