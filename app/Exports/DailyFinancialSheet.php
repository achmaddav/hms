<?php

namespace App\Exports;

use App\Models\CheckIn;
use App\Models\UtilityPayment;
use App\Models\SalaryPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DailyFinancialSheet implements FromCollection, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $hotelId;
    protected $date;
    
    public function __construct($hotelId, $date)
    {
        $this->hotelId = $hotelId;
        $this->date = $date;
    }
    
    public function collection()
    {
        return collect([]);
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $date = \Carbon\Carbon::parse($this->date);
                
                // Get hotel name
                $hotel = \App\Models\Hotel::find($this->hotelId);
                $hotelName = $hotel ? $hotel->name : 'Hotel';
                
                $currentRow = 1;
                
                // ============================================================
                // HEADER SECTION - Ultra Professional
                // ============================================================
                
                // Main Title
                $sheet->setCellValue('A1', 'LAPORAN KEUANGAN HARIAN');
                $sheet->mergeCells('A1:K1');
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'c4a962'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Date Info Box - Left Side
                $sheet->setCellValue('A3', 'TANGGAL');
                $sheet->setCellValue('B3', $date->format('d F Y'));
                $sheet->mergeCells('B3:D3');
                
                $sheet->setCellValue('A4', 'HARI');
                $sheet->setCellValue('B4', $date->locale('id')->isoFormat('dddd'));
                $sheet->mergeCells('B4:D4');
                
                // Hotel Info Box - Right Side
                $sheet->setCellValue('F3', 'HOTEL');
                $sheet->setCellValue('G3', $hotelName);
                $sheet->mergeCells('G3:H3');
                
                $sheet->setCellValue('F4', 'PERIODE');
                $sheet->setCellValue('G4', $date->format('d/m/Y'));
                $sheet->mergeCells('G4:H4');
                
                // Style info boxes
                $sheet->getStyle('A3:A4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                
                $sheet->getStyle('B3:D4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                
                $sheet->getStyle('F3:F4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                
                $sheet->getStyle('G3:H4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                
                $sheet->getRowDimension(3)->setRowHeight(22);
                $sheet->getRowDimension(4)->setRowHeight(22);
                
                $currentRow = 6;
                
                // ============================================================
                // SECTION 1: PEMASUKAN (REVENUE) - GREEN THEME
                // ============================================================
                
                $sheet->setCellValue("A{$currentRow}", 'PEMASUKAN (REVENUE)');
                $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10b981']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // A. PEMASUKAN KAMAR
                $checkIns = CheckIn::with(['room', 'payments', 'additionalCharges'])
                    ->where('hotel_id', $this->hotelId)
                    ->whereDate('check_in_date', $date)
                    ->get();
                
                $sheet->setCellValue("A{$currentRow}", 'A. PEMASUKAN KAMAR');
                $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(24);
                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '065f46']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd1fae5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                if ($checkIns->count() > 0) {
                    // Table Header
                    $headers = ['No', 'Booking Number', 'No. Kamar', 'Nama Tamu', 'Check-in', 'Check-out', 'Malam', 'Tarif/Malam', 'Subtotal', 'Total'];
                    $col = 'A';
                    foreach ($headers as $header) {
                        $sheet->setCellValue("{$col}{$currentRow}", $header);
                        $col++;
                    }
                    
                    $sheet->getRowDimension($currentRow)->setRowHeight(22);
                    $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1f2937']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                    ]);
                    $currentRow++;
                    
                    $totalRoomRevenue = 0;
                    $no = 1;
                    $startDataRow = $currentRow;
                    
                    foreach ($checkIns as $checkIn) {
                        $sheet->setCellValue("A{$currentRow}", $no++);
                        $sheet->setCellValue("B{$currentRow}", $checkIn->booking_number);
                        $sheet->setCellValue("C{$currentRow}", $checkIn->room->room_number);
                        $sheet->setCellValue("D{$currentRow}", $checkIn->guest_name);
                        $sheet->setCellValue("E{$currentRow}", $checkIn->check_in_date->format('d/m/Y'));
                        $sheet->setCellValue("F{$currentRow}", $checkIn->check_out_date ? $checkIn->check_out_date->format('d/m/Y') : '-');
                        $sheet->setCellValue("G{$currentRow}", $checkIn->total_nights);
                        $sheet->setCellValue("H{$currentRow}", $checkIn->price_per_night);
                        $sheet->setCellValue("I{$currentRow}", $checkIn->price_per_night * $checkIn->total_nights);
                        $sheet->setCellValue("J{$currentRow}", $checkIn->total_amount);
                        
                        // Data row styling
                        $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray([
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']],
                            ],
                        ]);
                        
                        // Center align numbers
                        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        
                        // Right align currency
                        $sheet->getStyle("H{$currentRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        
                        $currentRow++;
                        $totalRoomRevenue += $checkIn->total_amount;
                    }
                    
                    // Subtotal Row
                    $sheet->setCellValue("I{$currentRow}", 'Total Pemasukan Kamar:');
                    $sheet->setCellValue("J{$currentRow}", $totalRoomRevenue);
                    $sheet->getRowDimension($currentRow)->setRowHeight(24);
                    $sheet->getStyle("I{$currentRow}:J{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fef3c7']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => 'f59e0b']],
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f59e0b']],
                        ],
                    ]);
                    $currentRow++;
                } else {
                    $sheet->setCellValue("A{$currentRow}", 'Tidak ada transaksi pemasukan kamar pada tanggal ini');
                    $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                    $sheet->getStyle("A{$currentRow}")->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '6b7280']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $currentRow++;
                    $totalRoomRevenue = 0;
                }
                
                $currentRow++; // Empty row
                
                // B. LAYANAN TAMBAHAN
                $additionalCharges = CheckIn::where('hotel_id', $this->hotelId)
                    ->whereDate('check_in_date', $date)
                    ->with(['additionalCharges', 'room'])
                    ->get()
                    ->pluck('additionalCharges')
                    ->flatten();
                
                $sheet->setCellValue("A{$currentRow}", 'B. LAYANAN TAMBAHAN');
                $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(24);
                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '065f46']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd1fae5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                if ($additionalCharges->count() > 0) {
                    // Table Header
                    $headers = ['No', 'Booking Number', 'No. Kamar', 'Deskripsi Layanan', 'Jumlah'];
                    $col = 'A';
                    foreach ($headers as $header) {
                        $sheet->setCellValue("{$col}{$currentRow}", $header);
                        $col++;
                    }
                    
                    $sheet->getRowDimension($currentRow)->setRowHeight(22);
                    $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1f2937']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                    ]);
                    $currentRow++;
                    
                    $totalServicesRevenue = 0;
                    $no = 1;
                    
                    foreach ($additionalCharges as $charge) {
                        $sheet->setCellValue("A{$currentRow}", $no++);
                        $sheet->setCellValue("B{$currentRow}", $charge->checkIn->booking_number);
                        $sheet->setCellValue("C{$currentRow}", $charge->checkIn->room->room_number);
                        $sheet->setCellValue("D{$currentRow}", $charge->description);
                        $sheet->setCellValue("E{$currentRow}", $charge->amount);
                        
                        $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                        ]);
                        
                        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        
                        $currentRow++;
                        $totalServicesRevenue += $charge->amount;
                    }
                    
                    // Subtotal Row
                    $sheet->setCellValue("D{$currentRow}", 'Total Layanan Tambahan:');
                    $sheet->setCellValue("E{$currentRow}", $totalServicesRevenue);
                    $sheet->getRowDimension($currentRow)->setRowHeight(24);
                    $sheet->getStyle("D{$currentRow}:E{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fef3c7']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => 'f59e0b']],
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f59e0b']],
                        ],
                    ]);
                    $currentRow++;
                } else {
                    $sheet->setCellValue("A{$currentRow}", 'Tidak ada transaksi layanan tambahan pada tanggal ini');
                    $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                    $sheet->getStyle("A{$currentRow}")->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '6b7280']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $currentRow++;
                    $totalServicesRevenue = 0;
                }
                
                $currentRow++; // Empty row
                
                // GRAND TOTAL PEMASUKAN
                $totalRevenue = $totalRoomRevenue + $totalServicesRevenue;
                $sheet->setCellValue("I{$currentRow}", 'TOTAL PEMASUKAN HARI INI');
                $sheet->setCellValue("J{$currentRow}", $totalRevenue);
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("I{$currentRow}:J{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10b981']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '059669']]],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                $currentRow++; // Empty row
                
                // ============================================================
                // SECTION 2: PENGELUARAN (EXPENSE) - RED THEME
                // ============================================================
                
                $sheet->setCellValue("A{$currentRow}", 'PENGELUARAN (EXPENSE)');
                $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ef4444']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // A. PENGELUARAN UTILITAS
                $utilities = UtilityPayment::with(['room'])
                    ->where('hotel_id', $this->hotelId)
                    ->whereDate('created_at', $date)
                    ->get();
                
                $sheet->setCellValue("A{$currentRow}", 'A. PENGELUARAN UTILITAS');
                $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(24);
                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '991b1b']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                if ($utilities->count() > 0) {
                    // Table Header
                    $headers = ['No', 'Payment #', 'No. Kamar', 'Jenis', 'Periode', 'Pemakaian', 'Tarif/Unit', 'Biaya Tetap', 'Biaya Pakai', 'PPN 11%', 'Total'];
                    $col = 'A';
                    foreach ($headers as $header) {
                        $sheet->setCellValue("{$col}{$currentRow}", $header);
                        $col++;
                    }
                    
                    $sheet->getRowDimension($currentRow)->setRowHeight(22);
                    $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1f2937']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                    ]);
                    $currentRow++;
                    
                    $totalUtilityExpense = 0;
                    $no = 1;
                    
                    foreach ($utilities as $utility) {
                        $unit = '';
                        if ($utility->utility_type == 'electricity') $unit = 'kWh';
                        elseif ($utility->utility_type == 'water') $unit = 'm³';
                        elseif ($utility->utility_type == 'gas') $unit = 'm³';
                        
                        $usage = $utility->usage ? number_format($utility->usage, 1) . ' ' . $unit : '-';
                        
                        $sheet->setCellValue("A{$currentRow}", $no++);
                        $sheet->setCellValue("B{$currentRow}", $utility->payment_number);
                        $sheet->setCellValue("C{$currentRow}", $utility->room ? $utility->room->room_number : 'Hotel');
                        $sheet->setCellValue("D{$currentRow}", $utility->getUtilityTypeLabel());
                        $sheet->setCellValue("E{$currentRow}", \Carbon\Carbon::parse($utility->month_year)->format('M Y'));
                        $sheet->setCellValue("F{$currentRow}", $usage);
                        $sheet->setCellValue("G{$currentRow}", $utility->rate_per_unit);
                        $sheet->setCellValue("H{$currentRow}", $utility->base_charge);
                        $sheet->setCellValue("I{$currentRow}", $utility->usage_charge ?? 0);
                        $sheet->setCellValue("J{$currentRow}", $utility->tax);
                        $sheet->setCellValue("K{$currentRow}", $utility->total_amount);
                        
                        $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                        ]);
                        
                        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("G{$currentRow}:K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        
                        $currentRow++;
                        $totalUtilityExpense += $utility->total_amount;
                    }
                    
                    // Subtotal Row
                    $sheet->setCellValue("J{$currentRow}", 'Total Utilitas:');
                    $sheet->setCellValue("K{$currentRow}", $totalUtilityExpense);
                    $sheet->getRowDimension($currentRow)->setRowHeight(24);
                    $sheet->getStyle("J{$currentRow}:K{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fef3c7']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => 'f59e0b']],
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f59e0b']],
                        ],
                    ]);
                    $currentRow++;
                } else {
                    $sheet->setCellValue("A{$currentRow}", 'Tidak ada transaksi pengeluaran utilitas pada tanggal ini');
                    $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                    $sheet->getStyle("A{$currentRow}")->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '6b7280']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $currentRow++;
                    $totalUtilityExpense = 0;
                }
                
                $currentRow++; // Empty row
                
                // B. PENGELUARAN GAJI - Continue in next part...
                $salaries = SalaryPayment::with(['employee'])
                    ->where('hotel_id', $this->hotelId)
                    ->whereDate('created_at', $date)
                    ->get();
                
                $sheet->setCellValue("A{$currentRow}", 'B. PENGELUARAN GAJI KARYAWAN');
                $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(24);
                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '991b1b']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                if ($salaries->count() > 0) {
                    // Table Header - Salary has more columns, so we'll use A to O
                    $headers = ['No', 'Payment #', 'Nama', 'Jabatan', 'Periode', 'Gaji Pokok', 'Tunjangan', 'Lembur', 'Bonus', 'Gaji Kotor', 'Pajak', 'Asuransi', 'Pot. Lain', 'Tot. Pot.', 'Gaji Bersih'];
                    $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
                    for ($i = 0; $i < count($headers); $i++) {
                        $sheet->setCellValue("{$cols[$i]}{$currentRow}", $headers[$i]);
                    }
                    
                    $sheet->getRowDimension($currentRow)->setRowHeight(22);
                    $sheet->getStyle("A{$currentRow}:O{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1f2937']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                    ]);
                    $currentRow++;
                    
                    $totalSalaryExpense = 0;
                    $no = 1;
                    
                    foreach ($salaries as $salary) {
                        $sheet->setCellValue("A{$currentRow}", $no++);
                        $sheet->setCellValue("B{$currentRow}", $salary->payment_number);
                        $sheet->setCellValue("C{$currentRow}", $salary->employee->name);
                        $sheet->setCellValue("D{$currentRow}", ucfirst($salary->employee->role));
                        $sheet->setCellValue("E{$currentRow}", \Carbon\Carbon::parse($salary->month_year)->format('M Y'));
                        $sheet->setCellValue("F{$currentRow}", $salary->base_salary);
                        $sheet->setCellValue("G{$currentRow}", $salary->allowances);
                        $sheet->setCellValue("H{$currentRow}", $salary->overtime);
                        $sheet->setCellValue("I{$currentRow}", $salary->bonus);
                        $sheet->setCellValue("J{$currentRow}", $salary->gross_salary);
                        $sheet->setCellValue("K{$currentRow}", $salary->tax);
                        $sheet->setCellValue("L{$currentRow}", $salary->insurance);
                        $sheet->setCellValue("M{$currentRow}", $salary->other_deductions);
                        $sheet->setCellValue("N{$currentRow}", $salary->total_deductions);
                        $sheet->setCellValue("O{$currentRow}", $salary->net_salary);
                        
                        $sheet->getStyle("A{$currentRow}:O{$currentRow}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                        ]);
                        
                        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("F{$currentRow}:O{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        
                        $currentRow++;
                        $totalSalaryExpense += $salary->net_salary;
                    }
                    
                    // Subtotal Row
                    $sheet->setCellValue("N{$currentRow}", 'Total Gaji:');
                    $sheet->setCellValue("O{$currentRow}", $totalSalaryExpense);
                    $sheet->getRowDimension($currentRow)->setRowHeight(24);
                    $sheet->getStyle("N{$currentRow}:O{$currentRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fef3c7']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => 'f59e0b']],
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f59e0b']],
                        ],
                    ]);
                    $currentRow++;
                } else {
                    $sheet->setCellValue("A{$currentRow}", 'Tidak ada transaksi pembayaran gaji pada tanggal ini');
                    $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                    $sheet->getStyle("A{$currentRow}")->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '6b7280']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $currentRow++;
                    $totalSalaryExpense = 0;
                }
                
                $currentRow++; // Empty row
                
                // GRAND TOTAL PENGELUARAN
                $totalExpense = $totalUtilityExpense + $totalSalaryExpense;
                $sheet->setCellValue("I{$currentRow}", 'TOTAL PENGELUARAN HARI INI');
                $sheet->setCellValue("J{$currentRow}", $totalExpense);
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("I{$currentRow}:J{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ef4444']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => 'dc2626']]],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                $currentRow++; // Empty row
                
                // ============================================================
                // SECTION 3: RINGKASAN HARIAN - GOLD THEME
                // ============================================================
                
                $netProfit = $totalRevenue - $totalExpense;
                
                $sheet->setCellValue("A{$currentRow}", 'RINGKASAN HARIAN');
                $sheet->mergeCells("A{$currentRow}:K{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'c4a962']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Summary Table Header
                $sheet->setCellValue("A{$currentRow}", 'Kategori');
                $sheet->setCellValue("B{$currentRow}", 'Jumlah (Rp)');
                $sheet->setCellValue("C{$currentRow}", 'Keterangan');
                $sheet->mergeCells("C{$currentRow}:E{$currentRow}");
                
                $sheet->getRowDimension($currentRow)->setRowHeight(22);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1f2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                $currentRow++;
                
                // Revenue Row
                $sheet->setCellValue("A{$currentRow}", 'Total Pemasukan');
                $sheet->setCellValue("B{$currentRow}", $totalRevenue);
                $sheet->setCellValue("C{$currentRow}", 'Revenue dari kamar & layanan tambahan');
                $sheet->mergeCells("C{$currentRow}:E{$currentRow}");
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd1fae5']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Expense Row
                $sheet->setCellValue("A{$currentRow}", 'Total Pengeluaran');
                $sheet->setCellValue("B{$currentRow}", $totalExpense);
                $sheet->setCellValue("C{$currentRow}", 'Biaya operasional & gaji karyawan');
                $sheet->mergeCells("C{$currentRow}:E{$currentRow}");
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Profit/Loss Row
                $profitLabel = $netProfit >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH';
                $profitColor = $netProfit >= 0 ? '10b981' : 'ef4444';
                $profitNote = $netProfit >= 0 ? 'Profit hari ini - Kondisi keuangan sehat!' : 'Loss hari ini - Perlu evaluasi pengeluaran';
                
                $sheet->setCellValue("A{$currentRow}", $profitLabel);
                $sheet->setCellValue("B{$currentRow}", $netProfit);
                $sheet->setCellValue("C{$currentRow}", $profitNote);
                $sheet->mergeCells("C{$currentRow}:E{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $profitColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => $profitColor]]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Additional Metrics
                $sheet->setCellValue("A{$currentRow}", 'Profit Margin');
                $sheet->setCellValue("B{$currentRow}", $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("C{$currentRow}", 'Persentase keuntungan dari total pemasukan');
                $sheet->mergeCells("C{$currentRow}:E{$currentRow}");
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fef3c7']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Number formatting for all currency columns
                $sheet->getStyle("H:O")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("B:B")->getNumberFormat()->setFormatCode('#,##0');
            },
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 16,
            'C' => 12,
            'D' => 25,
            'E' => 12,
            'F' => 13,
            'G' => 12,
            'H' => 13,
            'I' => 35,
            'J' => 13,
            'K' => 15,
            'L' => 12,
            'M' => 12,
            'N' => 14,
            'O' => 15,
        ];
    }
    
    public function title(): string
    {
        return \Carbon\Carbon::parse($this->date)->format('d-M-Y');
    }
}
