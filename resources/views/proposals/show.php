@extends('layouts.app')

@section('title', $proposal->title)

@section('content')
<div class="proposal-view">
    <div class="proposal-header">
        <h1>{{ $proposal->title }}</h1>
        <div class="proposal-actions">
            @if(session('user_id') === $proposal->user_id || session('user_role') === 'admin')
                <a href="/proposals/{{ $proposal->id }}/edit" class="btn">Редактировать</a>
            @endif
            <a href="/proposals/{{ $proposal->id }}/pdf" class="btn" target="_blank">Скачать PDF</a>
            <a href="/proposals/{{ $proposal->id }}/docx" class="btn">Скачать DOCX</a>
        </div>
    </div>

    <div class="proposal-info">
        <p><strong>Номер:</strong> {{ $proposal->offer_number ?: 'N/A' }}</p>
        <p><strong>Дата:</strong> {{ $proposal->offer_date->format('d.m.Y') }}</p>
        @if($proposal->seller_info)
            <p><strong>Продавец:</strong> {{ $proposal->seller_info }}</p>
        @endif
        @if($proposal->buyer_info)
            <p><strong>Покупатель:</strong> {{ $proposal->buyer_info }}</p>
        @endif
    </div>

    <div class="proposal-content">
        {!! $proposal->body_html !!}
    </div>

    @if($proposal->items->count() > 0)
        <div class="proposal-items">
            <h2>Товары/услуги</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th>Кол-во</th>
                        <th>Ед.</th>
                        <th>Цена</th>
                        <th>Скидка %</th>
                        <th>Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proposal->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ number_format($item->price, 2) }} {{ $proposal->currency }}</td>
                            <td>{{ $item->discount }}%</td>
                            <td>{{ number_format($item->total, 2) }} {{ $proposal->currency }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @php
                $totals = $proposal->calculateTotals();
            @endphp

            <div class="totals">
                <div class="total-row">
                    <span>Итого без НДС:</span>
                    <strong>{{ number_format($totals['subtotal'], 2) }} {{ $proposal->currency }}</strong>
                </div>
                <div class="total-row">
                    <span>НДС ({{ $proposal->vat_rate }}%):</span>
                    <strong>{{ number_format($totals['vat'], 2) }} {{ $proposal->currency }}</strong>
                </div>
                <div class="total-row total-final">
                    <span>Всего к оплате:</span>
                    <strong>{{ number_format($totals['total'], 2) }} {{ $proposal->currency }}</strong>
                </div>
            </div>
        </div>
    @endif

    @if($proposal->terms)
        <div class="proposal-terms">
            <h3>Условия</h3>
            <p>{{ $proposal->terms }}</p>
        </div>
    @endif
</div>
@endsection



