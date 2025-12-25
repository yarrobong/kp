// Основной JavaScript файл для приложения

// Инициализация dataLayer для Яндекс.Метрики ecommerce (если не инициализирован в layout)
if (typeof window.dataLayer === 'undefined') {
    window.dataLayer = window.dataLayer || [];
}

// Функции для отслеживания ecommerce событий в Яндекс.Метрике
function trackEcommercePurchase(transactionId, total, items) {
    if (typeof ym !== 'undefined' && typeof dataLayer !== 'undefined') {
        dataLayer.push({
            'ecommerce': {
                'purchase': {
                    'actionField': {
                        'id': transactionId,
                        'revenue': total
                    },
                    'products': items
                }
            }
        });
    }
}

function trackEcommerceAddToCart(productId, productName, price, quantity = 1) {
    if (typeof ym !== 'undefined' && typeof dataLayer !== 'undefined') {
        dataLayer.push({
            'ecommerce': {
                'add': {
                    'products': [{
                        'id': productId,
                        'name': productName,
                        'price': price,
                        'quantity': quantity
                    }]
                }
            }
        });
    }
}

// Функция для закрытия flash сообщений
function closeFlashMessage() {
    const flashMessage = document.getElementById('flash-message');
    if (flashMessage) {
        flashMessage.style.display = 'none';
    }
}

// Автоматическое скрытие flash сообщений через 5 секунд
document.addEventListener('DOMContentLoaded', function() {
    const flashMessage = document.getElementById('flash-message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.opacity = '0';
            setTimeout(() => {
                flashMessage.style.display = 'none';
            }, 300);
        }, 5000);
    }
});

// Функция для форматирования цены
function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU').format(price) + ' ₽';
}

// Функция для показа уведомлений
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        ${message}
        <button onclick="closeFlashMessage()">×</button>
    `;

    const container = document.querySelector('.notifications') || document.body;
    container.appendChild(notification);

    // Автоматическое скрытие через 3 секунды
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Обработка клавиш в формах
document.addEventListener('keydown', function(e) {
    // Ctrl+Enter для отправки форм
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const form = e.target.closest('form');
        if (form) {
            form.submit();
        }
    }
});

// Функция для копирования текста в буфер обмена
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Текст скопирован в буфер обмена');
        });
    } else {
        // Fallback для старых браузеров
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Текст скопирован в буфер обмена');
    }
}

// Функция для форматирования цены
function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU').format(price) + ' ₽';
}

// Функция для показа уведомлений
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        ${message}
        <button onclick="this.parentElement.remove()">×</button>
    `;

    document.body.appendChild(notification);

    // Автоматическое скрытие через 3 секунды
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Обработка AJAX форм (если нужно в будущем)
function submitAjaxForm(form, callback) {
    const formData = new FormData(form);

    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (callback) {
            callback(data);
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        showNotification('Произошла ошибка при отправке формы', 'error');
    });
}

// Функция для подтверждения удаления
function confirmDelete(message = 'Вы уверены, что хотите удалить этот элемент?') {
    return confirm(message);
}

// Обработка клавиш в формах
document.addEventListener('keydown', function(e) {
    // Ctrl+Enter для отправки форм
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const form = e.target.closest('form');
        if (form) {
            form.submit();
        }
    }
});

// Плавная прокрутка к элементам с ошибками
document.addEventListener('DOMContentLoaded', function() {
    const errorElements = document.querySelectorAll('.error, .has-error');
    if (errorElements.length > 0) {
        errorElements[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Функция для копирования текста в буфер обмена
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Текст скопирован в буфер обмена');
        });
    } else {
        // Fallback для старых браузеров
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Текст скопирован в буфер обмена');
    }
}
