<div class="mb-8">
   {{-- 釣果の表示 --}}
   <h2 class="sub_heading mb-1">釣果</h2>

   <div class="flex flex-wrap items-center gap-10">
      {{-- 釣果 --}}
      <div class="w-full space-y-2">
         @forelse($getMemoFishResults as $result)
            <div class="flex flex-wrap items-center gap-12">
               {{-- 魚名 --}}
               <div class="md:w-auto w-full">
                  <div class="p-2 w-60 border border-gray-500 rounded">
                     {{ $result['name'] }}
                  </div>
               </div>

               {{-- 釣果（匹） --}}
               <div class="flex items-center gap-2">
                  <div class="p-2 md:w-24 w-20 border border-gray-500 text-right rounded">
                     {{ $result['count'] }}
                  </div>
                  <span class="text-gray-600">匹</span>
               </div>

               {{-- サイズ（cm） --}}
               <div class="flex items-center gap-2">
                  <div class="p-2 md:w-24 w-20 border border-gray-500 text-right rounded">
                     {{ $result['length'] }}
                  </div>
                  <span class="text-gray-600">cm</span>
               </div>
            </div>
         @empty
            <div class="text-gray-600">釣果は登録されていません。</div>
         @endforelse
      </div>
   </div>
</div>
