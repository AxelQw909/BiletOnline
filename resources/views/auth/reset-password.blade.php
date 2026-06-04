@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto my-12 bg-white border border-zinc-200 p-8">
    <h2 class="text-xl font-bold mb-6 uppercase">Смена пароля</h2>
    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="email" name="email" placeholder="Ваша почта" class="w-full border border-zinc-300 p-2 mb-4" required>
        <input type="password" name="password" placeholder="Новый пароль" class="w-full border border-zinc-300 p-2 mb-4" required>
        <input type="password" name="password_confirmation" placeholder="Подтвердите пароль" class="w-full border border-zinc-300 p-2 mb-4" required>
        <button class="w-full bg-zinc-900 text-white py-2 uppercase font-bold text-sm">Сменить пароль</button>
    </form>
</div>
@endsection