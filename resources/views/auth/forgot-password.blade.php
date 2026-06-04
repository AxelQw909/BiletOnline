@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto my-12 bg-white border border-zinc-200 p-8">
    <h2 class="text-xl font-bold mb-6 uppercase">Восстановление</h2>
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <label class="block text-xs uppercase text-zinc-500 mb-1">Введите вашу почту</label>
        <input type="email" name="email" required class="w-full border border-zinc-300 p-2 mb-4">
        <button class="w-full bg-zinc-900 text-white py-2 uppercase font-bold text-sm">Отправить ссылку</button>
    </form>
</div>
@endsection