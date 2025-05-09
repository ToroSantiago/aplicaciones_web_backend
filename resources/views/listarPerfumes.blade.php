<!DOCTYPE html>
<html>
<head>
    <title>Listado de Perfumes</title>
</head>
<body>
    <h1>Listado de Perfumes</h1>

    <a href="{{ route('perfumes.create') }}">Crear nuevo perfume</a>

    <ul>
        @foreach ($perfumes as $perfume)
            <li>
                {{ $perfume->nombre }} - {{ $perfume->marca }} - ${{ number_format($perfume->precio / 100, 2) }}
                - Género: {{ $perfume->genero }}
                - Stock: {{ $perfume->stock ? 'Sí' : 'No' }}

                <a href="{{ route('perfumes.edit', $perfume->id) }}">Editar</a>

                <form action="{{ route('perfumes.destroy', $perfume->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
