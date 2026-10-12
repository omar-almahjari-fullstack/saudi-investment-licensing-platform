@extends('layouts.app')

@section('title','سياسة الخصوصية')

@section('content')
<div style="min-height:70vh;direction:rtl;color:#fff;padding:28px 18px;max-width:900px;margin:0 auto">
    <h1 style="margin-top:10px">سياسة الخصوصية</h1>
    <p style="color:#9fb0bf">هذه صفحة تجريبية لسياسة الخصوصية. يُمكنك تعديل المحتوى لاحقًا بحسب متطلباتك القانونية.</p>
    <div class="glass-card" style="padding:18px;margin-top:12px">
        <ul style="line-height:1.9;color:#cbd5e1">
            <li>نلتزم بحماية بياناتك الشخصية وعدم مشاركتها مع أطراف غير مصرح لهم.</li>
            <li>قد نستخدم ملفات تعريف الارتباط لتحسين تجربة الاستخدام.</li>
            <li>يمكنك التواصل معنا لطلب حذف البيانات أو تعديلها.</li>
        </ul>
    </div>
    <div style="margin-top:18px">
        <a href="{{ url()->previous() }}" class="btn-secondary">العودة</a>
    </div>
</div>
@endsection
