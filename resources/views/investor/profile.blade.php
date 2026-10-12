@extends('layouts.app')

@section('content')
<div class="app-content">
  <div class="digital-background"></div>

  <header style="position:relative;z-index:10;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <h1 class="glow-text" style="font-size:28px;margin:8px 0;">ملفي الاستثماري</h1>
      <p style="color:#cbd5e1;">تحديث بيانات المستثمر الخاصة بك</p>
    </div>
  </header>

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;max-width:820px;margin:0 auto;">
      <form method="POST" action="{{ route('investor.profile.update') }}" enctype="multipart/form-data" class="glass-card" style="padding:16px;">
        @csrf
        @if(session('status'))
          <div style="background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.3);padding:10px;border-radius:10px;color:#bbf7d0;margin-bottom:12px;">
            {{ session('status') }}
          </div>
        @endif
        @if($errors->any())
          <div style="background:rgba(127,29,29,0.3);border:1px solid rgba(127,29,29,0.5);padding:10px;border-radius:10px;color:#fecaca;margin-bottom:12px;">
            {{ $errors->first() }}
          </div>
        @endif
        <div class="grid-2">
          <div>
            <label style="display:block;color:#cbd5e1;margin-bottom:6px;">الجنسية</label>
            <input type="text" name="nationality" value="{{ old('nationality', $investor->nationality) }}" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
          </div>
          <div>
            <label style="display:block;color:#cbd5e1;margin-bottom:6px;">المدينة</label>
            <input type="text" name="city" value="{{ old('city', $investor->city) }}" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
          </div>
          <div>
            <label style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الحساب البنكي (IBAN)</label>
            <input type="text" name="bank_account" value="{{ old('bank_account', $investor->bank_account) }}" placeholder="SAxx xxxx xxxx xxxx xxxx xx" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
            @error('bank_account')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;color:#cbd5e1;margin-bottom:6px;">نوع المستثمر</label>
            <select name="investor_type" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;">
              <option value="">—</option>
              <option value="individual" {{ old('investor_type', $investor->investor_type) === 'individual' ? 'selected' : '' }}>فردي</option>
              <option value="institutional" {{ old('investor_type', $investor->investor_type) === 'institutional' ? 'selected' : '' }}>مؤسسي</option>
            </select>
          </div>
          <div>
            <label style="display:block;color:#cbd5e1;margin-bottom:6px;">المبلغ المتوقع</label>
            <input type="text" name="expected_amount" value="{{ old('expected_amount', $investor->expected_amount) }}" placeholder="مثال: 1000-10000" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
          </div>
          <div>
            <label style="display:block;color:#cbd5e1;margin-bottom:6px;">مدة الاستثمار</label>
            <input type="text" name="investment_duration" value="{{ old('investment_duration', $investor->investment_duration) }}" placeholder="مثال: 6months, 1year" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
          </div>
          <div style="grid-column:1/-1;">
            <label style="display:block;color:#cbd5e1;margin-bottom:6px;">ملاحظة</label>
            <textarea name="notes" rows="3" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;">{{ old('notes', $investor->notes) }}</textarea>
            @error('notes')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div style="display:flex;align-items:center;gap:10px;">
            <input type="checkbox" name="marketing_opt_in" value="1" {{ old('marketing_opt_in', $investor->marketing_opt_in) ? 'checked' : '' }} />
            <span style="color:#cbd5e1;">أوافق على استلام العروض والفرص الاستثمارية</span>
          </div>
        </div>

        <!-- صور الهوية -->
        <div style="margin-top:16px;">
          <h3 style="color:#fff;margin-bottom:10px;">صور الهوية</h3>
      @php
      // Prefer canonical user identity images when available; fall back to investor files
      $frontUrl = null;
      $backUrl = null;
      // user identity fields may be present on $investor->user relationship
      if (\Illuminate\Support\Facades\Schema::hasColumn('users','identity_image_front') && !empty($investor->user) && !empty($investor->user->identity_image_front)) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($investor->user->identity_image_front)) {
          $frontUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($investor->user->identity_image_front);
        } else {
          $frontUrl = route('user.image', ['user' => $investor->user->id, 'side' => 'front']);
        }
      } elseif (!empty($investor->identity_image_front)) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($investor->identity_image_front)) {
          $frontUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($investor->identity_image_front);
        } else {
          $frontUrl = route('investor.image', ['investor' => $investor->id, 'side' => 'front']);
        }
      }

      if (\Illuminate\Support\Facades\Schema::hasColumn('users','identity_image_back') && !empty($investor->user) && !empty($investor->user->identity_image_back)) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($investor->user->identity_image_back)) {
          $backUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($investor->user->identity_image_back);
        } else {
          $backUrl = route('user.image', ['user' => $investor->user->id, 'side' => 'back']);
        }
      } elseif (!empty($investor->identity_image_back)) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($investor->identity_image_back)) {
          $backUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($investor->identity_image_back);
        } else {
          $backUrl = route('investor.image', ['investor' => $investor->id, 'side' => 'back']);
        }
      }
      @endphp
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div>
              <label style="display:block;color:#cbd5e1;margin-bottom:6px;">الوجه الأمامي</label>
              @if($frontUrl)
                <a href="{{ $frontUrl }}" target="_blank" style="display:inline-block;margin-bottom:8px;">
                  <img src="{{ $frontUrl }}" alt="الهوية - أمامي" style="max-width:100%;border-radius:8px;border:1px solid #334155;" />
                </a>
              @endif
              @if(!$frontUrl)
                <input type="file" accept="image/*" name="identity_image_front" style="width:100%;background:#0b1220;border:1px solid #334155;color:#fff;padding:10px;border-radius:8px;" />
              @else
                <div style="margin-top:8px;">
                  <a href="{{ $frontUrl }}" target="_blank">
                    <img src="{{ $frontUrl }}" alt="الهوية - أمامي" style="max-width:100%;border-radius:8px;border:1px solid #334155;" />
                  </a>
                </div>
              @endif
              @error('identity_image_front')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
              <label style="display:block;color:#cbd5e1;margin-bottom:6px;">الوجه الخلفي</label>
              @if($backUrl)
                <a href="{{ $backUrl }}" target="_blank" style="display:inline-block;margin-bottom:8px;">
                  <img src="{{ $backUrl }}" alt="الهوية - خلفي" style="max-width:100%;border-radius:8px;border:1px solid #334155;" />
                </a>
              @endif
              @if(!$backUrl)
                <input type="file" accept="image/*" name="identity_image_back" style="width:100%;background:#0b1220;border:1px solid #334155;color:#fff;padding:10px;border-radius:8px;" />
              @else
                <div style="margin-top:8px;">
                  <a href="{{ $backUrl }}" target="_blank">
                    <img src="{{ $backUrl }}" alt="الهوية - خلفي" style="max-width:100%;border-radius:8px;border:1px solid #334155;" />
                  </a>
                </div>
              @endif
              @error('identity_image_back')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
          </div>
          <p style="color:#94a3b8;font-size:12px;margin-top:8px;">يسمح برفع صور PNG / JPG حتى 5MB لكل صورة.</p>
        </div>
        <div style="margin-top:16px;">
          <button type="submit" class="btn-primary" style="padding:10px 24px;">حفظ</button>
          <a href="{{ route('investor.dashboard') }}" class="btn-secondary" style="margin-inline-start:10px;">رجوع</a>
        </div>
      </form>
    </div>
  </section>
</div>
@endsection
