<?php

namespace Database\Seeders;

use App\Models\MediaAppearance;
use Illuminate\Database\Seeder;

/**
 * The founder's earlier media appearances, as listed in the design file.
 * Safe to re-run: rows are matched by their link.
 */
class MediaAppearanceSeeder extends Seeder
{
    private const CHANNELS = [
        'alikhbariah' => ['الإخبارية السورية', 'Al-Ikhbariya Syria'],
        'syria_tv' => ['تلفزيون سوريا', 'Syria TV'],
        'sana' => ['وكالة سانا', 'SANA'],
        'thawra' => ['صحيفة الثورة السورية', 'Al-Thawra newspaper'],
    ];

    /**
     * @var list<array{kind: string, channel: string, date: ?string, title: string, url: string, featured?: bool}>
     */
    private const ITEMS = [
        ['kind' => 'video', 'channel' => 'alikhbariah', 'date' => '2026-08-15', 'featured' => true, 'title' => 'ما الأضرار التي تسببها الحرّاقات النفطية البدائية؟.. التفاصيل مع الاستشاري البيئي كامل شمس الدين', 'url' => 'https://www.youtube.com/watch?v=t04dnchKgyE'],
        ['kind' => 'video', 'channel' => 'syria_tv', 'date' => '2025-09-11', 'featured' => true, 'title' => 'تحذيرات من صدمة مناخية في سوريا تضع الأمن الغذائي في خطر كبير', 'url' => 'https://www.youtube.com/watch?v=UylHVtaN4pw'],
        ['kind' => 'article', 'channel' => 'sana', 'date' => '2026-11-11', 'featured' => true, 'title' => 'الاحترار العالمي يفاقم تحديات التكيف.. وسوريا أمام استحقاقات مناخية', 'url' => 'https://sana.sy/reports/2632637/'],
        ['kind' => 'article', 'channel' => 'alikhbariah', 'date' => '2026-08-16', 'title' => 'خبير بيئي يتحدث عن الأضرار التي تسببها الحرّاقات البدائية في دير الزور رغم إغلاقها', 'url' => 'https://alikhbariah.com/%D8%AE%D8%A8%D9%8A%D8%B1-%D8%A8%D9%8A%D8%A6%D9%8A-%D8%A2%D8%AB%D8%A7%D8%B1-%D8%A7%D9%84%D8%AD%D8%B1%D8%A7%D9%82%D8%A7%D8%AA-%D8%A7%D9%84%D8%A8%D8%AF%D8%A7%D8%A6%D9%8A%D8%A9-%D9%81%D9%8A-%D8%AF%D9%8A/'],
        ['kind' => 'article', 'channel' => 'thawra', 'date' => '2026-02-04', 'title' => 'من التهميش إلى التنظيم.. «النباشون» حلقة الوصل المنسية في إدارة النفايات', 'url' => 'https://thawra.sy/report/من-التهميش-إلى-التنظيم/'],
        ['kind' => 'article', 'channel' => 'thawra', 'date' => '2026-02-18', 'title' => 'تدوير النفايات في سوريا.. ضرورة بيئية وفرصة للتنمية المحلية', 'url' => 'https://thawra.sy/report/تدوير-النفايات-في-سوريا-ضرورة-بيئية-وف/'],
        ['kind' => 'article', 'channel' => 'thawra', 'date' => '2026-03-30', 'title' => 'من عبء بيئي إلى مورد واعد.. تعديلات مرتقبة على قانون النظافة لتعزيز الاستدامة وإدارة المخلفات', 'url' => 'https://thawra.sy/report/من-عبء-بيئي-إلى-مورد-واعد-تعديلات-مرتق/'],
        ['kind' => 'article', 'channel' => 'thawra', 'date' => '2026-07-06', 'title' => 'تعطل مكب «وادي الهدة» يسلط الضوء على واقع إدارة النفايات في طرطوس', 'url' => 'https://thawra.sy/report/تعطل-مكب-وادي-الهدة-يسلط-الضوء-على-واق/'],
        ['kind' => 'article', 'channel' => 'thawra', 'date' => null, 'title' => 'الغطاء النباتي في سوريا.. خسائر متراكمة ومسار طويل نحو التعافي البيئي', 'url' => 'https://thawra.sy/local/الغطاء-النباتي-في-سوريا/'],
        ['kind' => 'article', 'channel' => 'thawra', 'date' => '2026-06-21', 'title' => 'تأخر ترحيل النفايات في الأرياف.. تداعيات بيئية تطال الصحة والزراعة والغذاء', 'url' => 'https://thawra.sy/report/تأخر-ترحيل-النفايات-في-الأرياف-تداعيا/'],
    ];

    public function run(): void
    {
        foreach (self::ITEMS as $item) {
            [$channelAr, $channelEn] = self::CHANNELS[$item['channel']];

            // forceFill keeps youtube_id right even when model events are muted (DatabaseSeeder).
            MediaAppearance::query()->firstOrNew(['url' => $item['url']])
                ->forceFill([
                    'kind' => $item['kind'],
                    'title' => $item['title'],
                    'title_language' => 'ar',
                    'channel_ar' => $channelAr,
                    'channel_en' => $channelEn,
                    'youtube_id' => $item['kind'] === 'video' ? MediaAppearance::youtubeIdFrom($item['url']) : null,
                    'published_on' => $item['date'],
                    'is_published' => true,
                    'is_featured' => $item['featured'] ?? false,
                ])
                ->save();
        }

        MediaAppearance::flushCache();
    }
}
