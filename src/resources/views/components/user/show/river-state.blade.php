<div class="mb-8">
   {{-- 川の状態 --}}
   <h2 class="sub_heading mb-1">川の状態</h2>
   <div class="sm:flex-row sm:flex-wrap sm:gap-6 md:gap-10 flex flex-col items-start gap-6">
      {{-- 川の流れ --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">川の流れ</h2>
         <div class="p-2 w-32 border border-gray-500 rounded">
            {{ optional($selectMemo)->river_flow ?? '-' }}
         </div>
      </div>
      {{-- 濁り --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">濁り</h2>
         <div class="p-2 w-32 border border-gray-500 rounded">
            {{ optional($selectMemo)->turbidity ?? '-' }}
         </div>
      </div>
      {{-- 水中のゴミ --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">水中のゴミ</h2>
         <div class="p-2 w-32 border border-gray-500 rounded">
            {{ optional($selectMemo)->debris ?? '-' }}
         </div>
      </div>
      {{-- 水位 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">水位</h2>
         <div class="flex items-center gap-2">
            <div class="p-2 w-24 border border-gray-500 text-right rounded">
               {{ optional($selectMemo)->water_level ?? '-' }}
            </div>
            <span class="text-gray-600">m</span>
         </div>
      </div>
      {{-- 水温 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">水温</h2>
         <div class="flex items-center gap-2">
            <div class="p-2 border border-gray-500 w-24 text-right rounded">
               {{ optional($selectMemo)->water_temp ?? '-' }}
            </div>
            <span class="text-gray-600">℃</span>
         </div>
      </div>
   </div>
</div>
