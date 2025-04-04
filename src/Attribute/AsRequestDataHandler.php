<?php

namespace HalloVerden\RequestMappingBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AsRequestDataHandler {

  public function __construct(
    public string $type
  ) {
  }

}
