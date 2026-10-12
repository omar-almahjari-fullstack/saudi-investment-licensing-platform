@extends('layouts.app')

@section('title','الشروط والأحكام')

@section('content')
<div style="min-height:70vh;direction:rtl;color:#fff;padding:28px 18px;max-width:900px;margin:0 auto">
    <h1 style="margin-top:10px">الشروط والأحكام</h1>
    <p style="color:#9fb0bf">هذه صفحة تجريبية للشروط والأحكام. يُمكنك تعديل المحتوى لاحقًا بحسب متطلباتك القانونية.</p>
    <div class="glass-card" style="padding:18px;margin-top:12px">
        <ul style="line-height:1.9;color:#cbd5e1">
            <li>استخدام المنصة يخضع للأنظمة والقوانين المعمول بها.</li>
            <li>يجب تقديم معلومات دقيقة ومحدثة أثناء التسجيل.</li>
            <li>يُحظر إساءة استخدام الخدمات أو محاولة الوصول غير المصرح به.</li>
        </ul>
    </div>
    <div style="margin-top:18px">
        <a href="{{ url()->previous() }}" class="btn-secondary">العودة</a>
    </div>
</div>
@endsection
