<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(object $model): void
    {
        $this->logAction($model, 'created', null, $model->toArray());
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(object $model): void
    {
        $this->logAction($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(object $model): void
    {
        $this->logAction($model, 'deleted', $model->getOriginal(), null);
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(object $model): void
    {
        $this->logAction($model, 'restored', null, $model->toArray());
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(object $model): void
    {
        $this->logAction($model, 'force_deleted', $model->getOriginal(), null);
    }

    protected function logAction(object $model, string $action, ?array $oldData, ?array $newData): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => get_class($model),
            'entity_id' => $model->getKey(),
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => Request::ip(),
            'result' => 'success',
        ]);
    }
}
