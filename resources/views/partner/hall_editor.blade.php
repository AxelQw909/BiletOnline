@extends('layouts.app')
@section('title', isset($hall) ? 'Редактировать зал' : 'Создать новый зал')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">
            {{ isset($hall) ? 'Редактирование: ' . $hall->name : 'Конструктор нового зала' }}
        </h2>
    </header>

    <script>
        // Передаем структуру рядов и мест из БД в JavaScript
        const initialData = @json(isset($hall) ? $hall->rows : []);
    </script>

    <form action="{{ isset($hall) ? route('partner.hall.update', $hall->id) : route('partner.hall.store') }}" method="POST" id="hallForm" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @csrf
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm space-y-5">
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-2">Название зала</label>
                    <input type="text" name="name" value="{{ $hall->name ?? '' }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-2">Адрес зала</label>
                    <input type="text" name="address" value="{{ $hall->address ?? '' }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-2">Количество мест</label>
                    <input type="number" name="capacity" id="hallCapacity" value="{{ $hall->capacity ?? 0 }}" readonly class="w-full border border-zinc-100 p-3 rounded-xl text-sm bg-zinc-50 font-black text-indigo-600 focus:outline-none">
                </div>

                <input type="hidden" name="schema" id="schemaInput">
                
                <button type="submit" class="w-full bg-zinc-900 hover:bg-indigo-600 text-white text-sm py-4 rounded-xl uppercase font-bold tracking-wider transition-all active:scale-95 cursor-pointer mt-4">
                    Сохранить зал
                </button>
            </div>
        </div>

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
        if (initialData.length > 0) {
            initialData.forEach(r => {
                r.seats.forEach(s => {
                    // Собираем плоский массив для логики рендера
                    hallSchema.push({ row: r.number, seat: s.number, type: s.type });
                });
            });
            renderGrid();
        }
    });

    function renderGrid() {
        const container = document.getElementById('editorContainer');
        container.innerHTML = '';
        
        const rowNums = [...new Set(hallSchema.map(i => i.row))].sort((a,b) => a - b);
        const maxCols = Math.max(...hallSchema.map(i => i.seat));
        
        container.style.gridTemplateColumns = `48px repeat(${maxCols}, minmax(56px, 1fr))`;

        rowNums.forEach(rNum => {
            const rowLabel = document.createElement('div');
            rowLabel.className = "flex items-center justify-center font-bold text-zinc-400 w-12 text-[10px] uppercase tracking-wider";
            rowLabel.innerText = "Ряд " + rNum;
            container.appendChild(rowLabel);

            hallSchema.filter(i => i.row === rNum).forEach(item => {
                const cell = document.createElement('div');
                
                cell.className = "h-14 w-14 rounded-xl flex items-center justify-center cursor-pointer transition-all border-2 " + 
                                (item.type === 'seat' 
                                 ? "bg-zinc-900 border-zinc-900 text-white shadow-sm hover:bg-indigo-600 hover:border-indigo-600" 
                                 : "bg-white border-zinc-200 text-zinc-300");
                
                cell.innerHTML = item.type === 'seat' ? `<span class="font-bold text-xs">${item.seat}</span>` : '';

                cell.addEventListener('click', () => {
                    item.type = (item.type === 'seat') ? 'aisle' : 'seat';
                    renderGrid();
                });
                container.appendChild(cell);
            });
        });
        
        document.getElementById('hallCapacity').value = hallSchema.filter(i => i.type === 'seat').length;
        document.getElementById('schemaInput').value = JSON.stringify(hallSchema);
    }

    document.getElementById('hallForm').addEventListener('submit', function() {
        document.getElementById('schemaInput').value = JSON.stringify(hallSchema);
    });
</script>
@endsection