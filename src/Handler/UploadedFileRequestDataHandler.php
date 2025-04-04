<?php

namespace HalloVerden\RequestMappingBundle\Handler;

use HalloVerden\HttpExceptions\Utility\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class UploadedFileRequestDataHandler implements RequestDataHandlerInterface {
  private const ERROR_FILE_MISSING = 'FILE_MISSING';

  /**
   * @inheritDoc
   */
  public function handle(array $data, ArgumentMetadata $argument): ?UploadedFile {
    $uploadedFile = $data[$argument->getName()] ?? null;

    if ($uploadedFile instanceof UploadedFile) {
      return $uploadedFile;
    }

    if (null === $uploadedFile && ($argument->isNullable() || $argument->hasDefaultValue())) {
      return null;
    }

    $violationList = new ConstraintViolationList([
      new ConstraintViolation(self::ERROR_FILE_MISSING, null, [], [], $argument->getName(), $uploadedFile)
    ]);
    throw new ValidationException($violationList);
  }

}
