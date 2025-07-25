<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Country;
use App\Models\TaxSlabs;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tax_slabs', function (Blueprint $table) {
            // Add the new foreign key column.
            $table->foreignId('country_id')->nullable()->after('id')->constrained('countries');
        });

        // This script attempts to migrate your old string data to the new foreign key.
        // Ensure your countries table is populated before running this.
        if (Schema::hasColumn('tax_slabs', 'country')) {
            TaxSlabs::all()->each(function ($slab) {
                if ($slab->country) {
                    $countryModel = Country::firstOrCreate(['name' => $slab->country]);
                    $slab->country_id = $countryModel->id;
                    $slab->save();
                }
            });

            // After migrating the data, you can safely drop the old column.
            Schema::table('tax_slabs', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_slabs', function (Blueprint $table) {
            $table->string('country')->nullable()->after('id');
        });

        // Optional: Re-populate old column if needed during rollback
        TaxSlabs::all()->each(function ($slab) {
            if ($slab->country_id) {
                $slab->country = $slab->country()->first()->name;
                $slab->save();
            }
        });

        Schema::table('tax_slabs', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
