<div class="mb-5">
   {{-- 場所の入力 --}}
   <h2 class="sub_heading">場所</h2>
   
   {{-- 釣り場の表示 --}}
   <div class="space-y-5 lg:space-y-1">
      @forelse($getMemoSpotsResults as $result)
         <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10">
            {{-- 釣り場名 --}}
            <div>
               <h2 class="block text-sm font-semibold text-gray-700">釣り場</h2>
               <div class="p-2 w-60 border border-gray-500 rounded">
                  {{ $result['name'] }}
               </div>
            </div>

            {{-- 流れの有無 --}}
            <div>
               <h2 class="block text-sm text-gray-700">流れの有無</h2>
               <div class="p-2 w-32 border border-gray-500 rounded">
                  {{ $result['river_flow'] }}
               </div>
            </div>

            {{-- 濁り --}}
            <div>
               <h2 class="block text-sm text-gray-700">濁り</h2>
               <div class="p-2 w-32 border border-gray-500 rounded">
                  {{ $result['turbidity'] }}
               </div>
            </div>

            {{-- 水深 --}}
            <div>
               <h2 class="block text-sm text-gray-700">水深</h2>
               <div class="flex items-center gap-2">
                  <div class="p-2 w-24 border border-gray-500 rounded">
                     {{ $result['water_level'] }}
                  </div>
                  <span class="text-gray-600">m</span>
               </div>
            </div>

            {{-- 水温 --}}
            <div>
               <h2 class="block text-sm text-gray-700">水温</h2>
               <div class="flex items-center gap-2">
                  <div class="p-2 w-24 border border-gray-500 rounded">
                     {{ $result['water_temp'] }}
                  </div>
                  <span class="text-gray-600">℃</span>
               </div>
            </div>

         </div>
      @empty
         <div class="text-gray-600">場所は登録されていません。</div>
      @endforelse
   </div>
</div>
