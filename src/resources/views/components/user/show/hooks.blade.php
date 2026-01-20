<div class="mb-5">
   <h2 class="sub_heading">仕掛け</h2>
   {{-- 仕掛けの表示の表示 --}}
   <div class="space-y-5 lg:space-y-1">
      <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-14">
         @forelse($getMemoHooksName as $result)
            <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10">
               {{-- 釣り針名 --}}
               <div>
                  <h2 class="block text-sm font-semibold text-gray-700">釣り針</h2>
                  <div class="p-2 w-60 border border-gray-500 rounded">
                     {{ $result['name'] }}
                  </div>
               </div>

               {{-- ハリス（太さ） --}}
               <div>
                  <h2 class="block text-sm text-gray-700">ハリス（太さ）</h2>
                  <div class="flex items-center gap-2">
                     <div class="p-2 w-24 border border-gray-500 rounded">
                        {{ $result['leader_size'] }}
                     </div>
                     <span class="text-gray-600">号</span>
                  </div>
               </div>

               {{-- 上ハリス --}}
               <div>
                  <h2 class="block text-sm text-gray-700">上ハリス</h2>
                  <div class="flex items-center gap-2">
                     <div class="p-2 w-24 border border-gray-500 rounded">
                        {{ $result['leader_upper_cm'] }}
                     </div>
                     <span class="text-gray-600">cm</span>
                  </div>
               </div>

               {{-- 下ハリス --}}
               <div>
                  <h2 class="block text-sm text-gray-700">下ハリス</h2>
                  <div class="flex items-center gap-2">
                     <div class="p-2 w-24 border border-gray-500 rounded">
                        {{ $result['leader_lower_cm'] }}
                     </div>
                     <span class="text-gray-600">cm</span>
                  </div>
               </div>

            </div>
         @empty
            <div class="text-gray-600">仕掛けは登録されていません。</div>
         @endforelse
      </div>
   </div>
</div>
