<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\CandidateDocument;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    private const ENTITY_CATEGORIES = [
        User::class => 'auth',
        Candidate::class => 'candidate',
        Company::class => 'company',
        Vacancy::class => 'vacancy',
        Application::class => 'application',
        Role::class => 'auth',
        Permission::class => 'auth',
        CandidateDocument::class => 'candidate',
    ];

    public function created(object $model): void
    {
        $this->logAction($model, 'created', null, $model->toArray());
    }

    public function updated(object $model): void
    {
        $this->logAction($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    public function deleted(object $model): void
    {
        $this->logAction($model, 'deleted', $model->getOriginal(), null);
    }

    public function restored(object $model): void
    {
        $this->logAction($model, 'restored', null, $model->toArray());
    }

    public function forceDeleted(object $model): void
    {
        $this->logAction($model, 'force_deleted', $model->getOriginal(), null);
    }

    protected function logAction(object $model, string $action, ?array $oldData, ?array $newData): void
    {
        $entityClass = get_class($model);
        $category = self::ENTITY_CATEGORIES[$entityClass] ?? 'system';
        $severity = $this->determineSeverity($action, $category);
        $details = $this->buildDetails($model, $action, $oldData, $newData);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'category' => $category,
            'severity' => $severity,
            'entity_type' => $entityClass,
            'entity_id' => $model->getKey(),
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'details' => $details,
            'result' => 'success',
        ]);
    }

    private function determineSeverity(string $action, string $category): string
    {
        if ($action === 'force_deleted') {
            return 'critical';
        }

        if ($action === 'deleted' && in_array($category, ['auth', 'system'])) {
            return 'high';
        }

        if ($action === 'deleted') {
            return 'medium';
        }

        if ($action === 'updated' && $category === 'auth') {
            return 'medium';
        }

        return 'low';
    }

    private function buildDetails(object $model, string $action, ?array $oldData, ?array $newData): ?string
    {
        $className = class_basename($model);
        $label = $this->resolveEntityLabel($model);

        return match ($action) {
            'created' => "{$className} creado: {$label}",
            'updated' => "{$className} actualizado: {$label}",
            'deleted' => "{$className} eliminado: {$label}",
            'restored' => "{$className} restaurado: {$label}",
            'force_deleted' => "{$className} eliminado permanentemente: {$label}",
            default => "{$className} - {$action}: {$label}",
        };
    }

    private function resolveEntityLabel(object $model): string
    {
        if (! empty($model->getAttribute('name'))) {
            return $model->name;
        }

        if (! empty($model->getAttribute('email'))) {
            return $model->email;
        }

        if (! empty($model->getAttribute('title'))) {
            return $model->title;
        }

        return '#'.$model->getKey();
    }
}
