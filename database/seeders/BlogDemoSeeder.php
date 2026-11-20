<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo blog content so the public blog and the home page's blog section have
 * something to show before real posts are written. Safe to run more than once:
 * every record is matched by its slug and updated in place.
 *
 * Not part of DatabaseSeeder on purpose; run it explicitly:
 *   php artisan db:seed --class=BlogDemoSeeder
 */
class BlogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->where('email', 'admin@example.com')->first() ?? User::query()->first();

        $categories = [];
        foreach ($this->categories() as $slug => $category) {
            $categories[$slug] = BlogCategory::query()->updateOrCreate(
                ['slug' => $slug],
                $category + ['is_active' => true],
            );
        }

        $tags = [];
        foreach ($this->tags() as $slug => $name) {
            $tags[$slug] = BlogTag::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'is_active' => true],
            );
        }

        foreach ($this->posts() as $index => $post) {
            $model = BlogPost::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'language' => $post['language'],
                    'blog_category_id' => $categories[$post['category']]->id,
                    'author_id' => $author?->id,
                    'status' => 'published',
                    'published_at' => now()->subDays($post['days_ago'])->setTime(10, 0),
                    'is_featured' => $post['featured'] ?? false,
                    'is_indexable' => true,
                    'views_count' => 80 + $index * 23,
                ],
            );

            $model->tags()->sync(collect($post['tags'])->map(fn (string $slug): int => $tags[$slug]->id)->all());
        }
    }

    /**
     * @return array<string, array{name: string, description: string}>
     */
    private function categories(): array
    {
        return [
            'waste-management' => ['name' => 'إدارة النفايات', 'description' => 'واقع النفايات في سوريا وحلول إدارتها وتدويرها.'],
            'water-and-soil' => ['name' => 'المياه والتربة', 'description' => 'حماية الموارد المائية والتربة الزراعية من التلوث.'],
            'climate-justice' => ['name' => 'العدالة المناخية', 'description' => 'تغير المناخ وآثاره على المجتمعات الأكثر هشاشة.'],
            'environmental-rights' => ['name' => 'Environmental Rights', 'description' => 'Rights, law and participation in environmental decisions.'],
            'climate-adaptation' => ['name' => 'Climate Adaptation', 'description' => 'How communities in Syria can adapt to a changing climate.'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function tags(): array
    {
        return [
            'aleppo' => 'حلب',
            'recycling' => 'التدوير',
            'agriculture' => 'الزراعة',
            'syria' => 'Syria',
            'community' => 'Community',
        ];
    }

    /**
     * @return list<array{slug: string, title: string, excerpt: string, content: string, language: string, category: string, tags: list<string>, days_ago: int, featured?: bool}>
     */
    private function posts(): array
    {
        return [
            [
                'slug' => 'rural-waste-silent-crisis',
                'title' => 'النفايات في الأرياف السورية: أزمة صامتة تهدد الصحة والغذاء',
                'excerpt' => 'تأخر ترحيل النفايات في الأرياف لم يعد مشكلة منظر عام فحسب، بل صار خطراً يطال التربة والمياه وسلسلة الغذاء.',
                'content' => $this->arabicArticle(
                    'تتراكم النفايات في كثير من القرى والبلدات السورية لأسابيع قبل ترحيلها، وأحياناً تُحرق في العراء. ومع كل موسم أمطار تتسرب ملوثاتها إلى التربة والمياه الجوفية.',
                    'حجم المشكلة',
                    'تفتقر معظم الأرياف إلى مكبات نظامية، وتعتمد على مواقع عشوائية قريبة من الأراضي الزراعية ومصادر المياه، ما يجعل أثرها يمتد إلى ما نأكله ونشربه.',
                    'البيئة ليست قضية منفصلة عن حياة الناس، بل هي جزء من حقوقهم اليومية.',
                    ['جمع منتظم بجداول معلنة للسكان.', 'فرز من المصدر يخفف الكميات المرسلة إلى المكب.', 'مواقع تجميع مؤقتة بعيدة عن الآبار والأراضي الزراعية.'],
                    'لا يمكن لأي حل أن ينجح دون إشراك المجالس المحلية والسكان في التخطيط والمتابعة.',
                ),
                'language' => 'ar',
                'category' => 'waste-management',
                'tags' => ['aleppo', 'recycling'],
                'days_ago' => 1,
                'featured' => true,
            ],
            [
                'slug' => 'protecting-groundwater',
                'title' => 'كيف نحمي المياه الجوفية من تسرب الملوثات؟',
                'excerpt' => 'المياه الجوفية مصدر رئيسي للشرب والري في سوريا، وحمايتها تبدأ من فهم مصادر التلوث القريبة منها.',
                'content' => $this->arabicArticle(
                    'تعتمد مناطق واسعة على الآبار لتأمين مياه الشرب والري، لكن هذه الآبار تتأثر بما يجري على سطح الأرض من حولها.',
                    'مصادر التلوث',
                    'من أبرز المصادر: المكبات العشوائية، ومياه الصرف غير المعالجة، والاستخدام المفرط للأسمدة والمبيدات.',
                    'حماية المياه أرخص دائماً من معالجتها بعد التلوث.',
                    ['تحديد مناطق حماية حول الآبار.', 'مراقبة دورية لجودة المياه.', 'توعية المزارعين بالاستخدام الآمن للأسمدة.'],
                    'المعرفة الموثوقة بحالة المياه حق للمجتمعات التي تعتمد عليها.',
                ),
                'language' => 'ar',
                'category' => 'water-and-soil',
                'tags' => ['agriculture'],
                'days_ago' => 4,
            ],
            [
                'slug' => 'climate-justice-who-pays',
                'title' => 'العدالة المناخية: لماذا يدفع الأضعف الثمن الأكبر؟',
                'excerpt' => 'موجات الجفاف وارتفاع الحرارة لا تصيب الجميع بالقدر نفسه؛ فالمزارعون الصغار والأسر الفقيرة هم الأكثر تأثراً.',
                'content' => $this->arabicArticle(
                    'تزداد في سوريا موجات الجفاف وتقل الأمطار، فتتراجع المحاصيل وترتفع أسعار الغذاء.',
                    'من يتأثر أولاً؟',
                    'الأسر التي تعتمد على الزراعة البعلية والمجتمعات التي تفتقر إلى بنية تحتية للمياه هي أول من يدفع الثمن.',
                    'العدالة المناخية تعني أن تُسمع أصوات المتأثرين في قرارات التكيف.',
                    ['دعم المزارعين بأساليب ري موفرة للمياه.', 'أنظمة إنذار مبكر للجفاف.', 'إشراك المجتمعات في خطط التكيف المحلية.'],
                    'التكيف مع تغير المناخ ليس ترفاً، بل ضرورة لحماية سبل العيش.',
                ),
                'language' => 'ar',
                'category' => 'climate-justice',
                'tags' => ['agriculture'],
                'days_ago' => 7,
            ],
            [
                'slug' => 'recycling-local-development',
                'title' => 'تدوير النفايات فرصة للتنمية المحلية',
                'excerpt' => 'يمكن أن يتحول التدوير من عبء بيئي إلى مورد يخلق فرص عمل ويخفف الضغط على المكبات.',
                'content' => $this->arabicArticle(
                    'يعمل كثيرون في جمع المواد القابلة للتدوير دون أي تنظيم أو حماية، رغم دورهم المهم في تقليل النفايات.',
                    'الحلقة المنسية',
                    'تنظيم عمل جامعي المواد القابلة للتدوير وحمايتهم يرفع كفاءة التدوير ويحسن ظروفهم المعيشية.',
                    'ما نرميه اليوم قد يكون مورداً لاقتصاد محلي أكثر استدامة.',
                    ['نقاط فرز في الأحياء.', 'شراكات مع ورش التدوير المحلية.', 'تدريب العاملين على السلامة المهنية.'],
                    'التدوير الناجح يبدأ من البيت ويكتمل بسياسات داعمة.',
                ),
                'language' => 'ar',
                'category' => 'waste-management',
                'tags' => ['recycling', 'aleppo'],
                'days_ago' => 11,
            ],
            [
                'slug' => 'farmland-soil-recovery',
                'title' => 'التربة الزراعية بعد الحرب: خطوات نحو التعافي',
                'excerpt' => 'تضررت مساحات واسعة من التربة الزراعية، لكن التعافي ممكن بخطوات مدروسة تبدأ بالتقييم.',
                'content' => $this->arabicArticle(
                    'خلفت سنوات الحرب أضراراً في التربة بسبب التلوث والإهمال وتراجع الممارسات الزراعية السليمة.',
                    'تقييم الضرر أولاً',
                    'لا يمكن وضع خطة تعافٍ دون معرفة نوع التلوث ومستواه في كل منطقة.',
                    'التربة السليمة أساس الأمن الغذائي.',
                    ['تحليل عينات التربة.', 'زراعة محاصيل تساعد على استصلاح الأرض.', 'العودة التدريجية إلى الدورة الزراعية.'],
                    'تعافي التربة مسار طويل، لكنه يبدأ بخطوة صحيحة.',
                ),
                'language' => 'ar',
                'category' => 'water-and-soil',
                'tags' => ['agriculture'],
                'days_ago' => 15,
            ],
            [
                'slug' => 'environmental-rights-guide',
                'title' => 'دليل مبسّط للحقوق البيئية في سوريا',
                'excerpt' => 'من حقك أن تعرف المخاطر البيئية من حولك، وأن تشارك في القرارات التي تمس صحتك ومواردك.',
                'content' => $this->arabicArticle(
                    'الحقوق البيئية ليست مفهوماً قانونياً مجرداً، بل جزء من الحياة اليومية لكل فرد.',
                    'ما هي حقوقك؟',
                    'تشمل الحقوق البيئية الحق في بيئة صحية، والحق في الوصول إلى المعلومات البيئية، والحق في المشاركة.',
                    'المعرفة بالحقوق أول خطوة نحو حمايتها.',
                    ['الحق في المعلومة.', 'الحق في المشاركة.', 'الحق في الإنصاف عند الضرر.'],
                    'نعمل على تبسيط المعرفة القانونية البيئية وإتاحتها للجميع.',
                ),
                'language' => 'ar',
                'category' => 'climate-justice',
                'tags' => ['aleppo'],
                'days_ago' => 20,
            ],
            [
                'slug' => 'right-to-environmental-information',
                'title' => 'The right to know: environmental information in Syria',
                'excerpt' => 'Communities have the right to know about the environmental risks around them — and to take part in decisions that affect them.',
                'content' => $this->englishArticle(
                    'Access to reliable environmental information is the first step towards protecting health, resources and livelihoods.',
                    'Why information matters',
                    'Without data on water quality, waste sites or air pollution, communities cannot judge the risks they face or ask for change.',
                    'Knowledge of environmental risks is a right, not a privilege.',
                    ['Publish basic water-quality data.', 'Map informal waste sites.', 'Explain risks in plain language.'],
                    'We work to make environmental knowledge accessible to everyone.',
                ),
                'language' => 'en',
                'category' => 'environmental-rights',
                'tags' => ['syria', 'community'],
                'days_ago' => 2,
                'featured' => true,
            ],
            [
                'slug' => 'adapting-to-drought',
                'title' => 'Adapting to drought: what Syrian farmers need now',
                'excerpt' => 'Longer dry seasons are reshaping agriculture. Practical adaptation starts with water-saving irrigation and early warning.',
                'content' => $this->englishArticle(
                    'Rainfall in Syria has become less predictable, and small farmers feel the impact first.',
                    'The pressure on farms',
                    'Lower yields and rising costs push families who depend on rain-fed farming into deeper hardship.',
                    'Climate adaptation is not a luxury — it protects livelihoods.',
                    ['Support water-saving irrigation.', 'Set up early-warning systems for drought.', 'Involve communities in local adaptation plans.'],
                    'Adaptation works best when it is built with the people it is meant to serve.',
                ),
                'language' => 'en',
                'category' => 'climate-adaptation',
                'tags' => ['syria'],
                'days_ago' => 6,
            ],
            [
                'slug' => 'community-led-waste-solutions',
                'title' => 'Community-led solutions to the waste crisis',
                'excerpt' => 'Sorting at source and supporting informal recyclers can cut the waste reaching landfills and create local jobs.',
                'content' => $this->englishArticle(
                    'Many Syrian towns struggle with irregular waste collection and unmanaged dumpsites.',
                    'Starting at home',
                    'Sorting waste at source reduces the volume sent to landfills and makes recycling viable.',
                    'What we throw away today can become a resource for a local economy.',
                    ['Neighbourhood sorting points.', 'Partnerships with local recycling workshops.', 'Safety training for informal recyclers.'],
                    'Lasting solutions start with communities and are sustained by supportive policy.',
                ),
                'language' => 'en',
                'category' => 'environmental-rights',
                'tags' => ['community'],
                'days_ago' => 10,
            ],
        ];
    }

    /**
     * @param  list<string>  $points
     */
    private function arabicArticle(string $intro, string $heading, string $body, string $quote, array $points, string $closing): string
    {
        return $this->article($intro, $heading, $body, $quote, 'ما الذي يمكن فعله؟', $points, 'نحو حلول واقعية', $closing);
    }

    /**
     * @param  list<string>  $points
     */
    private function englishArticle(string $intro, string $heading, string $body, string $quote, array $points, string $closing): string
    {
        return $this->article($intro, $heading, $body, $quote, 'What can be done?', $points, 'Towards realistic solutions', $closing);
    }

    /**
     * @param  list<string>  $points
     */
    private function article(string $intro, string $heading, string $body, string $quote, string $pointsHeading, array $points, string $closingHeading, string $closing): string
    {
        $items = collect($points)->map(fn (string $point): string => '<li>'.e($point).'</li>')->implode('');

        return '<p>'.e($intro).'</p>'
            .'<h2>'.e($heading).'</h2><p>'.e($body).'</p>'
            .'<blockquote>'.e($quote).'</blockquote>'
            .'<h3>'.e($pointsHeading).'</h3><ul>'.$items.'</ul>'
            .'<h2>'.e($closingHeading).'</h2><p>'.e($closing).'</p>';
    }
}
