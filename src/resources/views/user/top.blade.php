<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <title>{{ config('app.name', 'Laravel') }}</title>

   <!-- Fonts -->
   <link rel="preconnect" href="https://fonts.bunny.net">
   <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

   <!-- Scripts -->
   @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
   <div class="min-h-screen flex justify-center items-center bg-slate-300">
      <div
         class="mx-4 max-w-5xl w-full sm:mx-6 lg:mx-8 p-6 sm:p-8 lg:p-10 flex flex-col lg:flex-row justify-between items-center gap-6 lg:gap-10 rounded-3xl bg-white shadow-lg">
         {{-- ロゴエリア --}}
         <div class="w-40 sm:w-56 lg:w-64 lg:mr-5 flex-shrink-0">
            <x-application-logo />
         </div>
         {{-- 説明文エリア --}}
         <div class="mb-4 w-full text-center lg:text-left">
            {{-- タイトル --}}
            <h1 class="mb-5 text-4xl sm:text-6xl lg:text-8xl text-slate-800 font-bold tracking-tight">FishingMemo</h1>
            {{-- サービスの説明 --}}
            <div class="mb-7 text-slate-600 font-semibold leading-7">
               <p class="text-sm sm:text-base">趣味の釣行記録を、手軽に残せるサービスです。</p>
               <p class="text-sm sm:text-base">ログインして使用してください。</p>
            </div>
            {{-- ログインエリア --}}
            <div
               class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 sm:gap-4 text-center">
               {{-- ユーザーログイン（未ログイン時はミドルウェア側でログインページへ） --}}
               @if (Route::has('user.index'))
                  <a href="{{ route('user.index') }}"
                     class="btn lg:ml-10 w-44 sm:w-48 py-2 px-4 font-semibold rounded-lg bg-sky-900 hover:bg-sky-800 text-white shadow">
                     ユーザーログイン
                  </a>
               @endif
               {{-- 管理者ログイン（未ログイン時はミドルウェア側でログインページへ） --}}
               @if (Route::has('admin.index'))
                  <a href="{{ route('admin.index') }}"
                     class="btn lg:ml-20 w-44 sm:w-48 py-2 px-4 font-semibold rounded-lg bg-rose-900 hover:bg-rose-800 text-white shadow">
                     管理者ログイン
                  </a>
               @endif
            </div>
         </div>
      </div>
   </div>
</body>

</html>
