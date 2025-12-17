<div class="mb-8">
   <h2 class="sub_heading">釣果</h2>
   {{-- 釣果の表示 --}}
   <div class="space-y-5 lg:space-y-1">
      @forelse($getMemoFishResults as $result)
         <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10">
            {{-- 魚名 --}}
            <div>
               <label class="block text-sm font-semibold text-gray-700">魚名</label>
               <div class="p-2 w-60 border border-gray-500 rounded">
                  {{ $result['name'] }}
               </div>
            </div>

            {{-- 釣果（匹） --}}
            <div>
               <label class="block text-sm text-gray-700">匹数</label>
               <div class="flex items-center gap-2">
                  <div class="p-2 md:w-24 w-20 border border-gray-500 text-right rounded">
                     {{ $result['count'] }}
                  </div>
                  <span class="text-gray-600">匹</span>
               </div>
            </div>

            {{-- サイズ（cm） --}}
            <div>
               <label class="block text-sm text-gray-700">最大サイズ</label>
               <div class="flex items-center gap-2">
                  <div class="p-2 md:w-24 w-20 border border-gray-500 text-right rounded">
                     {{ $result['length'] }}
                  </div>
                  <span class="text-gray-600">cm</span>
               </div>
            </div>
         </div>
      @empty
         <div class="text-gray-600">釣果は登録されていません。</div>
      @endforelse
   </div>
</div>
