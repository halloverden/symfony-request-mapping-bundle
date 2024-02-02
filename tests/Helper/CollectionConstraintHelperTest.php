<?php

namespace HalloVerden\RequestMappingBundle\Tests\Helper;

use HalloVerden\RequestMappingBundle\Helper\CollectionConstraintHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class CollectionConstraintHelperTest extends TestCase {

  public function testGetFields_simpleFields_shouldReturnFields(): void {
    // Arrange
    $constraint = new Collection(fields: [
      'test' => [new NotBlank()],
      'test2' => [new NotBlank()],
    ]);

    // Act
    $fields = CollectionConstraintHelper::getFields($constraint);

    // Assert
    $this->assertEquals(['test', 'test2'], $fields);
  }

  public function testGetFields_nestedFields_shouldReturnFields(): void {
    // Arrange
    $constraint = new Collection(fields: [
      'test' => [new NotBlank()],
      'test2' => [new NotBlank()],
      'testNested' => [new NotBlank(), new Collection(fields: [
        'test3' => [new NotBlank()]
      ])]
    ]);

    // Act
    $fields = CollectionConstraintHelper::getFields($constraint);

    // Assert
    $this->assertEquals(['test', 'test2', 'testNested' => ['test3']], $fields);
  }

}
