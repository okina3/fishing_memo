<div class="mb-8">
   {{-- 基本情報 --}}
   <h2 class="sub_heading mb-1">基本情報</h2>
   <div class="md:flex-row md:flex-wrap md:gap-14 flex flex-col items-start gap-6">
      {{-- 釣行日 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">釣行日</h2>
         <div class="p-2 sm:w-44 md:w-44 w-full border border-gray-500 rounded">
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
      {{-- 釣り場 --}}
      <div>
         <h2 class="mb-1 block text-sm text-gray-700">釣り場</h2>
         <div class="p-2 w-60 border border-gray-500 rounded">
            {{ optional($selectMemo->spot)->name ?? '-' }}
         </div>
      </div>
   </div>
</div>
