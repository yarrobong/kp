<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Http\Request;
use App\Http\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;

class ExportController extends Controller
{
    public function pdf(Request $request, $id)
    {
        $proposal = Proposal::with(['user', 'items'])->findOrFail($id);

        // Проверка прав
        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userRole !== 'admin' && $proposal->user_id !== $userId && !$proposal->isPublished()) {
            abort(403);
        }

        $totals = $proposal->calculateTotals();

        $html = view('exports.pdf', compact('proposal', 'totals'))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'proposal_' . $proposal->id . '_' . date('Y-m-d') . '.pdf';

        if ($request->expectsJson()) {
            return Response::json([
                'pdf' => base64_encode($dompdf->output()),
                'filename' => $filename,
            ]);
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $dompdf->output();
        exit;
    }

    public function docx(Request $request, $id)
    {
        $proposal = Proposal::with(['user', 'items'])->findOrFail($id);

        // Проверка прав
        $userId = session('user_id');
        $userRole = session('user_role');
        
        if ($userRole !== 'admin' && $proposal->user_id !== $userId && !$proposal->isPublished()) {
            abort(403);
        }

        $totals = $proposal->calculateTotals();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Заголовок
        $section->addText($proposal->title, ['bold' => true, 'size' => 16]);

        // Данные
        $section->addText('Номер: ' . ($proposal->offer_number ?: 'N/A'));
        $section->addText('Дата: ' . $proposal->offer_date->format('d.m.Y'));

        if ($proposal->seller_info) {
            $section->addText('Продавец: ' . $proposal->seller_info);
        }

        if ($proposal->buyer_info) {
            $section->addText('Покупатель: ' . $proposal->buyer_info);
        }

        $section->addTextBreak();

        // Таблица товаров
        if ($proposal->items->count() > 0) {
            $table = $section->addTable();
            $table->addRow();
            $table->addCell(2000)->addText('Наименование');
            $table->addCell(1000)->addText('Кол-во');
            $table->addCell(800)->addText('Ед.');
            $table->addCell(1500)->addText('Цена');
            $table->addCell(1000)->addText('Скидка %');
            $table->addCell(1500)->addText('Сумма');

            foreach ($proposal->items as $item) {
                $table->addRow();
                $table->addCell(2000)->addText($item->name);
                $table->addCell(1000)->addText($item->quantity);
                $table->addCell(800)->addText($item->unit);
                $table->addCell(1500)->addText(number_format($item->price, 2));
                $table->addCell(1000)->addText($item->discount);
                $table->addCell(1500)->addText(number_format($item->total, 2));
            }
        }

        $section->addTextBreak();
        $section->addText('Итого без НДС: ' . number_format($totals['subtotal'], 2) . ' ' . $proposal->currency);
        $section->addText('НДС (' . $proposal->vat_rate . '%): ' . number_format($totals['vat'], 2) . ' ' . $proposal->currency);
        $section->addText('Всего к оплате: ' . number_format($totals['total'], 2) . ' ' . $proposal->currency, ['bold' => true]);

        if ($proposal->terms) {
            $section->addTextBreak();
            $section->addText('Условия: ' . $proposal->terms);
        }

        // Контент из редактора
        if ($proposal->body_html) {
            $section->addTextBreak();
            Html::addHtml($section, $proposal->body_html);
        }

        $filename = 'proposal_' . $proposal->id . '_' . date('Y-m-d') . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'proposal_');
        
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        if ($request->expectsJson()) {
            return Response::json([
                'docx' => base64_encode(file_get_contents($tempFile)),
                'filename' => $filename,
            ]);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($tempFile);
        unlink($tempFile);
        exit;
    }
}



