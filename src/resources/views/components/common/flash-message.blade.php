{{-- 初期設定 --}}
@props(['status' => 'success'])

{{-- フラッシュメッセージの背景色を切り分ける --}}
@php
   if (session('status') === 'success') {
       $bgColor = 'bg-green-500';
   }
   if (session('status') === 'error') {
       $bgColor = 'bg-red-500';
} @endphp

{{-- メッセージを表示する --}}
@if (session('message'))
   <div class="{{ $bgColor }} p-2 mb-3 text-white rounded">
      {{ session('message') }}
   </div>
@endif
