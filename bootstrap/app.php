<?php

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use App\Exceptions\Handler;
use App\Http\Kernel;
use App\Console\Kernel as ConsoleKernelImplementation;

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    HttpKernel::class,
    Kernel::class
);

$app->singleton(
    ConsoleKernel::class,
    ConsoleKernelImplementation::class
);

$app->singleton(
    ExceptionHandler::class,
    Handler::class
);

return $app;