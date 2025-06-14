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
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('about')->nullable();
            $table->string('location_name')->nullable();
            $table->string('location_url')->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->string('speaker')->nullable();
            $table->string('poster_image')->nullable();
            $table->integer('max_attendees')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('category_id')->constrained('event_categories')->onDelete('set null')->nullable();
            $table->string('type');
            $table->enum('attendance_mode', ['ticketing', 'barcode']);
            $table->timestamps();
        });

        Schema::create('event_rundowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('title');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('ticket_code')->unique();
            $table->string('barcode')->unique();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('event_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('attendee_id')->nullable()->constrained('event_attendees')->onDelete('set null');
            $table->timestamp('scanned_at')->useCurrent();
            $table->string('location')->nullable();
            $table->string('name')->nullable(); // untuk barcode mode
            $table->string('origin_institution')->nullable(); // untuk barcode mode
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_categories');
        Schema::dropIfExists('events');
        Schema::dropIfExists('event_attendance');
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('event_rundowns');
    }
};
