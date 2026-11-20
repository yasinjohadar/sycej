<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

/**
 * The founder plus demo team members and advisors to fill the team page until the
 * real roster is entered from the panel. Safe to re-run: rows are matched by Arabic name.
 */
class TeamMemberSeeder extends Seeder
{
    /**
     * @var list<array{group: string, name_ar: string, name_en: string, role_ar: string, role_en: string, bio_ar: string, bio_en: string}>
     */
    private const MEMBERS = [
        [
            'group' => TeamMember::GROUP_FOUNDER,
            'name_ar' => 'كامل شمس الدين',
            'name_en' => 'Kamel Shams Alddin',
            'role_ar' => 'مهندس واستشاري بيئي',
            'role_en' => 'Environmental Engineer & Consultant',
            'bio_ar' => 'مؤسس المركز السوري للعدالة البيئية والمناخية، يعمل في قضايا إدارة النفايات والتلوث والتكيف المناخي، ويشارك بانتظام في الإعلام لتبسيط القضايا البيئية للجمهور.',
            'bio_en' => 'Founder of the Syrian Center for Environmental and Climate Justice, working on waste management, pollution and climate adaptation, and a regular media voice on environmental issues.',
        ],
        [
            'group' => TeamMember::GROUP_TEAM,
            'name_ar' => 'ريم الحلبي',
            'name_en' => 'Reem Al-Halabi',
            'role_ar' => 'منسقة البرامج',
            'role_en' => 'Programs Coordinator',
            'bio_ar' => 'تتابع تنفيذ البرامج والمبادرات المجتمعية وتنسق مع الشركاء المحليين.',
            'bio_en' => 'Oversees community programs and initiatives and coordinates with local partners.',
        ],
        [
            'group' => TeamMember::GROUP_TEAM,
            'name_ar' => 'عمر الخطيب',
            'name_en' => 'Omar Al-Khatib',
            'role_ar' => 'باحث في السياسات البيئية',
            'role_en' => 'Environmental Policy Researcher',
            'bio_ar' => 'يعد الدراسات والأوراق التحليلية حول التشريعات البيئية وأثرها على المجتمعات المحلية.',
            'bio_en' => 'Prepares studies and policy papers on environmental legislation and its impact on local communities.',
        ],
        [
            'group' => TeamMember::GROUP_TEAM,
            'name_ar' => 'لينا درويش',
            'name_en' => 'Lina Darwish',
            'role_ar' => 'مسؤولة التوعية والإعلام',
            'role_en' => 'Outreach & Communications Officer',
            'bio_ar' => 'تقود حملات التوعية البيئية وتدير المحتوى الرقمي للمركز.',
            'bio_en' => 'Leads environmental awareness campaigns and manages the Center’s digital content.',
        ],
        [
            'group' => TeamMember::GROUP_TEAM,
            'name_ar' => 'محمد العلي',
            'name_en' => 'Mohammad Al-Ali',
            'role_ar' => 'مهندس رصد بيئي',
            'role_en' => 'Environmental Monitoring Engineer',
            'bio_ar' => 'يعمل على جمع بيانات جودة الهواء والمياه والتربة وتحليلها ميدانياً.',
            'bio_en' => 'Collects and analyses air, water and soil quality data in the field.',
        ],
        [
            'group' => TeamMember::GROUP_ADVISOR,
            'name_ar' => 'د. سمير حداد',
            'name_en' => 'Dr. Samir Haddad',
            'role_ar' => 'مستشار قانوني في التشريعات البيئية',
            'role_en' => 'Legal Advisor, Environmental Law',
            'bio_ar' => 'محامٍ وأكاديمي متخصص في القانون البيئي والحقوق المناخية.',
            'bio_en' => 'Lawyer and academic specialising in environmental law and climate rights.',
        ],
        [
            'group' => TeamMember::GROUP_ADVISOR,
            'name_ar' => 'د. هالة منصور',
            'name_en' => 'Dr. Hala Mansour',
            'role_ar' => 'مستشارة في التغير المناخي',
            'role_en' => 'Climate Change Advisor',
            'bio_ar' => 'خبيرة في نمذجة المخاطر المناخية وخطط التكيف على المستوى المحلي.',
            'bio_en' => 'Expert in climate risk modelling and local adaptation planning.',
        ],
        [
            'group' => TeamMember::GROUP_ADVISOR,
            'name_ar' => 'م. باسل قاسم',
            'name_en' => 'Eng. Basel Kassem',
            'role_ar' => 'مستشار في إدارة النفايات',
            'role_en' => 'Waste Management Advisor',
            'bio_ar' => 'خبرة طويلة في تصميم أنظمة الجمع والتدوير والمعالجة في المدن السورية.',
            'bio_en' => 'Long experience designing collection, recycling and treatment systems in Syrian cities.',
        ],
    ];

    public function run(): void
    {
        foreach (self::MEMBERS as $index => $member) {
            TeamMember::query()->firstOrNew(['name_ar' => $member['name_ar']])
                ->forceFill([...$member, 'is_published' => true, 'sort_order' => $index])
                ->save();
        }

        TeamMember::flushCache();
    }
}
