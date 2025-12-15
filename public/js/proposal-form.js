// Инициализация Quill редактора
let quill;
if (document.getElementById('editor')) {
    quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Сохранение содержимого в textarea перед отправкой
    document.getElementById('proposal-form')?.addEventListener('submit', function() {
        document.getElementById('body_html').value = quill.root.innerHTML;
    });
}

// Управление позициями товаров
let itemIndex = document.querySelectorAll('#items-body tr').length;

document.getElementById('add-item')?.addEventListener('click', function() {
    const tbody = document.getElementById('items-body');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" name="items[${itemIndex}][name]" required></td>
        <td><input type="number" name="items[${itemIndex}][quantity]" step="0.01" value="1" required></td>
        <td><input type="text" name="items[${itemIndex}][unit]" value="шт."></td>
        <td><input type="number" name="items[${itemIndex}][price]" step="0.01" value="0" required></td>
        <td><input type="number" name="items[${itemIndex}][discount]" step="0.1" value="0" min="0" max="100"></td>
        <td class="item-total">0</td>
        <td><button type="button" class="btn-remove" onclick="removeItem(this)">×</button></td>
    `;
    tbody.appendChild(row);
    itemIndex++;
    attachItemListeners(row);
});

function removeItem(btn) {
    btn.closest('tr').remove();
    recalculateTotals();
}

function attachItemListeners(row) {
    const inputs = row.querySelectorAll('input[type="number"]');
    inputs.forEach(input => {
        input.addEventListener('input', recalculateItemTotal);
    });
}

function recalculateItemTotal(e) {
    const row = e.target.closest('tr');
    const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
    const price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
    const discount = parseFloat(row.querySelector('input[name*="[discount]"]').value) || 0;
    
    let total = qty * price;
    total = total - (total * discount / 100);
    
    row.querySelector('.item-total').textContent = total.toFixed(2);
    recalculateTotals();
}

function recalculateTotals() {
    const rows = document.querySelectorAll('#items-body tr');
    let subtotal = 0;
    
    rows.forEach(row => {
        const totalText = row.querySelector('.item-total').textContent;
        subtotal += parseFloat(totalText) || 0;
    });
    
    // Здесь можно добавить отображение итогов, если нужно
}

// Привязка слушателей к существующим строкам
document.querySelectorAll('#items-body tr').forEach(row => {
    attachItemListeners(row);
});



