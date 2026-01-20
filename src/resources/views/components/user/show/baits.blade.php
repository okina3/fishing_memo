<div class="mb-5">
   {{-- エサ --}}
   <h2 class="sub_heading mb-1">エサ</h2>
   <div class="space-y-1">
      <div class="flex flex-wrap items-center gap-x-6 gap-y-1 md:gap-x-10">
         @forelse(($getMemoBaitsName) as $bait_name)
            <div class="p-2 w-60 border border-gray-500 rounded">
               {{ $bait_name }}
            </div>
         @empty
            <div class="text-gray-600">エサは登録されていません。</div>
         @endforelse
      </div>
   </div>
</div>
