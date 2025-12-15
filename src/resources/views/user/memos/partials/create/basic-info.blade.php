<div class="mb-4">
   {{-- 基本情報 --}}
   <h2 class="sub_heading">基本情報</h2>
   <div class="flex flex-row flex-wrap items-start gap-x-6 gap-y-1 md:gap-x-10">
      {{-- 釣行日 --}}
      <div>
         <label class="block text-sm text-gray-700">釣行日</label>
         <input class="rounded" type="date" name="fishing_date" value="{{ old('fishing_date') }}"
            max="{{ now()->toDateString() }}" />
         {{-- エラーメッセージ（釣行日） --}}
         <x-input-error class="mt-2" :messages="$errors->get('fishing_date')" />
      </div>
      {{-- 釣行時間 --}}
      <div>
         <label class="block text-sm text-gray-700">釣行時間</label>
         <div class="flex items-center w-full">
            <input class="text-center rounded" type="time" name="start_time" value="{{ old('start_time') }}"
               step="60" />
            <span class="my-0 mx-1 text-gray-600">〜</span>
            <input class="text-center rounded" type="time" name="end_time" value="{{ old('end_time') }}"
               step="60" />
         </div>
         {{-- エラーメッセージ（釣行時間） --}}
         <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
         <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
      </div>
      {{-- 天気 --}}
      <div>
         <label class="block text-sm text-gray-700">天気</label>
         <select name="weather" class="w-28 rounded">
            <option value="" @selected(old('weather', '') === '')>未選択</option>
            <option value="晴れ" @selected(old('weather') === '晴れ')>晴れ</option>
            <option value="曇り" @selected(old('weather') === '曇り')>曇り</option>
            <option value="雨" @selected(old('weather') === '雨')>雨</option>
            <option value="その他" @selected(old('weather') === 'その他')>その他</option>
         </select>
         {{-- エラーメッセージ（天気） --}}
         <x-input-error class="mt-2" :messages="$errors->get('weather')" />
      </div>
      {{-- 気温 --}}
      <div>
         <label class="block text-sm text-gray-700">気温</label>
         <div class="flex items-center gap-2">
            <input class="w-24 rounded text-right" type="number" name="air_temp" value="{{ old('air_temp') }}"
               placeholder="0" inputmode="numeric" step="1" min="0" max="60" />
            <span class="text-gray-600">℃</span>
         </div>
         {{-- エラーメッセージ（気温） --}}
         <x-input-error class="mt-2" :messages="$errors->get('air_temp')" />
      </div>
      {{-- 風向 --}}
      <div>
         <label class="block text-sm text-gray-700">風向</label>
         <select name="wind_dir" class="w-28 rounded">
            <option value="" @selected(old('wind_dir', '') === '')>未選択</option>
            <option value="北" @selected(old('wind_dir') === '北')>北</option>
            <option value="北東" @selected(old('wind_dir') === '北東')>北東</option>
            <option value="東" @selected(old('wind_dir') === '東')>東</option>
            <option value="南東" @selected(old('wind_dir') === '南東')>南東</option>
            <option value="南" @selected(old('wind_dir') === '南')>南</option>
            <option value="南西" @selected(old('wind_dir') === '南西')>南西</option>
            <option value="西" @selected(old('wind_dir') === '西')>西</option>
            <option value="北西" @selected(old('wind_dir') === '北西')>北西</option>
         </select>
         {{-- エラーメッセージ（風向） --}}
         <x-input-error class="mt-2" :messages="$errors->get('wind_dir')" />
      </div>
   </div>
</div>
