<?php

namespace HalloVerden\RequestMappingBundle\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapRequestFile extends MapRequest {

  /**
   * @inheritDoc
   */
  public function __construct(?string $handler = null, public readonly ?string $key = null) {
    parent::__construct($handler);
  }

}
