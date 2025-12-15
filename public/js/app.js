// Генерация CSRF токена
if (!document.querySelector('meta[name="csrf-token"]')) {
    const meta = document.createElement('meta');
    meta.name = 'csrf-token';
    meta.content = generateToken();
    document.head.appendChild(meta);
}

function generateToken() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

// Установка токена в сессию при загрузке
if (typeof session !== 'undefined') {
    session('_token', generateToken());
}



