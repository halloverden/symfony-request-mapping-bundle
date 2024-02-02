<?php

namespace HalloVerden\RequestMappingBundle\Handler;

use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

interface RequestDataHandlerInterface {

  /**
   * @param array            $data
   * @param ArgumentMetadata $argument
   *
   * @return mixed
   */
  public function handle(array $data, ArgumentMetadata $argument): mixed;

}
