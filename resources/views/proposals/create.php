@extends('layouts.app')

@section('title', 'Создать коммерческое предложение')

@push('styles')
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
@endpush

@section('content')
<h1>Создать коммерческое предложение</h1>

<form method="POST" action="/proposals" id="proposal-form">
    <input type="hidden" name="_token" value="{{ session('_token') }}">
    
    <div class="form-group">
        <label>Название КП *</label>
        <input type="text" name="title" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Номер предложения</label>
            <input type="text" name="offer_number">
        </div>
        <div class="form-group">
            <label>Дата *</label>
            <input type="date" name="offer_date" value="{{ date('Y-m-d') }}" required>
        </div>
    </div>

    <div class="form-group">
        <label>Данные продавца</label>
        <textarea name="seller_info" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label>Данные покупателя</label>
        <textarea name="buyer_info" rows="3"></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Валюта</label>
            <input type="text" name="currency" value="₽" maxlength="10">
        </div>
        <div class="form-group">
            <label>НДС, %</label>
            <input type="number" name="vat_rate" step="0.1" value="0" min="0" max="100">
        </div>
    </div>

    <div class="form-group">
        <label>Условия оплаты/поставки</label>
        <input type="text" name="terms">
    </div>

    <div class="form-group">
        <label>Содержание КП *</label>
        <div id="editor" style="height: 300px;"></div>
        <textarea name="body_html" id="body_html" style="display: none;" required></textarea>
    </div>

    <div class="form-group">
        <h3>Товары/услуги</h3>
        <button type="button" class="btn btn-secondary" id="add-item">Добавить позицию</button>
        <table class="items-table" id="items-table">
            <thead>
                <tr>
                    <th>Наименование</th>
                    <th>Кол-во</th>
                    <th>Ед.</th>
                    <th>Цена</th>
                    <th>Скидка %</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="items-body">
                <tr>
                    <td><input type="text" name="items[0][name]" required></td>
                    <td><input type="number" name="items[0][quantity]" step="0.01" value="1" required></td>
                    <td><input type="text" name="items[0][unit]" value="шт."></td>
                    <td><input type="number" name="items[0][price]" step="0.01" value="0" required></td>
                    <td><input type="number" name="items[0][discount]" step="0.1" value="0" min="0" max="100"></td>
                    <td class="item-total">0</td>
                    <td><button type="button" class="btn-remove" onclick="removeItem(this)">×</button></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <label>Статус</label>
        <select name="status">
            <option value="draft">Черновик</option>
            <option value="published">Опубликовать</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/proposals" class="btn btn-secondary">Отмена</a>
    </div>
</form>

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="{{ asset('js/proposal-form.js') }}"></script>
@endpush
@endsection



