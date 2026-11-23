<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_backup', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->timestamp('backed_up_at')->useCurrent();
        });

        // Trigger & stored procedure hanya tersedia di MySQL/MariaDB.
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS after_user_insert');
        DB::unprepared('
            CREATE TRIGGER after_user_insert AFTER INSERT ON users FOR EACH ROW
            BEGIN
                INSERT INTO users_backup (id, name, email) VALUES (NEW.id, NEW.name, NEW.email);
            END
        ');

        DB::unprepared('DROP PROCEDURE IF EXISTS getcat');
        DB::unprepared('CREATE PROCEDURE getcat(IN cid INT) SELECT * FROM categories WHERE id = cid');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS after_user_insert');
            DB::unprepared('DROP PROCEDURE IF EXISTS getcat');
        }

        Schema::dropIfExists('users_backup');
    }
};
