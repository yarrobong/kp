@extends('layouts.app')

@section('title', 'Редактировать КП')

@push('styles')
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
@endpush

@section('content')
<h1>Редактировать коммерческое предложение</h1>

<form method="POST" action="/proposals/{{ $proposal->id }}" id="proposal-form">
    <input type="hidden" name="_token" value="{{ session('_token') }}">
    <input type="hidden" name="_method" value="PUT">
    
    <div class="form-group">
        <label>Название КП *</label>
        <input type="text" name="title" value="{{ $proposal->title }}" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Номер предложения</label>
            <input type="text" name="offer_number" value="{{ $proposal->offer_number }}">
        </div>
        <div class="form-group">
            <label>Дата *</label>
            <input type="date" name="offer_date" value="{{ $proposal->offer_date->format('Y-m-d') }}" required>
        </div>
    </div>

    <div class="form-group">
        <label>Данные продавца</label>
        <textarea name="seller_info" rows="3">{{ $proposal->seller_info }}</textarea>
    </div>

    <div class="form-group">
        <label>Данные покупателя</label>
        <textarea name="buyer_info" rows="3">{{ $proposal->buyer_info }}</textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Валюта</label>
            <input type="text" name="currency" value="{{ $proposal->currency }}" maxlength="10">
        </div>
        <div class="form-group">
            <label>НДС, %</label>
            <input type="number" name="vat_rate" step="0.1" value="{{ $proposal->vat_rate }}" min="0" max="100">
        </div>
    </div>

    <div class="form-group">
        <label>Условия оплаты/поставки</label>
        <input type="text" name="terms" value="{{ $proposal->terms }}">
    </div>

    <div class="form-group">
        <label>Содержание КП *</label>
        <div id="editor" style="height: 300px;">{!! $proposal->body_html !!}</div>
        <textarea name="body_html" id="body_html" style="display: none;" required>{{ $proposal->body_html }}</textarea>
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
                @foreach($proposal->items as $index => $item)
                    <tr>
                        <td><input type="text" name="items[{{ $index }}][name]" value="{{ $item->name }}" required></td>
                        <td><input type="number" name="items[{{ $index }}][quantity]" step="0.01" value="{{ $item->quantity }}" required></td>
                        <td><input type="text" name="items[{{ $index }}][unit]" value="{{ $item->unit }}"></td>
                        <td><input type="number" name="items[{{ $index }}][price]" step="0.01" value="{{ $item->price }}" required></td>
                        <td><input type="number" name="items[{{ $index }}][discount]" step="0.1" value="{{ $item->discount }}" min="0" max="100"></td>
                        <td class="item-total">{{ number_format($item->total, 2) }}</td>
                        <td><button type="button" class="btn-remove" onclick="removeItem(this)">×</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <label>Статус</label>
        <select name="status">
            <option value="draft" {{ $proposal->status === 'draft' ? 'selected' : '' }}>Черновик</option>
            <option value="published" {{ $proposal->status === 'published' ? 'selected' : '' }}>Опубликовать</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/proposals/{{ $proposal->id }}" class="btn btn-secondary">Отмена</a>
    </div>
</form>

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="{{ asset('js/proposal-form.js') }}"></script>
@endpush
@endsection



