<!DOCTYPE html>
<html>
<head>
    <title>Editar Perfume</title>
</head>
<body>
    <h1>Editar perfume</h1>

    <form method="POST" action="{{ route('perfumes.update', $perfume->id) }}">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input name="nombre" value="{{ $perfume->nombre }}" required><br>

        <label>Marca:</label>
        <input name="marca" value="{{ $perfume->marca }}" required><br>

        <label>Descripción:</label>
        <textarea name="descripcion">{{ $perfume->descripcion }}</textarea><br>

        <label>Volumen (ml):</label>
        <input name="volumen" type="number" value="{{ $perfume->volumen }}" required><br>

        <label>Precio:</label>
        <input name="precio" type="number" value="{{ $perfume->precio }}" required><br>

        <label>Género:</label>
        <select name="genero" required>
            <option value="M" {{ $perfume->genero == 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ $perfume->genero == 'F' ? 'selected' : '' }}>Femenino</option>
            <option value="U" {{ $perfume->genero == 'U' ? 'selected' : '' }}>Unisex</option>
        </select><br>

        <label>
            <input type="hidden" name="stock" value="0">
            <input type="checkbox" name="stock" value="1" {{ $perfume->stock ? 'checked' : '' }}>
            ¿Hay stock?
        </label><br>

        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
