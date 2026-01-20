<?php

namespace Tests;

use App\Enum\NewsSource;
use App\Services\News\Data\NewsResourceCollection;
use App\Services\News\NewsSourceFactory;
use App\Services\News\NewsSourceInterface;
use Illuminate\Container\RewindableGenerator;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function fakeNewsFactory(): void
    {
        $mockSourceFactory = $this->getMockNewsFactory();

        $this->app->instance(NewsSourceFactory::class, $mockSourceFactory);
    }

    protected function getMockNewsFactory(): NewsSourceFactory
    {
        return new NewsSourceFactory($this->getMockSources());
    }

    private function getMockSources(): RewindableGenerator
    {
        return new RewindableGenerator(function () {
            foreach (NewsSource::cases() as $source) {
                yield $this->mockNewsSource($source);
            }
        }, count(NewsSource::cases()));
    }

    private function mockNewsSource(NewsSource $source, array $data = []): NewsSourceInterface
    {
        $mock = $this->createMock(NewsSourceInterface::class);
        $mock->method('getType')->willReturn($source);
        $mock->method('fetch')->willReturn(new NewsResourceCollection($data));

        return $mock;
    }
}
