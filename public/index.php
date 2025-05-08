<?php

declare(strict_types=1);

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    ini_set('memory_limit', -1);
    set_time_limit(0);

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
