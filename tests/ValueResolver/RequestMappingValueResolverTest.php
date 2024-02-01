<?php

namespace HalloVerden\RequestMappingBundle\Tests\ValueResolver;

use HalloVerden\RequestMappingBundle\Attribute\MapRequestPayload;
use HalloVerden\RequestMappingBundle\Attribute\MapRequestQuery;
use HalloVerden\RequestMappingBundle\Handler\RequestDataHandlerInterface;
use HalloVerden\RequestMappingBundle\ValueResolver\RequestMappingValueResolver;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Contracts\Service\ServiceProviderInterface;

class RequestMappingValueResolverTest extends TestCase {

  /**
   * @throws Exception
   */
  public function testResolve_mapQuery_shouldReturnQuery(): void {
    // Arrange
    $handler = $this->createMock(RequestDataHandlerInterface::class);
    $handler->method('handle')->willReturnArgument(0);

    $handlers = $this->createMock(ServiceProviderInterface::class);
    $handlers->method('get')->willReturn($handler);

    $valueResolver = new RequestMappingValueResolver($handlers);

    $query = ['test' => 'value'];
    $request = new Request($query);

    $attribute = new MapRequestQuery(handler: 'test');
    $argument = new ArgumentMetadata('test', 'array', false, false, null, false, [$attribute]);

    // Act
    $result = $valueResolver->resolve($request, $argument);

    // Assert
    $this->assertCount(1, $result);
    $this->assertEquals($query, $result[0]);
  }

  /**
   * @throws Exception
   */
  public function testResolve_mapPayload_shouldReturnPayload(): void {
    // Arrange
    $handler = $this->createMock(RequestDataHandlerInterface::class);
    $handler->method('handle')->willReturnArgument(0);

    $handlers = $this->createMock(ServiceProviderInterface::class);
    $handlers->method('get')->willReturn($handler);

    $valueResolver = new RequestMappingValueResolver($handlers);

    $payload = ['test' => 'value'];
    $request = new Request(content: json_encode($payload));

    $attribute = new MapRequestPayload(handler: 'test');
    $argument = new ArgumentMetadata('test', 'array', false, false, null, false, [$attribute]);

    // Act
    $result = $valueResolver->resolve($request, $argument);

    // Assert
    $this->assertCount(1, $result);
    $this->assertEquals($payload, $result[0]);
  }

}
