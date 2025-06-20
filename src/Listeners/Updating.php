<?php

namespace KSeven\Userstamps\Listeners;

use Illuminate\Database\Eloquent\Model;
use KSeven\Userstamps\Userstamps;

class Updating
{
    public function handle(Model $model): void
    {
        if (! $model->isUserstamping() || is_null($model->getUpdatedByColumn()) || is_null(Userstamps::getUserId())) {
            return;
        }

        $model->{$model->getUpdatedByColumn()} = Userstamps::getUserId();
    }
}
