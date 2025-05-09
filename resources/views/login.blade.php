<body>
    <h2>Iniciar Sesión</h2>
    
    @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <label>Email:</label><br>
        <input type="email" name="email" value="{{ old('email') }}"><br><br>
        
        <label>Contraseña:</label><br>
        <input type="password" name="password"><br><br>
        
        <button type="submit">Ingresar</button>
    </form>
    
    <p>¿No tenés cuenta? <a href="{{ route('register') }}">Registrate acá</a></p>
</body>
</html>