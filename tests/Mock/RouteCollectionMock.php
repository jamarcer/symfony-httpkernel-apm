<?php

declare(strict_types=1);

namespace Jamarcer\Tests\APM\Mock;

final class RouteCollectionMock
{
    public function get(): RouteMock
    {
        return new RouteMock();
    }
}