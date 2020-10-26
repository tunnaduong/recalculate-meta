<?php

namespace MigrateToFlarum\RecalculateMeta;

use Flarum\Extend;

return [
    (new Extend\Console())
        ->command(Commands\RecalculateCommand::class),
];
