<?php

namespace App\Exports;

use App\Models\Kasbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Carbon;


class KaryawanExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return Kasbon::with('karyawan.user')
            ->get()
            ->map(function ($item) {
                return [
                    'Nama Karyawan'     => $item->karyawan->user->name ?? '-',
                    ' Jabatan'     => $item->karyawan->jabatan ?? '-',
                    'Gaji Pokok'     => $item->karyawan->gaji_pokok,
                    'Jumlah Kasbon'     => $item->jumlah,
                ];
            });
    }

    public function headings(): array
    {
        return [
            ['DATA KARYAWAN'], // Judul utama
            ['Nama Karyawan', 'Jabatan', 'Gaji Pokok', 'Jumlah Kasbon'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge judul besar
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Heading tabel
        $sheet->getStyle('A2:D2')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:D2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F81BD');

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Range semua data mulai dari A2
                $cellRange = 'A2:' . $highestColumn . $highestRow;

                // Border kotak
                $sheet->getStyle($cellRange)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
