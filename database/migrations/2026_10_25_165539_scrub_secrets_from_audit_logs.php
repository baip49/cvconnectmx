<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove password hashes, remember tokens and 2FA secrets stored in old user audit entries.
     */
    public function up(): void
    {
        $secrets = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

        DB::table('audit_logs')
            ->where('entity_type', User::class)
            ->orderBy('id')
            ->chunkById(500, function ($logs) use ($secrets): void {
                foreach ($logs as $log) {
                    $scrub = fn (?string $json): ?string => $json === null
                        ? null
                        : json_encode(Arr::except((array) json_decode($json, true), $secrets));

                    DB::table('audit_logs')->where('id', $log->id)->update([
                        'old_data' => $scrub($log->old_data),
                        'new_data' => $scrub($log->new_data),
                    ]);
                }
            });
    }
};
