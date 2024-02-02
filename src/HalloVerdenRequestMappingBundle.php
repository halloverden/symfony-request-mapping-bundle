<?php

namespace HalloVerden\RequestMappingBundle;

use HalloVerden\RequestMappingBundle\Handler\RequestDataHandlerInterface;
use HalloVerden\RequestMappingBundle\ValueResolver\RequestMappingValueResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

final class HalloVerdenRequestMappingBundle extends AbstractBundle {

  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void {
    $builder->registerForAutoconfiguration(RequestDataHandlerInterface::class)
      ->addTag('halloverden.request_mapping.data_handler');

    $container->services()
      ->set('halloverden.request_mapping.value_resolver', RequestMappingValueResolver::class)
        ->args([tagged_locator('halloverden.request_mapping.data_handler')])
        ->tag('controller.targeted_value_resolver', ['name' => RequestMappingValueResolver::class]);
  }

}
