<?php

declare(strict_types=1);

namespace Jamarcer\Tests\APM\Symfony\Component\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Jamarcer\APM\Symfony\Component\HttpKernel\ElasticAPMSubscriber;
use PHPUnit\Framework\TestCase;
use Jamarcer\Tests\APM\Mock\ControllerMock;
use Jamarcer\Tests\APM\Mock\EventDispatcherMock;
use Jamarcer\Tests\APM\Mock\ReporterMock;
use Jamarcer\Tests\APM\Mock\RouterMock;
use ZoiloMora\ElasticAPM\Configuration\CoreConfiguration;
use ZoiloMora\ElasticAPM\ElasticApmTracer;
use ZoiloMora\ElasticAPM\Pool\Memory\MemoryPoolFactory;

class ElasticAPMSubscriberTest extends TestCase
{
    /** @test */
    public function check_if_apm_subscriber_is_subscribed(): void
    {
        self::assertArrayHasKey(KernelEvents::CONTROLLER, ElasticAPMSubscriber::getSubscribedEvents());
        self::assertArrayHasKey(KernelEvents::REQUEST, ElasticAPMSubscriber::getSubscribedEvents());
        self::assertArrayHasKey(KernelEvents::RESPONSE, ElasticAPMSubscriber::getSubscribedEvents());
        self::assertArrayHasKey(KernelEvents::TERMINATE, ElasticAPMSubscriber::getSubscribedEvents());
    }

    /** @test */
    public function when_request_event_is_received_apm_registered_transaction(): void
    {
        $subscriber = $this->getSubscriber();

        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $event = new RequestEvent($kernel, new Request([], [], ['_route' => 'RouteTest']), 1);

        $dispatcher = new EventDispatcherMock();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event, KernelEvents::REQUEST);

        self::assertEquals(1, $subscriber->countTransactions());
        self::assertEquals(0, $subscriber->countSpans());
    }

    /** @test */
    public function when_controller_event_is_received_apm_registered_span(): void
    {
        $subscriber = $this->getSubscriber(true);

        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $event = new ControllerEvent(
            $kernel,
            new ControllerMock(),
            new Request([], [], ['_route' => 'RouteTest']),
            1
        );

        $dispatcher = new EventDispatcherMock();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event, KernelEvents::CONTROLLER);

        self::assertEquals(0, $subscriber->countTransactions());
        self::assertEquals(1, $subscriber->countSpans());
    }

    /** @no_test */
    public function when_response_event_is_received_apm_not_registered_transaction(): void
    {
        $subscriber = $this->getSubscriber();

        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $event = new ResponseEvent($kernel, new Request([], [], ['_route' => 'RouteTest']), 1, new Response());

        $dispatcher = new EventDispatcherMock();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event, KernelEvents::RESPONSE);

        self::assertEquals(0, $subscriber->countTransactions());
        self::assertEquals(0, $subscriber->countSpans());
    }

    /** @no_test */
    public function when_terminate_event_is_received_apm_not_registered_transaction(): void
    {
        $subscriber = $this->getSubscriber();

        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $event = new TerminateEvent($kernel, new Request([], [], ['_route' => 'RouteTest']), new Response());

        $dispatcher = new EventDispatcherMock();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event, KernelEvents::TERMINATE);

        self::assertEquals(0, $subscriber->countTransactions());
        self::assertEquals(0, $subscriber->countSpans());
    }

    protected function getSubscriber(bool $startTransaction = false): ElasticAPMSubscriber
    {
        $router = new RouterMock();

        $configurator = CoreConfiguration::create(['appName' => 'Test',]);
        $reporter = new ReporterMock();
        $factory = MemoryPoolFactory::create();
        $tracer = new ElasticApmTracer($configurator, $reporter, $factory);
        if ($startTransaction) {
            $tracer->startTransaction('testTransaction', 'testType');
        }

        return new ElasticAPMSubscriber($router, $tracer);
    }
}
