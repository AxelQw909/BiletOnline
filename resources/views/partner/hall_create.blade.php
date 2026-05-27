@extends('layouts.app')
@section('title', isset($hall) ? 'Редактировать зал' : 'Создать новый зал')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    {{-- Заголовок --}}
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">
            {{ isset($hall) ? 'Редактирование: ' . $hall->name : 'Конструктор нового зала' }}
        </h2>
    </header>

    {{-- Безопасная передача данных в JS --}}
    @if(isset($hall))
        <div id="schemaData" data-schema='@json($hall->schema)' class="hidden"></div>
    @endif

    <form action="{{ isset($hall) ? route('partner.hall.update', $hall->id) : route('partner.hall.store') }}" method="POST" id="hallForm" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @csrf
        
        {{-- Боковая панель --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm space-y-5">
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-2">Название зала</label>
                    <input type="text" name="name" id="hallName" value="{{ isset($hall) ? $hall->name : '' }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-2">Адрес зала</label>
                    <input type="text" name="address" id="hallAddress" value="{{ isset($hall) ? $hall->address : '' }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-2">Количество мест</label>
                    <input type="number" name="capacity" id="hallCapacity" value="{{ isset($hall) ? $hall->capacity : 0 }}" readonly class="w-full border border-zinc-100 p-3 rounded-xl text-sm bg-zinc-50 font-black text-indigo-600 focus:outline-none">
                </div>

                <div class="pt-6 border-t border-zinc-100 space-y-4">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Генератор сетки</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase text-zinc-400 mb-1">Рядов</label>
                            <input type="number" id="inputRows" value="10" min="1" max="40" class="w-full border border-zinc-200 p-2 rounded-lg text-sm focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase text-zinc-400 mb-1">Мест в ряду</label>
                            <input type="number" id="inputSeats" value="12" min="1" max="40" class="w-full border border-zinc-200 p-2 rounded-lg text-sm focus:outline-none">
                        </div>
                    </div>
                    <button type="button" onclick="generateGrid()" class="w-full bg-zinc-100 hover:bg-zinc-200 text-zinc-900 text-xs py-3 uppercase font-bold rounded-xl transition cursor-pointer">Пересоздать сетку</button>
                </div>

                <input type="hidden" name="schema" id="schemaInput">
                <button type="submit" class="w-full bg-zinc-900 hover:bg-indigo-600 text-white text-sm py-4 rounded-xl uppercase font-bold tracking-wider transition-all active:scale-95 cursor-pointer mt-4">Сохранить зал</button>
            </div>
        </div>

        {{-- Основная область редактора --}}
        <div class="lg:col-span-3">
            <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm min-h-[500px]">
                <label class="block text-xs uppercase font-bold text-zinc-400 mb-6">Интерактивная схема зала</label>
                
                <div class="w-full flex justify-center overflow-x-auto p-4 bg-zinc-50 rounded-2xl border border-dashed border-zinc-200">
                    <div id="editorContainer" class="inline-grid gap-2"></div>
                </div>
                
                <p class="text-[10px] text-zinc-400 mt-6 uppercase tracking-widest text-center">Нажмите на ячейку, чтобы переключить тип (место/проход)</p>
            </div>
        </div>
    </form>
</div>

<script>
    let hallSchema = [];
    
    document.addEventListener("DOMContentLoaded", function() {
        const schemaElement = document.getElementById('schemaData');
        if (schemaElement && schemaElement.dataset.schema) {
            hallSchema = JSON.parse(schemaElement.dataset.schema);
            renderGridFromSchema();
        } else {
            generateGrid();
        }
    });

    function generateGrid() {
        const rows = parseInt(document.getElementById('inputRows').value) || 10;
        const seats = parseInt(document.getElementById('inputSeats').value) || 12;
        hallSchema = [];
        for (let r = 1; r <= rows; r++) {
            for (let s = 1; s <= seats; s++) {
                // Изначально сохраняем логический индекс места
                hallSchema.push({ row: r, seat: s, type: 'seat' });
            }
        }
        renderGridFromSchema();
    }

    function renderGridFromSchema() {
        const container = document.getElementById('editorContainer');
        container.innerHTML = '';
        if(hallSchema.length === 0) return;

        // Определяем количество столбцов для сетки
        const maxSeat = Math.max(...hallSchema.filter(i => i.row === 1).map(item => item.seat));
        container.style.gridTemplateColumns = `repeat(${maxSeat}, minmax(48px, 1fr))`;

        let currentRow = -1;
        let seatCounter = 0;

        hallSchema.forEach((item) => {
            // Если начался новый ряд, сбрасываем счетчик мест
            if (item.row !== currentRow) {
                currentRow = item.row;
                seatCounter = 0;
            }

            const cell = document.createElement('div');
            
            if (item.type === 'seat') {
                seatCounter++;
                // Визуально отображаем текущий порядковый номер в ряду
                cell.innerText = seatCounter;
                cell.className = "h-12 w-12 text-[10px] font-bold flex items-center justify-center cursor-pointer select-none transition-all rounded-lg bg-zinc-900 text-white shadow-sm hover:bg-indigo-600";
            } else {
                cell.innerText = "";
                cell.className = "h-12 w-12 flex items-center justify-center cursor-pointer select-none transition-all rounded-lg bg-white border border-zinc-200 hover:border-zinc-300";
            }
            
            cell.addEventListener('click', () => {
                item.type = (item.type === 'seat') ? 'aisle' : 'seat';
                renderGridFromSchema();
            });
            container.appendChild(cell);
        });

        calculateCapacity();
        saveSchemaToInput();
    }

    function calculateCapacity() {
        document.getElementById('hallCapacity').value = hallSchema.filter(item => item.type === 'seat').length;
    }

    function saveSchemaToInput() {
        document.getElementById('schemaInput').value = JSON.stringify(hallSchema);
    }

    document.getElementById('hallForm').addEventListener('submit', saveSchemaToInput);
</script>
@endsection