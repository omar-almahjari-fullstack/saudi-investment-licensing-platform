<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuccessStoriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
                // top featured
                ['name'=>'عبدالرحمن بن سعد المطيري','avatar'=>'ع.س','location'=>'الرياض','amount'=>'750,000 ريال','returns'=>'375,000 ريال','duration'=>'شهر واحد','profit'=>'50%','category'=>'large','featured'=>1,'is_top'=>1,'story'=>'استثمار استثنائي حقق لي أرباحاً تفوق التوقعات. المنصة رائدة في مجالها.','testimonial'=>'استثمار استثنائي حقق لي أرباحاً تفوق التوقعات. المنصة رائدة في مجالها.'],
                ['name'=>'هند بنت علي الشهري','avatar'=>'ه.ع','location'=>'مكة المكرمة','amount'=>'200,000 ريال','returns'=>'96,000 ريال','duration'=>'شهر واحد','profit'=>'48%','category'=>'large','featured'=>1,'is_top'=>1,'story'=>'كامرأة عاملة، كنت أبحث عن فرصة استثمارية موثوقة. وجدت ضالتي هنا.','testimonial'=>'كامرأة عاملة، كنت أبحث عن فرصة استثمارية موثوقة. وجدت ضالتي هنا.'],
                // additional stories
                ['name'=>'أحمد محمد الغامدي','avatar'=>'أ.م','location'=>'الرياض','amount'=>'15,000 ريال','returns'=>'5,250 ريال','duration'=>'شهر واحد','profit'=>'35%','category'=>'small','featured'=>1,'is_top'=>0,'story'=>'بدأت استثماري...','testimonial'=>'أفضل قرار استثماري...'],
                ['name'=>'فاطمة عبدالله النجار','avatar'=>'ف.ع','location'=>'جدة','amount'=>'45,000 ريال','returns'=>'21,150 ريال','duration'=>'شهر واحد','profit'=>'47%','category'=>'small','featured'=>1,'is_top'=>0,'story'=>'كانت تجربتي...','testimonial'=>'عوائد ممتازة...'],
                ['name'=>'سعد بن محمد الزهراني','avatar'=>'س.م','location'=>'الدمام','amount'=>'100,000 ريال','returns'=>'47,000 ريال','duration'=>'شهر واحد','profit'=>'47%','category'=>'large','featured'=>1,'is_top'=>0,'story'=>'قررت الاستثمار...','testimonial'=>'استثمار آمن...'],
                ['name'=>'نورا أحمد الحربي','avatar'=>'ن.أ','location'=>'الطائف','amount'=>'7,200 ريال','returns'=>'2,088 ريال','duration'=>'شهر واحد','profit'=>'29%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'بدأت بمبلغ صغير...','testimonial'=>'منصة موثوقة...'],
                ['name'=>'خالد بن عبدالرحمن السعد','avatar'=>'خ.ع','location'=>'المدينة المنورة','amount'=>'25,000 ريال','returns'=>'9,750 ريال','duration'=>'شهر واحد','profit'=>'39%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'التجربة كانت...','testimonial'=>'أفضل منصة...'],
                ['name'=>'محمد بن عبدالله القحطاني','avatar'=>'م.ع','location'=>'أبها','amount'=>'25,000 ريال','returns'=>'9,750 ريال','duration'=>'شهر واحد','profit'=>'39%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'نتائج فاقت...','testimonial'=>'نتائج فاقت...'],
                ['name'=>'رانيا أحمد الحربي','avatar'=>'ر.أ','location'=>'الطائف','amount'=>'150,000 ريال','returns'=>'70,500 ريال','duration'=>'شهر واحد','profit'=>'47%','category'=>'large','featured'=>0,'is_top'=>0,'story'=>'استثمار مربح...','testimonial'=>'استثمار مربح...'],
                ['name'=>'خالد بن ناصر العتيبي','avatar'=>'خ.ن','location'=>'الخرج','amount'=>'8,000 ريال','returns'=>'2,480 ريال','duration'=>'شهر واحد','profit'=>'31%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'بداية ممتازة...','testimonial'=>'بداية ممتازة...'],
                ['name'=>'سارة بنت محمد الدوسري','avatar'=>'س.م','location'=>'الخبر','amount'=>'60,000 ريال','returns'=>'27,600 ريال','duration'=>'شهر واحد','profit'=>'46%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'تجربة استثنائية...','testimonial'=>'تجربة استثنائية...'],
                ['name'=>'عبدالعزيز بن سليمان الراجحي','avatar'=>'ع.س','location'=>'بريدة','amount'=>'300,000 ريال','returns'=>'144,000 ريال','duration'=>'شهر واحد','profit'=>'48%','category'=>'large','featured'=>0,'is_top'=>0,'story'=>'استثمار احترافي...','testimonial'=>'استثمار احترافي...'],
                ['name'=>'مريم بنت عبدالرحمن الفيصل','avatar'=>'م.ع','location'=>'الدمام','amount'=>'35,000 ريال','returns'=>'15,050 ريال','duration'=>'شهر واحد','profit'=>'43%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'منصة موثوقة...','testimonial'=>'منصة موثوقة...'],
                ['name'=>'يوسف بن أحمد العمري','avatar'=>'ي.أ','location'=>'حائل','amount'=>'12,000 ريال','returns'=>'3,960 ريال','duration'=>'شهر واحد','profit'=>'33%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'استثمار آمن...','testimonial'=>'استثمار آمن...'],
                ['name'=>'نادية بنت فهد الشمري','avatar'=>'ن.ف','location'=>'تبوك','amount'=>'85,000 ريال','returns'=>'39,950 ريال','duration'=>'شهر واحد','profit'=>'47%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'تجربة رائعة...','testimonial'=>'تجربة رائعة...'],
                ['name'=>'ماجد بن عبدالله الغامدي','avatar'=>'م.ع','location'=>'الباحة','amount'=>'500,000 ريال','returns'=>'250,000 ريال','duration'=>'شهر واحد','profit'=>'50%','category'=>'large','featured'=>0,'is_top'=>0,'story'=>'استثمار كبير...','testimonial'=>'استثمار كبير...'],
                ['name'=>'لطيفة بنت سعد القرشي','avatar'=>'ل.س','location'=>'مكة المكرمة','amount'=>'18,000 ريال','returns'=>'6,660 ريال','duration'=>'شهر واحد','profit'=>'37%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'استثمار مثالي...','testimonial'=>'استثمار مثالي...'],
                ['name'=>'بندر بن محمد آل سعود','avatar'=>'ب.م','location'=>'الرياض','amount'=>'1,200,000 ريال','returns'=>'600,000 ريال','duration'=>'شهر واحد','profit'=>'50%','category'=>'large','featured'=>0,'is_top'=>0,'story'=>'استثمار ضخم...','testimonial'=>'استثمار ضخم...'],
                ['name'=>'أمل بنت عبدالعزيز الحمود','avatar'=>'أ.ع','location'=>'القصيم','amount'=>'28,000 ريال','returns'=>'11,480 ريال','duration'=>'شهر واحد','profit'=>'41%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'عوائد ممتازة...','testimonial'=>'عوائد ممتازة...'],
                ['name'=>'فيصل بن خالد الجبري','avatar'=>'ف.خ','location'=>'الأحساء','amount'=>'95,000 ريال','returns'=>'44,650 ريال','duration'=>'شهر واحد','profit'=>'47%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'استثمار موفق...','testimonial'=>'استثمار موفق...'],
                ['name'=>'هيفاء بنت صالح العوضي','avatar'=>'ه.ص','location'=>'جازان','amount'=>'42,000 ريال','returns'=>'18,900 ريال','duration'=>'شهر واحد','profit'=>'45%','category'=>'small','featured'=>0,'is_top'=>0,'story'=>'تجربة ناجحة...','testimonial'=>'تجربة ناجحة...'],
                ['name'=>'عادل بن محمد الزهراني','avatar'=>'ع.م','location'=>'الطائف','amount'=>'180,000 ريال','returns'=>'84,600 ريال','duration'=>'شهر واحد','profit'=>'47%','category'=>'large','featured'=>0,'is_top'=>0,'story'=>'استثمار احترافي...','testimonial'=>'استثمار احترافي...']
            ];

            foreach ($rows as $r) {
                // key to identify unique story: name + location
                $match = ['name' => $r['name'], 'location' => $r['location']];
                $exists = DB::table('success_stories')->where($match)->exists();

                $data = array_merge($r, [
                    // mirror older field names into new columns
                    'investment_amount' => $r['amount'] ?? null,
                    'profit_rate' => $r['profit'] ?? null,
                    'returns' => $r['returns'] ?? null,
                    'duration' => $r['duration'] ?? null,
                    'avatar' => $r['avatar'] ?? null,
                    'category' => $r['category'] ?? null,
                    'featured' => $r['featured'] ?? 0,
                    'is_top' => $r['is_top'] ?? 0,
                    'story' => $r['story'] ?? null,
                    'testimonial' => $r['testimonial'] ?? null,
                ]);

                if ($exists) {
                    // update existing row (do not replace id or created_at)
                    $updateData = $data;
                    unset($updateData['id']);
                    unset($updateData['created_at']);
                    unset($updateData['updated_at']);
                    $updateData['updated_at'] = $now;
                    DB::table('success_stories')->where($match)->update($updateData);
                } else {
                    // insert new row with UUID
                    DB::table('success_stories')->insert(array_merge($data, [
                        'id' => (string) Str::uuid(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]));
                }
            }
    }
}
