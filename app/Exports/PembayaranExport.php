<?php

namespace App\Exports;

use App\Models\Pembayaran;
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

class PembayaranExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return Pembayaran::with('kasbon.karyawan.user')
            ->get()
            ->map(function ($item) {
                return [
                    'Karyawan' => $item->kasbon->karyawan->user->name ?? '-',
                    'Total Kasbon' => $item->kasbon->jumlah,
                    'Jumlah Bayar' => $item->jumlah_bayar,
                    'Metode Pembayaran' => $item->metode === 'potong_gaji' ? 'Potong Gaji' : 'Manual',
                    'Tanggal Pembayaran' => $item->tanggal_bayar
                        ? Carbon::parse($item->tanggal_bayar)->format('d M Y')
                        : null,
                ];
            });
    }

    public function headings(): array
    {
        return [
            ['DATA PEMBAYARAN'], // Judul utama
            ['Nama Karyawan', 'Total Kasbon', 'Jumlah Bayar', 'Metode Pembayaran', 'Tanggal Pembayaran'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Judul besar
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Heading tabel (baris 2)
        $sheet->getStyle('A2:E2')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF'); // warna tulisan putih
        $sheet->getStyle('A2:E2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:E2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F81BD'); // warna biru header

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Range semua tabel dari A2 sampai akhir data
                $cellRange = 'A2:' . $highestColumn . $highestRow;

                // Tambahkan border kotak tipis
                $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
