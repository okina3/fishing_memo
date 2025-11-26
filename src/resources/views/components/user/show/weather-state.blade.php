<div class="mb-8">
   {{-- 気象状態 --}}
   <h2 class="sub_heading mb-1">気象状態</h2>
   <div class="sm:flex-row sm:flex-wrap sm:gap-6 md:gap-12 flex flex-col items-start gap-6">
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
            <div class="p-2 w-24 border border-gray-500 text-right rounded">
               {{ optional($selectMemo)->air_temp ?? '-' }}
            </div>
            <span class="text-gray-600">℃</span>
         </div>
      </div>
      {{-- 最大風速 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">最大風速</h2>
         <div class="flex items-center gap-2">
            <div class="p-2 w-24 border border-gray-500 text-right rounded">
               {{ optional($selectMemo)->max_wind ?? '-' }}
            </div>
            <span class="text-gray-600">m/s</span>
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
