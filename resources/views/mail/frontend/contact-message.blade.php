<x-mail::message>
# رسالة جديدة من الموقع

**الاسم:** {{ $senderName }}<br>
**البريد الإلكتروني:** {{ $senderEmail }}<br>
@if ($topic)
**الموضوع:** {{ trans('frontend.contact.form.topics.'.$topic, [], 'ar') }}<br>
@endif
**لغة الصفحة:** {{ $visitorLocale === 'ar' ? 'العربية' : 'English' }}

<x-mail::panel>
{{ $body }}
</x-mail::panel>

يمكنك الرد مباشرة على هذه الرسالة للتواصل مع المرسل.
</x-mail::message>
