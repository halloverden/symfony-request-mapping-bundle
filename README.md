# symfony-request-mapping-bundle
Symfony bundle to map request data to objects

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require halloverden/request-mapping-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require halloverden/request-mapping-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
   HalloVerden\RequestMappingBundle\HalloVerdenRequestMappingBundle::class => ['all' => true],
];
```

Usage
============

1. Create a handler that implements `RequestDataHandlerInterface` (or extend `AbstractRequestDataHandler`)
2. Create a class that represents the payload, query or headers of the request
3. Use `MapRequestPayload`, `MapRequestQuery` or `MapRequestHeaders` attribute on parameters into the controller.
    ```php
    <?php
    
    namespace App\Controller;
    
    use App\Entity\Requests\TestRequest;
    use App\Entity\Response\TestResponse;
    use HalloVerden\RequestMappingBundle\Attribute\MapRequestPayload;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;
    
    #[Route(path: '/test2', name: 'testpost', methods: [Request::METHOD_POST])]
    class Test2Controller extends AbstractResponseEntityController {
      public function __invoke(
        #[MapRequestPayload(handler: 'your_handler')]
        TestRequestPayload $testRequestPayload
      ): JsonResponse {
        // Do something with $testRequestPayload
        return new JsonResponse();
      }
    }
    ```
