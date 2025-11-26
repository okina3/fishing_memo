{{-- 新規登録フォーム --}}
<div class="mb-5 flex flex-wrap items-start">
   {{-- 新規釣り場の登録 --}}
   <div class="mb-5 sm:mr-10">
      <form action="{{ route('user.masters.spot.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading mb-1">新規釣り場の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="spot_name" value="{{ old('spot_name') }}"
               placeholder="例:相模川上流">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（新規釣り場の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('spot_name')" />
      </form>
   </div>
   {{-- 新規エサの登録 --}}
   <div class="mb-5 sm:mr-10">
      <form action="{{ route('user.masters.bait.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading mb-1">新規エサの登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="bait_name" value="{{ old('bait_name') }}"
               placeholder="例: アオイソメ">
            <button type="submit" class="btn bg-blue-800 hover:bg-blue-700">保存</button>
         </div>
         {{-- エラーメッセージ（新規エサの登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('bait_name')" />
      </form>
   </div>
   {{-- 新規魚名の登録 --}}
   <div class="mb-5">
      <form action="{{ route('user.masters.fish-name.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading mb-1">新規魚名の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="fish_name" value="{{ old('fish_name') }}"
               placeholder="例: ヤマメ">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（新規魚名の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('fish_name')" />
      </form>
   </div>
</div>
