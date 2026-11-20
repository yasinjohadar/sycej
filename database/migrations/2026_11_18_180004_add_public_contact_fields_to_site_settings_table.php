<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Facebook page that used to be hard-coded in config/frontend.php. It moves
     * into the social links table so it can be edited from the panel.
     */
    private const LEGACY_FACEBOOK = 'https://www.facebook.com/people/Kamel-Shams-Alddin/100084527919012/';

    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('address_en')->nullable()->after('address');
            $table->string('website')->nullable()->after('address_en');
            $table->boolean('whatsapp_float_enabled')->default(true)->after('primary_whatsapp');
            $table->string('whatsapp_float_message', 300)->nullable()->after('whatsapp_float_enabled');
        });

        if (! DB::table('social_links')->where('url', self::LEGACY_FACEBOOK)->exists()) {
            DB::table('social_links')->insert([
                'label' => 'فيسبوك',
                'url' => self::LEGACY_FACEBOOK,
                'icon' => 'ri-facebook-circle-line',
                'sort_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['address_en', 'website', 'whatsapp_float_enabled', 'whatsapp_float_message']);
        });
    }
};
