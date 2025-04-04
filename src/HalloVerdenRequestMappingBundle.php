<?php

namespace HalloVerden\RequestMappingBundle;

use HalloVerden\RequestMappingBundle\Attribute\AsRequestDataHandler;
use HalloVerden\RequestMappingBundle\Handler\RequestDataHandlerInterface;
use HalloVerden\RequestMappingBundle\Handler\UploadedFileRequestDataHandler;
use HalloVerden\RequestMappingBundle\ValueResolver\RequestMappingValueResolver;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

final class HalloVerdenRequestMappingBundle extends AbstractBundle {

  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void {
    $builder->registerForAutoconfiguration(RequestDataHandlerInterface::class)
      ->addTag('halloverden.request_mapping.data_handler');

    $builder->registerAttributeForAutoconfiguration(AsRequestDataHandler::class, static function (ChildDefinition $definition, AsRequestDataHandler $attribute): void {
      $definition->addTag('halloverden.request_mapping.data_handler', ['type' => $attribute->type]);
    });

    $container->services()
      ->set('halloverden.reqeust_mapping.data_handler.uploaded_file', UploadedFileRequestDataHandler::class)
        ->tag('halloverden.request_mapping.data_handler', ['type' => UploadedFile::class])
      ->set('halloverden.request_mapping.value_resolver', RequestMappingValueResolver::class)
        ->args([tagged_locator('halloverden.request_mapping.data_handler', 'type')])
        ->tag('controller.targeted_value_resolver', ['name' => RequestMappingValueResolver::class]);
  }

}
