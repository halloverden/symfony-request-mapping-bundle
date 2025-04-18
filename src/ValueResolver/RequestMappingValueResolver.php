<?php

namespace HalloVerden\RequestMappingBundle\ValueResolver;

use HalloVerden\RequestMappingBundle\Attribute\MapRequest;
use HalloVerden\RequestMappingBundle\Attribute\MapRequestFile;
use HalloVerden\RequestMappingBundle\Attribute\MapRequestHeaders;
use HalloVerden\RequestMappingBundle\Attribute\MapRequestPayload;
use HalloVerden\RequestMappingBundle\Attribute\MapRequestQuery;
use HalloVerden\RequestMappingBundle\Handler\RequestDataHandlerInterface;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Contracts\Service\ServiceProviderInterface;

final readonly class RequestMappingValueResolver implements ValueResolverInterface {

  /**
   * RequestMappingValueResolver constructor.
   *
   * @param ServiceProviderInterface<RequestDataHandlerInterface> $handlers
   */
  public function __construct(
    private ServiceProviderInterface $handlers
  ) {
  }

  /**
   * @inheritDoc
   */
  public function resolve(Request $request, ArgumentMetadata $argument): iterable {
    if (!$attribute = $argument->getAttributesOfType(MapRequest::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null) {
      return [];
    }

    if ($argument->isVariadic()) {
      throw new \LogicException(sprintf('Mapping variadic argument "$%s" is not supported.', $argument->getName()));
    }

    $data = $this->getData($request, $attribute, $argument);

    $handler = $this->handlers->get($attribute->handler ?? $argument->getType() ?? throw new \RuntimeException('Handler not found'));
    return [$handler->handle($data, $argument)];
  }

  /**
   * @param Request          $request
   * @param MapRequest       $mapRequestAttribute
   * @param ArgumentMetadata $argument
   *
   * @return array
   */
  private function getData(Request $request, MapRequest $mapRequestAttribute, ArgumentMetadata $argument): array {
    if ($mapRequestAttribute instanceof MapRequestPayload) {
      try {
        return $request->getPayload()->all();
      } catch (JsonException) {
        return [];
      }
    }

    if ($mapRequestAttribute instanceof MapRequestQuery) {
      return $request->query->all();
    }

    if ($mapRequestAttribute instanceof MapRequestHeaders) {
      return $request->headers->all();
    }

    if ($mapRequestAttribute instanceof MapRequestFile) {
      return $mapRequestAttribute->key ? [$argument->getName() => $request->files->get($mapRequestAttribute->key)] : $request->files->all();
    }

    throw new \LogicException('%s is not a supported request attribute', \get_debug_type($mapRequestAttribute));
  }

}
