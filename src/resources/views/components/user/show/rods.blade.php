<div class="mb-5">
   <h2 class="sub_heading">釣り具</h2>
   {{-- 釣り具の表示の表示 --}}
   <div class="space-y-5 lg:space-y-1">
      <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-14">
         @forelse($getMemoRodsName as $result)
            <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10">
               {{-- 釣り竿名 --}}
               <div>
                  <h2 class="block text-sm font-semibold text-gray-700">釣り竿</h2>
                  <div class="p-2 w-60 border border-gray-500 rounded">
                     {{ $result['name'] }}
                  </div>
               </div>

               {{-- 道糸 --}}
               <div>
                  <h2 class="block text-sm text-gray-700">道糸</h2>
                  <div class="flex items-center gap-2">
                     <div class="p-2 w-24 border border-gray-500 rounded">
                        {{ $result['main_line'] }}
                     </div>
                     <span class="text-gray-600">m</span>
                  </div>
               </div>

            </div>
         @empty
            <div class="text-gray-600">道具は登録されていません。</div>
         @endforelse
      </div>
   </div>
</div>
