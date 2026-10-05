<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('incidents')) {
            Schema::rename('incidents', 'tickets');
        }

        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'title')) {
                $table->string('title')->nullable()->after('id');
            }

            if (! Schema::hasColumn('tickets', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('affected_user_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'claimed_by')) {
                $table->foreignId('claimed_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'secondary_assistant_id')) {
                $table->foreignId('secondary_assistant_id')->nullable()->after('claimed_by')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'closed_by')) {
                $table->foreignId('closed_by')->nullable()->after('secondary_assistant_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('tickets', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('closed_by');
            }
        });

        DB::table('tickets')->whereNull('title')->update(['title' => 'Ticket sin título']);
        DB::table('tickets')->whereNull('created_by')->update(['created_by' => DB::raw('affected_user_id')]);

        if (Schema::hasTable('incident_actions')) {
            Schema::create('ticket_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
                $table->text('message');
                $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });

            DB::table('ticket_replies')->insertUsing(
                ['ticket_id', 'message', 'performed_by', 'created_at', 'updated_at'],
                DB::table('incident_actions')->select('incident_id', 'action', 'performed_by', 'created_at', 'updated_at')
            );

            Schema::drop('incident_actions');
        }
    }

    public function down(): void
    {
        Schema::create('incident_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->string('action');
            $table->string('phase')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('incident_actions')->insertUsing(
            ['incident_id', 'action', 'performed_by', 'created_at', 'updated_at'],
            DB::table('ticket_replies')->select('ticket_id', 'message', 'performed_by', 'created_at', 'updated_at')
        );

        Schema::drop('ticket_replies');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('claimed_by');
            $table->dropConstrainedForeignId('secondary_assistant_id');
            $table->dropConstrainedForeignId('closed_by');
            $table->dropColumn(['title', 'closed_at']);
        });

        Schema::rename('tickets', 'incidents');
    }
};
