<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
</head>
<body>
    <h2>Registro de Usuario</h2>

    @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label>Nombre:</label><br>
        <input type="text" name="name" value="{{ old('name') }}"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="{{ old('email') }}"><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password"><br><br>

        <label>Confirmar Contraseña:</label><br>
        <input type="password" name="password_confirmation"><br><br>

        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tenés cuenta? <a href="{{ route('login') }}">Iniciá sesión</a></p>
</body>
</html>
