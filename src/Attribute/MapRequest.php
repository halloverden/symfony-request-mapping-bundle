<?php

namespace HalloVerden\RequestMappingBundle\Attribute;

use HalloVerden\RequestMappingBundle\ValueResolver\RequestMappingValueResolver;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

abstract class MapRequest extends ValueResolver {

  /**
   * MapRequest constructor.
   */
  public function __construct(
    public readonly ?string $handler = null
  ) {
    parent::__construct(RequestMappingValueResolver::class);
  }

}
