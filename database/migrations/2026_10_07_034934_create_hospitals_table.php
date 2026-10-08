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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rs');
            $table->string('tipe');
            $table->boolean('mitra_bpjs')->default(true);
            $table->string('lokasi');
            $table->text('alamat')->nullable();
            $table->string('no_telp')->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('fasilitas')->nullable();
            $table->string('jarak_estimasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
