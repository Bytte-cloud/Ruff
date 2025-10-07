<?php

namespace Ruff\Tests\Unit\Http\Middleware;

use Ruff\Tests\TestCase;
use Ruff\Tests\Traits\Http\RequestMockHelpers;
use Ruff\Tests\Traits\Http\MocksMiddlewareClosure;
use Ruff\Tests\Assertions\MiddlewareAttributeAssertionsTrait;

abstract class MiddlewareTestCase extends TestCase
{
    use MiddlewareAttributeAssertionsTrait;
    use MocksMiddlewareClosure;
    use RequestMockHelpers;

    /**
     * Setup tests with a mocked request object and normal attributes.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->buildRequestMock();
    }
}
