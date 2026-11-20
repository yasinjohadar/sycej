<?php

namespace App\Console\Commands;

use App\Models\MediaAppearance;
use Database\Seeders\BlogDemoSeeder;
use Database\Seeders\MediaAppearanceSeeder;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;

/**
 * Wipes the content tables filled by the seeders and seeds them again from scratch,
 * so the site matches the seeder files exactly (rows added from the panel are removed too).
 */
class ReseedContentCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'content:reseed
                            {--only= : media أو blog لإعادة قسم واحد فقط}
                            {--force : التنفيذ دون تأكيد في بيئة الإنتاج}';

    protected $description = 'حذف المشاركات الإعلامية و/أو المدونة بالكامل ثم إعادة إضافتها من ملفات الـ seed';

    public function handle(): int
    {
        $only = $this->option('only');

        if ($only !== null && ! in_array($only, ['media', 'blog'], true)) {
            $this->error('قيمة --only يجب أن تكون media أو blog.');

            return self::INVALID;
        }

        $sections = $only !== null ? [$only] : ['media', 'blog'];

        $this->warn('سيتم حذف كل البيانات في: '.implode('، ', $sections).' (بما فيها ما أُضيف من لوحة التحكم).');

        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        DB::transaction(function () use ($sections): void {
            if (in_array('media', $sections, true)) {
                $this->resetMedia();
            }

            if (in_array('blog', $sections, true)) {
                $this->resetBlog();
            }
        });

        MediaAppearance::flushCache();

        $this->info('تمت إعادة البيانات بنجاح.');

        return self::SUCCESS;
    }

    private function resetMedia(): void
    {
        DB::table('media_appearances')->delete();

        $this->call('db:seed', ['--class' => MediaAppearanceSeeder::class, '--force' => true]);
    }

    /**
     * Raw deletes so soft-deleted posts and categories are removed for good
     * and their slugs are free for the seeder again.
     */
    private function resetBlog(): void
    {
        DB::table('blog_post_tag')->delete();
        DB::table('blog_posts')->delete();
        DB::table('blog_tags')->delete();
        DB::table('blog_categories')->update(['parent_id' => null]);
        DB::table('blog_categories')->delete();

        $this->call('db:seed', ['--class' => BlogDemoSeeder::class, '--force' => true]);
    }
}
