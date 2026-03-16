<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verification_code_hash')) {
                $table->string('email_verification_code_hash')->nullable()->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'email_verification_code_expires_at')) {
                $table->timestamp('email_verification_code_expires_at')->nullable()->after('email_verification_code_hash');
            }

            if (!Schema::hasColumn('users', 'email_verification_code_sent_at')) {
                $table->timestamp('email_verification_code_sent_at')->nullable()->after('email_verification_code_expires_at');
            }

            if (!Schema::hasColumn('users', 'telefone_pessoal_1_pending')) {
                $table->string('telefone_pessoal_1_pending')->nullable()->after('telefone_pessoal_1');
            }

            if (!Schema::hasColumn('users', 'telefone_pessoal_1_verification_code_hash')) {
                $table->string('telefone_pessoal_1_verification_code_hash')->nullable()->after('telefone_pessoal_1_pending');
            }

            if (!Schema::hasColumn('users', 'telefone_pessoal_1_verification_code_expires_at')) {
                $table->timestamp('telefone_pessoal_1_verification_code_expires_at')->nullable()->after('telefone_pessoal_1_verification_code_hash');
            }

            if (!Schema::hasColumn('users', 'telefone_pessoal_1_verification_code_sent_at')) {
                $table->timestamp('telefone_pessoal_1_verification_code_sent_at')->nullable()->after('telefone_pessoal_1_verification_code_expires_at');
            }

            if (!Schema::hasColumn('users', 'telefone_pessoal_1_verified_at')) {
                $table->timestamp('telefone_pessoal_1_verified_at')->nullable()->after('telefone_pessoal_1_verification_code_sent_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'email_verification_code_hash',
                'email_verification_code_expires_at',
                'email_verification_code_sent_at',
                'telefone_pessoal_1_pending',
                'telefone_pessoal_1_verification_code_hash',
                'telefone_pessoal_1_verification_code_expires_at',
                'telefone_pessoal_1_verification_code_sent_at',
                'telefone_pessoal_1_verified_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
