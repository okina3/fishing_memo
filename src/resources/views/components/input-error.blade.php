@props(['messages'])

@php
   // $messagesの型を判定し、最終的に一次元の文字列配列にしてから出力。
   if (is_object($messages) && method_exists($messages, 'all')) {
       $raw = $messages->all();
   } else {
       $raw = (array) $messages;
   }

   $messagesList = \Illuminate\Support\Arr::flatten($raw);
@endphp

@if (!empty($messagesList))
   <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
      @foreach ($messagesList as $message)
         <li>{{ $message }}</li>
      @endforeach
   </ul>
@endif
