@extends('layouts.app')

@section('title', 'Каталог товаров')

@section('content')
<div class="page-header">
    <h1>Каталог товаров</h1>
    <div style="display: flex; gap: 12px;">
        <input type="text" placeholder="Поиск товаров..." style="padding: 12px 16px; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px);">
        <select style="padding: 12px 16px; border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 8px; background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px);">
            <option>Все категории</option>
            <option>Электроника</option>
            <option>Оборудование</option>
        </select>
    </div>
</div>

<div class="products-grid">
    @forelse($products ?? [] as $product)
        <div class="product-card">
            <div class="product-image-container">
                <img src="{{ $product->getPhotoUrl() }}" alt="{{ $product->name }}" class="product-image">
            </div>
            <div class="product-info">
                <div class="product-title">{{ $product->name }}</div>
                <div class="product-price">₽ {{ number_format($product->price, 2, ',', ' ') }}</div>
                @if($product->description)
                    <div class="product-description">{{ Str::limit($product->description, 100) }}</div>
                @endif
            </div>
            <div class="product-actions">
                <a href="/products/{{ $product->id }}" class="btn btn-sm">👁 Просмотр</a>
                <a href="/products/{{ $product->id }}/edit" class="btn btn-sm btn-secondary">✏️ Редактировать</a>
                <form method="POST" action="/products/{{ $product->id }}" style="display: inline;">
                    <input type="hidden" name="_token" value="{{ session('_token') }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить товар?')">🗑 Удалить</button>
                </form>
            </div>
        </div>
    @empty
        <div class="product-card" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
            <div class="product-title">Каталог пуст</div>
            <div class="product-description">Добавьте первый товар в ваш каталог</div>
            <div style="margin-top: 20px;">
                <a href="/products/create" class="btn btn-primary">➕ Добавить товар</a>
            </div>
        </div>
    @endforelse
</div>

<!-- FAB Button -->
<a href="/products/create" class="fab" title="Добавить товар">
    ➕
</a>

<!-- Modal for product creation/editing -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle" class="modal-title">Добавить товар</h2>
            <button class="modal-close">&times;</button>
        </div>
        <form id="productForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ session('_token') }}">

            <div class="form-group">
                <label>Название товара</label>
                <input type="text" name="name" required placeholder="Например: Ноутбук Lenovo ThinkPad">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Цена (₽)</label>
                    <input type="number" name="price" step="0.01" required placeholder="10000.00">
                </div>
                <div class="form-group">
                    <label>Категория</label>
                    <select name="category">
                        <option>Электроника</option>
                        <option>Оборудование</option>
                        <option>Программное обеспечение</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Фото товара</label>
                <input type="file" name="photo" accept="image/*">
                <div class="hint">Поддерживаются форматы: JPG, PNG, GIF (макс. 5MB)</div>
            </div>

            <div class="form-group">
                <label>Описание</label>
                <textarea name="description" rows="4" placeholder="Подробное описание товара, характеристики, преимущества..."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Сохранить товар</button>
                <button type="button" class="btn btn-secondary modal-close">❌ Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('productModal');
    const closeBtn = document.querySelector('.modal-close');

    // Open modal when FAB is clicked
    document.querySelector('.fab').addEventListener('click', function(e) {
        e.preventDefault();
        modal.classList.add('show');
    });

    // Close modal
    closeBtn.addEventListener('click', function() {
        modal.classList.remove('show');
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });

    // Toast notifications
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-title">${type === 'success' ? 'Успех' : 'Ошибка'}</div>
            <div class="toast-message">${message}</div>
        `;

        const container = document.querySelector('.toast-container') || createToastContainer();
        container.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    // Form submission
    document.getElementById('productForm').addEventListener('submit', function(e) {
        // Here you would handle the form submission
        // For now, just show a success message
        showToast('Товар успешно добавлен!');
        modal.classList.remove('show');
        e.preventDefault();
    });
});
</script>
@endsection
