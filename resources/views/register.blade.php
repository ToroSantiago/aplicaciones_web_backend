<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <style>
        :root {
            --dark-gray: #282729;
            --medium-dark-gray: #4D4C4F;
            --medium-gray: #767578;
            --light-gray: #A2A1A3;
            --very-light-gray: #D0CFD1;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-color: #282729;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 15px;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: -1;
            pointer-events: none;
        }
        
        .register-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 2rem;
            animation: slideUp 0.3s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-section h1 {
            color: var(--dark-gray);
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.5px;
        }
        
        .logo-section p {
            color: var(--medium-gray);
            font-size: 0.95rem;
            margin: 0;
        }
        
        .error-container {
            background-color: #fee;
            color: #dc3545;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #dc3545;
            font-size: 0.9rem;
        }
        
        .error-container p {
            margin: 4px 0;
        }
        
        .success-container {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #28a745;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--medium-dark-gray);
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--very-light-gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.9);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--medium-gray);
            box-shadow: 0 0 0 3px rgba(118, 117, 120, 0.1);
            background-color: white;
            transform: translateY(-1px);
        }
        
        .form-group input:hover:not(:focus) {
            border-color: var(--light-gray);
        }
        
        .form-group input.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        
        .register-button {
            background: linear-gradient(135deg, var(--medium-dark-gray) 0%, var(--dark-gray) 100%);
            border: none;
            color: white;
            padding: 14px 20px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .register-button:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--dark-gray) 0%, #1a1a1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .register-button:active {
            transform: translateY(0);
        }
        
        .register-button:disabled {
            background: var(--light-gray);
            cursor: not-allowed;
            transform: none;
            opacity: 0.6;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .login-link p {
            margin: 0;
            color: var(--medium-gray);
            font-size: 0.95rem;
        }
        
        .login-link a {
            color: var(--medium-dark-gray);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--dark-gray);
            text-decoration: underline;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin-top: 1rem;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid var(--very-light-gray);
            border-top: 2px solid var(--medium-dark-gray);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* RESPONSIVE DESIGN */
        @media (max-width: 768px) {
            body {
                padding: 10px;
                background-attachment: scroll;
            }
            
            .register-container {
                padding: 1.5rem;
                border-radius: 8px;
                max-width: 100%;
            }
            
            .logo-section h1 {
                font-size: 1.6rem;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 5px;
                align-items: flex-start;
                padding-top: 20px;
            }
            
            .register-container {
                padding: 1.25rem;
                border-radius: 6px;
                margin-top: 0;
            }
            
            .logo-section {
                margin-bottom: 1.5rem;
            }
            
            .logo-section h1 {
                font-size: 1.4rem;
            }
            
            .logo-section p {
                font-size: 0.9rem;
            }
            
            .form-group {
                margin-bottom: 1.25rem;
            }
            
            .form-group input {
                padding: 12px 14px;
                font-size: 16px;
            }
            
            .register-button {
                padding: 16px 20px;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 320px) {
            .register-container {
                padding: 1rem;
            }
            
            .logo-section h1 {
                font-size: 1.3rem;
            }
            
            .form-group input {
                padding: 10px 12px;
            }
        }
        
        @media (min-width: 1200px) {
            .register-container {
                max-width: 480px;
                padding: 2.5rem;
            }
            
            .logo-section h1 {
                font-size: 2rem;
            }
        }
        
        @media (max-height: 600px) and (orientation: landscape) {
            body {
                align-items: flex-start;
                padding-top: 10px;
            }
            
            .register-container {
                margin-top: 0;
            }
            
            .logo-section {
                margin-bottom: 1rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-section">
            <h1>Crear Cuenta</h1>
            <p>Completa los datos para registrarte</p>
        </div>
        
        @if ($errors->any())
            <div class="error-container">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        @if (session('status'))
            <div class="success-container">
                <p>{{ session('status') }}</p>
            </div>
        @endif
        
        <form method="POST" action="/register" id="registerForm">
            @csrf

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" 
                    id="nombre" 
                    name="nombre" 
                    value="{{ old('nombre') }}" 
                    required 
                    autocomplete="given-name" 
                    class="@error('nombre') is-invalid @enderror">
                @error('nombre')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" 
                    id="apellido" 
                    name="apellido" 
                    value="{{ old('apellido') }}" 
                    required 
                    autocomplete="family-name" 
                    class="@error('apellido') is-invalid @enderror">
                @error('apellido')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="email"
                    class="@error('email') is-invalid @enderror">
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="genero">Género</label>
                <select id="genero" name="genero" required class="@error('genero') is-invalid @enderror">
                    <option value="">Seleccioná una opción</option>
                    <option value="M" {{ old('genero', $genero ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero', $genero ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="O" {{ old('genero', $genero ?? '') == 'O' ? 'selected' : '' }}>Otro</option>
                </select>
                @error('genero')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    class="@error('password') is-invalid @enderror">
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <div class="password-strength" id="passwordStrength"></div>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    class="@error('password_confirmation') is-invalid @enderror">
                @error('password_confirmation')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="register-button" id="registerBtn">
                Crear Cuenta
            </button>
            
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <span>Creando cuenta...</span>
            </div>
        </form>
        
        <div class="login-link">
            <p>¿Ya tenés cuenta? <a href="/login">Iniciá sesión</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const button = document.getElementById('registerBtn');
            const loading = document.getElementById('loading');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const strengthIndicator = document.getElementById('passwordStrength');
            
            // Mostrar loading al enviar formulario
            form.addEventListener('submit', function() {
                button.disabled = true;
                button.style.display = 'none';
                loading.style.display = 'block';
            });
            
            // Validador de fortaleza de contraseña
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = checkPasswordStrength(password);
                
                strengthIndicator.textContent = strength.text;
                strengthIndicator.className = 'password-strength ' + strength.class;
                
                // Validar confirmación de contraseña
                if (confirmPasswordInput.value && confirmPasswordInput.value !== password) {
                    confirmPasswordInput.classList.add('is-invalid');
                } else {
                    confirmPasswordInput.classList.remove('is-invalid');
                }
            });
            
            // Validar confirmación de contraseña
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
            
            // Auto-focus en primer campo con error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.focus();
            }
            
            // Limpiar errores al escribir
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                });
            });
            
            function checkPasswordStrength(password) {
                if (password.length < 6) {
                    return { text: 'Muy débil (mínimo 6 caracteres)', class: 'strength-weak' };
                }
                
                let score = 0;
                if (password.length >= 8) score++;
                if (/[a-z]/.test(password)) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[^A-Za-z0-9]/.test(password)) score++;
                
                if (score < 3) {
                    return { text: 'Débil', class: 'strength-weak' };
                } else if (score < 4) {
                    return { text: 'Media', class: 'strength-medium' };
                } else {
                    return { text: 'Fuerte', class: 'strength-strong' };
                }
            }
        });
    </script>
</body>
</html>