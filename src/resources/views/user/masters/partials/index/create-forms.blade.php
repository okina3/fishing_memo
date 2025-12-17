{{-- 新規登録フォーム --}}
<div class="mb-5 flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10">
   {{-- 釣り場の登録 --}}
   <div class="sm:mr-10">
      <form action="{{ route('user.spot.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2 mb-1">釣り場の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="spot_name" value="{{ old('spot_name') }}"
               placeholder="例: T県 サンプル川上流域">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（釣り場の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('spot_name')" />
      </form>
   </div>
   {{-- 釣り竿の登録 --}}
   <div class="sm:mr-10">
      <form action="{{ route('user.rod.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2 mb-1">釣り竿の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="rod_name" value="{{ old('rod_name') }}"
               placeholder="例: D社 サンプルロッド 12尺">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（釣り竿の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('rod_name')" />
      </form>
   </div>
   {{-- エサの登録 --}}
   <div class="sm:mr-10">
      <form action="{{ route('user.bait.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2 mb-1">エサの登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="bait_name" value="{{ old('bait_name') }}"
               placeholder="例: アオイソメ">
            <button type="submit" class="btn bg-blue-800 hover:bg-blue-700">保存</button>
         </div>
         {{-- エラーメッセージ（エサの登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('bait_name')" />
      </form>
   </div>
   {{-- 魚名の登録 --}}
   <div>
      <form action="{{ route('user.fish-name.store') }}" method="POST">
         @csrf
         <h2 class="sub_heading-2 mb-1">魚名の登録</h2>
         <div class="flex gap-2 items-center">
            <input class="w-60 rounded" type="text" name="fish_name" value="{{ old('fish_name') }}"
               placeholder="例: マブナ">
            <button class="btn bg-blue-800 hover:bg-blue-700" type="submit">保存</button>
         </div>
         {{-- エラーメッセージ（魚名の登録） --}}
         <x-input-error class="mt-2" :messages="$errors->get('fish_name')" />
      </form>
   </div>
</div>
