<h1>Отчет по концерту: {{ $data['title'] }}</h1>
<p>Дата: {{ $data['date'] }}</p>
<p>Площадка: {{ $data['partner'] }}</p>
<p>Зал: {{ $data['hall'] }}</p>
<hr>
<p>Продано билетов: {{ $data['sold'] }}</p>
<p>Не продано мест: {{ $data['unsold'] }}</p>
<h2>Итоговая выручка: {{ number_format($data['revenue'], 0, ',', ' ') }} руб.</h2>