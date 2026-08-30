document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // VALIDACIÓN FORMULARIO DE REGISTRO
    // ==========================================
    const registroForm = document.getElementById('registroForm');
    if (registroForm) {
        registroForm.addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid', 'is-valid'));

            const nombre = document.getElementById('nombre');
            const nombreError = document.getElementById('nombreError');
            if (nombre) {
                const val = nombre.value.trim();
                if (val === '') { mostrarError(nombre, nombreError, 'El nombre es obligatorio.'); isValid = false; }
                else if (val.length < 3) { mostrarError(nombre, nombreError, 'Mínimo 3 caracteres.'); isValid = false; }
                else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(val)) { mostrarError(nombre, nombreError, 'Solo letras y espacios.'); isValid = false; }
                else { nombre.classList.add('is-valid'); }
            }

            const email = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            if (email) {
                const val = email.value.trim();
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (val === '') { mostrarError(email, emailError, 'El email es obligatorio.'); isValid = false; }
                else if (!regex.test(val)) { mostrarError(email, emailError, 'Email no válido.'); isValid = false; }
                else { email.classList.add('is-valid'); }
            }

            const password = document.getElementById('password');
            const passwordError = document.getElementById('passwordError');
            if (password) {
                const val = password.value;
                if (val === '') { mostrarError(password, passwordError, 'La contraseña es obligatoria.'); isValid = false; }
                else if (val.length < 6) { mostrarError(password, passwordError, 'Mínimo 6 caracteres.'); isValid = false; }
                else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(val)) { mostrarError(password, passwordError, 'Debe tener mayúscula, minúscula y número.'); isValid = false; }
                else { password.classList.add('is-valid'); }
            }

            const edad = document.getElementById('edad');
            const edadError = document.getElementById('edadError');
            if (edad) {
                const val = parseInt(edad.value);
                if (edad.value === '' || isNaN(val)) { mostrarError(edad, edadError, 'La edad es obligatoria.'); isValid = false; }
                else if (val < 18) { mostrarError(edad, edadError, 'Debe ser mayor de edad (18+).'); isValid = false; }
                else if (val > 120) { mostrarError(edad, edadError, 'Edad no válida.'); isValid = false; }
                else { edad.classList.add('is-valid'); }
            }

            const telefono = document.getElementById('telefono');
            const telefonoError = document.getElementById('telefonoError');
            if (telefono) {
                const val = telefono.value.trim();
                const regex = /^[0-9\+\-\s\(\)]{7,20}$/;
                if (val === '') { mostrarError(telefono, telefonoError, 'El teléfono es obligatorio.'); isValid = false; }
                else if (!regex.test(val)) { mostrarError(telefono, telefonoError, 'Teléfono no válido.'); isValid = false; }
                else { telefono.classList.add('is-valid'); }
            }

            if (!isValid) e.preventDefault();
        });

        registroForm.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', function() {
                const submitEvent = new Event('submit', { cancelable: true });
                registroForm.dispatchEvent(submitEvent);
            });
        });
    }

    // ==========================================
    // VALIDACIÓN LOGIN
    // ==========================================
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid', 'is-valid'));

            const email = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            if (email) {
                const val = email.value.trim();
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (val === '') { mostrarError(email, emailError, 'El email es obligatorio.'); isValid = false; }
                else if (!regex.test(val)) { mostrarError(email, emailError, 'Email no válido.'); isValid = false; }
                else { email.classList.add('is-valid'); }
            }

            const password = document.getElementById('password');
            const passwordError = document.getElementById('passwordError');
            if (password) {
                if (password.value === '') { mostrarError(password, passwordError, 'La contraseña es obligatoria.'); isValid = false; }
                else if (password.value.length < 6) { mostrarError(password, passwordError, 'Mínimo 6 caracteres.'); isValid = false; }
                else { password.classList.add('is-valid'); }
            }

            if (!isValid) e.preventDefault();
        });
    }

    // ==========================================
    // VALIDACIÓN PRODUCTO
    // ==========================================
    const productoForm = document.getElementById('productoForm');
    if (productoForm) {
        productoForm.addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid', 'is-valid'));

            const nombreProducto = document.getElementById('nombreProducto');
            const nombreProductoError = document.getElementById('nombreProductoError');
            if (nombreProducto) {
                const val = nombreProducto.value.trim();
                if (val === '') { mostrarError(nombreProducto, nombreProductoError, 'El nombre es obligatorio.'); isValid = false; }
                else if (val.length < 2) { mostrarError(nombreProducto, nombreProductoError, 'Mínimo 2 caracteres.'); isValid = false; }
                else { nombreProducto.classList.add('is-valid'); }
            }

            const precio = document.getElementById('precio');
            const precioError = document.getElementById('precioError');
            if (precio) {
                const val = parseFloat(precio.value);
                if (precio.value === '' || isNaN(val)) { mostrarError(precio, precioError, 'El precio es obligatorio.'); isValid = false; }
                else if (val <= 0) { mostrarError(precio, precioError, 'El precio debe ser mayor a 0.'); isValid = false; }
                else { precio.classList.add('is-valid'); }
            }

            const descripcion = document.getElementById('descripcion');
            const descripcionError = document.getElementById('descripcionError');
            if (descripcion) {
                const val = descripcion.value.trim();
                if (val === '') { mostrarError(descripcion, descripcionError, 'La descripción es obligatoria.'); isValid = false; }
                else if (val.length < 10) { mostrarError(descripcion, descripcionError, 'Mínimo 10 caracteres.'); isValid = false; }
                else { descripcion.classList.add('is-valid'); }
            }

            if (!isValid) e.preventDefault();
        });
    }

    function mostrarError(input, errorElement, mensaje) {
        if (input) input.classList.add('is-invalid');
        if (errorElement) {
            errorElement.textContent = mensaje;
            errorElement.style.display = 'block';
        }
    }
});
