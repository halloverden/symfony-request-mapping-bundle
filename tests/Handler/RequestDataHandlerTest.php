<?php

namespace HalloVerden\RequestMappingBundle\Tests\Handler;

use HalloVerden\RequestMappingBundle\Handler\AbstractRequestDataHandler;
use JMS\Serializer\ArrayTransformerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDataHandlerTest extends TestCase {

  /**
   * @throws Exception
   */
  public function testHandle_noData_shouldReturnRequestPayload(): void {
    // Arrange
    $validator = $this->createMock(ValidatorInterface::class);
    $validator->method('validate')->willReturn(new ConstraintViolationList());

    $serializer = $this->createMock(ArrayTransformerInterface::class);
    $serializer->method('fromArray')->willReturn(new RequestPayloadMock());

    $requestDataHandler = new class($validator, $serializer) extends AbstractRequestDataHandler {};

    // Act
    $requestPayloadObject = $requestDataHandler->handle(
      [],
      new ArgumentMetadata('test', RequestPayloadMock::class, false, false, null)
    );

    // Assert
    $this->assertInstanceOf(RequestPayloadMock::class, $requestPayloadObject);
  }

  /**
   * @throws Exception
   */
  public function testHandle_allowedProperties_shouldReturnFilteredData(): void {
    // Arrange
    $validator = $this->createMock(ValidatorInterface::class);
    $validator->method('validate')->willReturn(new ConstraintViolationList());

    $serializer = $this->createMock(ArrayTransformerInterface::class);
    $serializer->method('fromArray')->willReturnArgument(0);

    $requestDataHandler = new class($validator, $serializer) extends AbstractRequestDataHandler {
      protected function getAllowedProperties(): ?array {
        return ['allowed'];
      }
    };

    // Act
    $data = $requestDataHandler->handle(
      ['allowed' => 'test', 'notAllowed' => 'test2'],
      new ArgumentMetadata('test', 'array', false, false, null)
    );

    // Assert
    $this->assertIsArray($data);
    $this->assertArrayHasKey('allowed', $data);
    $this->assertArrayNotHasKey('notAllowed', $data);
  }

  /**
   * @throws Exception
   */
  public function testHandle_nestedAllowedProperties_shouldReturnFilteredData(): void {
    // Arrange
    $validator = $this->createMock(ValidatorInterface::class);
    $validator->method('validate')->willReturn(new ConstraintViolationList());

    $serializer = $this->createMock(ArrayTransformerInterface::class);
    $serializer->method('fromArray')->willReturnArgument(0);

    $requestDataHandler = new class($validator, $serializer) extends AbstractRequestDataHandler {
      protected function getAllowedProperties(): ?array {
        return ['nested' => ['allowed']];
      }
    };

    // Act
    $data = $requestDataHandler->handle(
      ['nested' => ['allowed' => 'test', 'notAllowed' => 'test2']],
      new ArgumentMetadata('test', 'array', false, false, null)
    );

    // Assert
    $this->assertIsArray($data);
    $this->assertArrayHasKey('nested', $data);
    $this->assertArrayHasKey('allowed', $data['nested']);
    $this->assertArrayNotHasKey('notAllowed', $data['nested']);
  }

  /**
   * @throws Exception
   */
  public function testHandle_collectionConstraintFields_shouldReturnFilteredData(): void {
    // Arrange
    $validator = $this->createMock(ValidatorInterface::class);
    $validator->method('validate')->willReturn(new ConstraintViolationList());

    $serializer = $this->createMock(ArrayTransformerInterface::class);
    $serializer->method('fromArray')->willReturnArgument(0);

    $requestDataHandler = new class($validator, $serializer) extends AbstractRequestDataHandler {
      protected function getCollectionConstraintFields(): array {
        return [
          'allowed' => [new NotBlank()],
        ];
      }
    };

    // Act
    $data = $requestDataHandler->handle(
      ['allowed' => 'test', 'notAllowed' => 'test2'],
      new ArgumentMetadata('test', 'array', false, false, null)
    );

    // Assert
    $this->assertIsArray($data);
    $this->assertArrayHasKey('allowed', $data);
    $this->assertArrayNotHasKey('notAllowed', $data);
  }

}
