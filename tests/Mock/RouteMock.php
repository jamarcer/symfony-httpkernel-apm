<?php

declare(strict_types=1);

namespace Jamarcer\Tests\APM\Mock;

final class RouteMock
{
    public function getPath(): string
    {
        return 'mock_path';
    }
}