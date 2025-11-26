<div class="mb-8">
   <div class="md:gap-8 md:flex-row md:flex-wrap lg:gap-12 flex flex-col items-start gap-6">
      {{-- 釣果 --}}
      <div>
         <h2 class="sub_heading mb-1">釣果</h2>
         <div class="flex items-start">
            <div class="space-y-2 w-full">
               @forelse($getMemoFishResults as $result)
                  <div class="lg:gap-6 flex flex-wrap items-center gap-3 catch-row">
                     {{-- 魚名 --}}
                     <div class="p-2 w-60 border border-gray-500 rounded">
                        {{ $result['name'] }}
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
   </div>
</div>
