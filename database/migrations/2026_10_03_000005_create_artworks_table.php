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
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('artist_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('artworkable');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->decimal('width_cm', 7, 2)->nullable();
            $table->decimal('height_cm', 7, 2)->nullable();
            $table->decimal('depth_cm', 7, 2)->nullable();
            // Explicit flag: an edition with one print left is still not an original.
            $table->boolean('is_original')->default(true);
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('edition_size')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['artworkable_type', 'artworkable_id']);
            $table->unique(['store_id', 'slug']);
            $table->index(['store_id', 'status']);
        });

        Schema::create('painting_details', function (Blueprint $table) {
            $table->id();
            $table->string('medium');
            $table->string('surface');
            $table->boolean('is_framed')->default(false);
            $table->timestamps();
        });

        Schema::create('print_details', function (Blueprint $table) {
            $table->id();
            $table->string('print_method');
            $table->string('paper')->nullable();
            $table->boolean('is_signed')->default(false);
            $table->boolean('is_numbered')->default(false);
            $table->timestamps();
        });

        Schema::create('photograph_details', function (Blueprint $table) {
            $table->id();
            $table->string('print_process')->nullable();
            $table->string('paper')->nullable();
            $table->boolean('is_signed')->default(false);
            $table->timestamps();
        });

        Schema::create('sculpture_details', function (Blueprint $table) {
            $table->id();
            $table->string('material');
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->boolean('requires_freight')->default(false);
            $table->timestamps();
        });

        Schema::create('digital_details', function (Blueprint $table) {
            $table->id();
            $table->string('file_format');
            $table->string('resolution')->nullable();
            $table->string('license');
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::create('artwork_collection', function (Blueprint $table) {
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);

            $table->primary(['artwork_id', 'collection_id']);
        });

        Schema::create('artwork_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_variants');
        Schema::dropIfExists('artwork_collection');
        Schema::dropIfExists('digital_details');
        Schema::dropIfExists('sculpture_details');
        Schema::dropIfExists('photograph_details');
        Schema::dropIfExists('print_details');
        Schema::dropIfExists('painting_details');
        Schema::dropIfExists('artworks');
    }
};
