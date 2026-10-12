@extends('layouts.app')

@section('title','لوحة تحكم المدير')

@section('content')

<div class="min-h-screen p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">لوحة تحكم المدير</h1>
                <p class="text-gray-400">نظرة عامة على الإحصاءات والمستخدمين والاستثمارات</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 rounded bg-cyan-600 text-black font-semibold">المدفوعات</a>
                <a href="{{ route('admin.withdrawals.index') }}" class="px-4 py-2 rounded bg-emerald-500 text-black font-semibold">السحوبات</a>
                <a href="{{ route('admin.ledger.index') }}" class="px-4 py-2 rounded bg-amber-400 text-black font-semibold">دفتر المنصة</a>
                <a href="{{ route('admin.logout') }}" class="px-4 py-2 rounded bg-gray-800 text-white">تسجيل خروج</a>
            </div>
        </div>

        <!-- بيانات وهمية مُضمّنة server-side كما طلبت -->
        <?php
            $fakeStats = [
                'users' => 1280,
                'investments' => 342,
                'licenses' => 58,
            ];

            $fakeUsers = [
                ['name'=>'أحمد العلي','email'=>'ahmed@example.com','created_at'=>'2025-10-01'],
                ['name'=>'سارة محمد','email'=>'sara@example.com','created_at'=>'2025-10-03'],
                ['name'=>'محمد خالد','email'=>'m.khalid@example.com','created_at'=>'2025-10-05'],
                ['name'=>'ليلى حسين','email'=>'laila@example.com','created_at'=>'2025-10-06'],
                ['name'=>'يوسف نزار','email'=>'yousef@example.com','created_at'=>'2025-10-07'],
            ];

            $fakeInvestments = [
                ['id'=>101,'title'=>'مشروع الطاقة الشمسية','amount'=>'1,200,000 SAR','status'=>'قيد المراجعة'],
                ['id'=>102,'title'=>'مركز لوجستي المتكامل','amount'=>'3,500,000 SAR','status'=>'مقبول'],
                ['id'=>103,'title'=>'منتجع سياحي','amount'=>'800,000 SAR','status'=>'مرفوض'],
            ];

            $fakeLicenses = [
                ['id'=>201,'name'=>'ترخيص وساطة','status'=>'مفعّل'],
                ['id'=>202,'name'=>'ترخيص استشارات','status'=>'بانتظار'],
                ['id'=>203,'name'=>'ترخيص تمويل','status'=>'مفعّل'],
            ];
        ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-gray-900 text-white rounded">
                <div class="text-sm text-gray-400">المستخدمين</div>
                <div class="text-3xl font-semibold mt-2">{{ $fakeStats['users'] }}</div>
            </div>
            <div class="p-4 bg-gray-900 text-white rounded">
                <div class="text-sm text-gray-400">الاستثمارات</div>
                <div class="text-3xl font-semibold mt-2">{{ $fakeStats['investments'] }}</div>
            </div>
            <div class="p-4 bg-gray-900 text-white rounded">
                <div class="text-sm text-gray-400">التراخيص</div>
                <div class="text-3xl font-semibold mt-2">{{ $fakeStats['licenses'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <section class="bg-gray-900 p-4 rounded text-white">
                <h2 class="font-semibold mb-3">المستخدمون الجدد</h2>
                @foreach($fakeUsers as $u)
                    <div class="py-2 border-b border-gray-800">
                        <div class="font-medium">{{ $u['name'] }}</div>
                        <div class="text-xs text-gray-400">{{ $u['email'] }} — {{ $u['created_at'] }}</div>
                    </div>
                @endforeach
            </section>

            <section class="bg-gray-900 p-4 rounded text-white">
                <h2 class="font-semibold mb-3">الاستثمارات الأخيرة</h2>
                @foreach($fakeInvestments as $i)
                    <div class="py-2 border-b border-gray-800">
                        <div class="font-medium">{{ $i['title'] }}</div>
                        <div class="text-xs text-gray-400">المبلغ: {{ $i['amount'] }} — الحالة: {{ $i['status'] }}</div>
                    </div>
                @endforeach
            </section>
        </div>

        <div class="mt-6">
            <section class="bg-gray-900 p-4 rounded text-white">
                <h2 class="font-semibold mb-3">التراخيص</h2>
                @foreach($fakeLicenses as $l)
                    <div class="py-2 border-b border-gray-800">
                        <div class="font-medium">{{ $l['name'] }}</div>
                        <div class="text-xs text-gray-400">الحالة: {{ $l['status'] }}</div>
                    </div>
                @endforeach
            </section>
        </div>
    </div>
</div>

@endsection
