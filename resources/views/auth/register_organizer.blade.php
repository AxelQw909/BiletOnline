@extends('layouts.app')
@section('title', 'Регистрация организатора — BiletOnline')

@section('content')
<div class="max-w-md mx-auto my-6 bg-white border border-zinc-200 p-8">
    <h2 class="text-xl font-bold tracking-tight mb-6 uppercase">Регистрация: Организатор</h2>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-600 text-xs border border-red-200">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.organizer') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs uppercase tracking-wider text-zinc-500 font-semibold mb-1">Название организации</label>
            <input type="text" name="company_name" value="{{ old('company_name') }}" required class="w-full border border-zinc-300 p-2 text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-zinc-500 font-semibold mb-1">Номер телефона</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full border border-zinc-300 p-2 text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-zinc-500 font-semibold mb-1">Почта</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-zinc-300 p-2 text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-zinc-500 font-semibold mb-1">Пароль</label>
            <input type="password" name="password" required class="w-full border border-zinc-300 p-2 text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-zinc-500 font-semibold mb-1">Подтвердите пароль</label>
            <input type="password" name="password_confirmation" required class="w-full border border-zinc-300 p-2 text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50">
        </div>
        <button type="submit" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white text-sm py-2.5 uppercase font-medium tracking-wider cursor-pointer transition">Зарегистрироваться</button>
    </form>
</div>
@endsection