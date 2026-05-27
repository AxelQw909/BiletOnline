@extends('layouts.app')
@section('title', 'Вход — BiletOnline')

@section('content')
<div class="max-w-md mx-auto my-12 bg-white border border-zinc-200 p-8">
    <h2 class="text-xl font-bold tracking-tight mb-6 uppercase">Вход в систему</h2>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-600 text-xs border border-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ url('/login') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs uppercase tracking-wider text-zinc-500 font-semibold mb-1">Почта (Email)</label>
            <input type="email" name="email" required class="w-full border border-zinc-300 p-2 text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-zinc-500 font-semibold mb-1">Пароль</label>
            <input type="password" name="password" required class="w-full border border-zinc-300 p-2 text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50">
        </div>
        <button type="submit" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white text-sm py-2.5 uppercase font-medium tracking-wider cursor-pointer transition">Войти</button>
    </form>

    <div class="mt-8 pt-6 border-t border-zinc-200 flex flex-col gap-2 text-xs text-zinc-600">
        <a href="{{ route('register.partner') }}" class="hover:text-zinc-900 underline">Регистрация для Партнера</a>
        <a href="{{ route('register.organizer') }}" class="hover:text-zinc-900 underline">Регистрация для Организатора</a>
    </div>
</div>
@endsection