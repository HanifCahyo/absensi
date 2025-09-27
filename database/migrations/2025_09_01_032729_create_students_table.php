<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nis', 50)->unique();
            $table->string('nisn', 50)->unique();
            $table->string('date_of_birth', 100);
            $table->foreignId('class_id')
                ->nullable() // wajib kalau mau pakai onDelete('set null')
                ->constrained('classes')
                ->nullOnDelete(); // cara lebih rapi dari onDelete('set null')
            $table->string('religion', 50);
            $table->string('parent_contact', 20)->nullable();
            $table->string('major', 100);
            $table->string('qr_code_path')->nullable(); // Path file gambar QR Code
            $table->string('barcode_path')->nullable(); // Path file gambar Barcode
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
