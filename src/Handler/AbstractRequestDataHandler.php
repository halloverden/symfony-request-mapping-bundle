<?php

namespace HalloVerden\RequestMappingBundle\Handler;

use HalloVerden\HttpExceptions\Utility\ValidationException;
use HalloVerden\RequestMappingBundle\Helper\CollectionConstraintHelper;
use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\DeserializationContext;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRequestDataHandler implements RequestDataHandlerInterface {
  private Constraint|array|null $constraints = null;

  /**
   * RequestDataHandler constructor.
   */
  public function __construct(
    protected readonly ValidatorInterface        $validator,
    protected readonly ArrayTransformerInterface $serializer
  ) {
  }

  /**
   * @inheritDoc
   */
  public function handle(array $data, ArgumentMetadata $argument): mixed {
    $data = $this->filterData($this->transformData($data), $this->getAllowedProperties());
    $this->validate($data);

    if (!$type = $argument->getType()) {
      return $data;
    }

    return $this->map($data, $type);
  }

  /**
   * @param array $fields
   *
   * @return Collection
   */
  protected static function createCollectionConstraint(array $fields): Collection {
    return new Collection(fields: $fields, allowExtraFields: true);
  }

  /**
   * @param array $data
   *
   * @return array
   */
  protected function transformData(array $data): array {
    return $data;
  }

  /**
   * @param array      $data
   * @param array|null $allowedProperties
   *
   * @return array
   */
  protected function filterData(array $data, ?array $allowedProperties): array {
    if (null === $allowedProperties) {
      return $data;
    }

    $filteredData = [];

    foreach ($data as $key => $value) {
      if (in_array($key, $allowedProperties, true)) {
        $filteredData[$key] = $value;
      } elseif (isset($allowedProperties[$key]) && is_array($allowedProperties[$key]) && is_array($value)) {
        $filteredData[$key] = $this->filterData($value, $allowedProperties[$key]);
      }
    }

    return $filteredData;
  }

  /**
   * @param array $data
   *
   * @return void
   */
  protected function validate(array $data): void {
    $violations = $this->validator->validate($data, $this->getConstraints(), $this->getGroups());

    if (0 !== count($violations)) {
      throw new ValidationException($violations);
    }
  }

  /**
   * @param array  $data
   * @param string $type
   *
   * @return mixed
   */
  protected function map(array $data, string $type): mixed {
    return $this->serializer->fromArray($data, $type, $this->createDeserializationContext());
  }

  /**
   * @return array|null
   */
  protected function getAllowedProperties(): ?array {
    $constraints = $this->getConstraints();
    if ($constraints instanceof Collection) {
      return CollectionConstraintHelper::getFields($constraints);
    }

    return null;
  }

  /**
   * @return Constraint|array|null
   */
  protected function getConstraints(): Constraint|array|null {
    return $this->constraints ??= static::createCollectionConstraint($this->getCollectionConstraintFields());
  }

  /**
   * @return array
   */
  protected function getCollectionConstraintFields(): array {
    return [];
  }

  /**
   * @return string|GroupSequence|array|null
   */
  protected function getGroups(): string|GroupSequence|array|null {
    return null;
  }

  /**
   * @return DeserializationContext|null
   */
  protected function createDeserializationContext(): ?DeserializationContext {
    return null;
  }

}
