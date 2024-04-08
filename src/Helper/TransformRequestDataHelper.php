<?php

namespace HalloVerden\RequestMappingBundle\Helper;

final readonly class TransformRequestDataHelper {
  private const UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  private const LOWER = '-abcdefghijklmnopqrstuvwxyz';

  private function __construct() {
  }

  /**
   * @param array<string, list<string|null>> $data
   *
   * @return array<string|null>
   */
  public static function transformRequestHeaders(array $data): array {
    return \array_map(fn(array $values) => isset($values[0]) ? (string) $values[0] : null, $data);
  }

  /**
   * @param string $key
   *
   * @return string
   */
  public static function transformHeaderKey(string $key): string {
    return \strtr($key, self::UPPER, self::LOWER);
  }

}
