<?php

namespace HalloVerden\RequestMappingBundle\Helper;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Existence;

final readonly class CollectionConstraintHelper {
  private function __construct() {
  }

  /**
   * @param Collection $collection
   *
   * @return array
   */
  public static function getFields(Collection $collection): array {
    $fields = [];

    foreach ($collection->fields as $field => $constraint) {
      if (\is_array($constraint) && $nestedCollection = self::getCollectionConstraint($constraint)) {
        $fields[$field] = self::getFields($nestedCollection);
      } elseif ($constraint instanceof Existence && $nestedCollection = self::getCollectionConstraint($constraint->constraints)) {
        $fields[$field] = self::getFields($nestedCollection);
      } elseif ($constraint instanceof Collection) {
        $fields[$field] = self::getFields($constraint);
      } else {
        $fields[] = $field;
      }
    }

    return $fields;
  }

  /**
   * @param array $constraints
   *
   * @return Collection|null
   */
  private static function getCollectionConstraint(array $constraints): ?Collection {
    foreach ($constraints as $constraint) {
      if ($constraint instanceof Collection) {
        return $constraint;
      }
    }

    return null;
  }

}
