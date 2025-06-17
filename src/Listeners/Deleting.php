<?php

namespace KSeven\Userstamps\Listeners;

use Illuminate\Database\Eloquent\Model;
use KSeven\Userstamps\Userstamps;

class Deleting
{
    public function handle(Model $model): void
    {
        if (! $model->isUserstamping() || is_null($model->getDeletedByColumn())) {
            return;
        }

        if (is_null($model->{$model->getDeletedByColumn()})) {
            $model->{$model->getDeletedByColumn()} = Userstamps::getUserId();
        }

        $dispatcher = $model->getEventDispatcher();

        $model->unsetEventDispatcher();

        $model->save();

        $model->setEventDispatcher($dispatcher);
    }
}
