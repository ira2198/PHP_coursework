<?php

namespace GeekBrains\LevelTwo\Users\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFoundException extends  AppException implements NotFoundExceptionInterface
{}