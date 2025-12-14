<div class="mb-8">
   {{-- 基本情報 --}}
   <h2 class="sub_heading mb-1">基本情報</h2>
   <div class="flex flex-row flex-wrap items-start gap-6 md:gap-10">
      {{-- 釣行日 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">釣行日</h2>
         <div class="p-2 w-28 border border-gray-500 rounded">
            {{ optional(optional($selectMemo)->fishing_date)->format('Y-m-d') ?? '-' }}
         </div>
      </div>
      {{-- 釣行時間 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">釣行時間</h2>
         <div class="flex items-center w-full">
            <div class="p-2 w-28 border border-gray-500 rounded">
               {{ optional(optional($selectMemo)->start_time)->format('H:i') ?? '-' }}
            </div>
            <span class="my-0 mx-1 text-gray-600">〜</span>
            <div class="p-2 w-28 border border-gray-500 rounded">
               {{ optional(optional($selectMemo)->end_time)->format('H:i') ?? '-' }}
            </div>
         </div>
      </div>
      {{-- 天気 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">天気</h2>
         <div class="p-2 w-28 border border-gray-500 rounded">
            {{ optional($selectMemo)->weather ?? '-' }}
         </div>
      </div>
      {{-- 気温 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">気温</h2>
         <div class="flex items-center gap-2">
            <div class="p-2 w-24 border border-gray-500 rounded">
               {{ optional($selectMemo)->air_temp !== null ? optional($selectMemo)->air_temp : '-' }}
            </div>
            <span class="text-gray-600">℃</span>
         </div>
      </div>
      {{-- 風向 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">風向</h2>
         <div class="p-2 w-28 border border-gray-500 rounded">
            {{ optional($selectMemo)->wind_dir ?? '-' }}
         </div>
      </div>
   </div>
</div>
