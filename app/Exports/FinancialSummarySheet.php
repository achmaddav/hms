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

class FinancialSummarySheet implements FromCollection, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $hotelId;
    protected $startDate;
    protected $endDate;
    
    public function __construct($hotelId, $startDate, $endDate)
    {
        $this->hotelId = $hotelId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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
                
                $start = \Carbon\Carbon::parse($this->startDate);
                $end = \Carbon\Carbon::parse($this->endDate);
                
                // Get hotel name
                $hotel = \App\Models\Hotel::find($this->hotelId);
                $hotelName = $hotel ? $hotel->name : 'Hotel';
                
                $currentRow = 1;
                
                // ============================================================
                // HEADER SECTION
                // ============================================================
                
                // Main Title
                $sheet->setCellValue('A1', 'LAPORAN LABA RUGI');
                $sheet->mergeCells('A1:E1');
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getStyle('A1:E1')->applyFromArray([
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
                
                $sheet->setCellValue('A2', 'PROFIT & LOSS STATEMENT');
                $sheet->mergeCells('A2:E2');
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getStyle('A2:E2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'd4ba7a'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Info Box
                $sheet->setCellValue('A4', 'PERIODE');
                $sheet->setCellValue('B4', $start->format('d F Y') . ' - ' . $end->format('d F Y'));
                $sheet->mergeCells('B4:E4');
                
                $sheet->setCellValue('A5', 'HOTEL');
                $sheet->setCellValue('B5', $hotelName);
                $sheet->mergeCells('B5:E5');
                
                $sheet->setCellValue('A6', 'JUMLAH HARI');
                $sheet->setCellValue('B6', $start->diffInDays($end) + 1 . ' hari');
                $sheet->mergeCells('B6:E6');
                
                $sheet->getStyle('A4:A6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                
                $sheet->getStyle('B4:E6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f9fafb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                
                $sheet->getRowDimension(4)->setRowHeight(22);
                $sheet->getRowDimension(5)->setRowHeight(22);
                $sheet->getRowDimension(6)->setRowHeight(22);
                
                $currentRow = 8;
                
                // ============================================================
                // CALCULATE ALL TOTALS
                // ============================================================
                
                // Room Revenue
                $roomRevenue = CheckIn::where('hotel_id', $this->hotelId)
                    ->whereBetween('check_in_date', [$this->startDate, $this->endDate])
                    ->sum('total_amount');
                
                $roomCount = CheckIn::where('hotel_id', $this->hotelId)
                    ->whereBetween('check_in_date', [$this->startDate, $this->endDate])
                    ->count();
                
                // Additional Services
                $additionalServicesRevenue = CheckIn::where('hotel_id', $this->hotelId)
                    ->whereBetween('check_in_date', [$this->startDate, $this->endDate])
                    ->withSum('additionalCharges', 'amount')
                    ->get()
                    ->sum('additional_charges_sum_amount');
                
                $servicesCount = CheckIn::where('hotel_id', $this->hotelId)
                    ->whereBetween('check_in_date', [$this->startDate, $this->endDate])
                    ->has('additionalCharges')
                    ->count();
                
                // Utilities
                $electricityExpense = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('utility_type', 'electricity')
                    ->sum('total_amount');
                
                $electricityCount = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('utility_type', 'electricity')
                    ->count();
                
                $waterExpense = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('utility_type', 'water')
                    ->sum('total_amount');
                
                $waterCount = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('utility_type', 'water')
                    ->count();
                
                $gasExpense = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('utility_type', 'gas')
                    ->sum('total_amount');
                
                $gasCount = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('utility_type', 'gas')
                    ->count();
                
                $internetExpense = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('utility_type', 'internet')
                    ->sum('total_amount');
                
                $internetCount = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('utility_type', 'internet')
                    ->count();
                
                $otherUtilitiesExpense = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->whereNotIn('utility_type', ['electricity', 'water', 'gas', 'internet'])
                    ->sum('total_amount');
                
                $otherUtilitiesCount = UtilityPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->whereNotIn('utility_type', ['electricity', 'water', 'gas', 'internet'])
                    ->count();
                
                // Salary
                $salaryExpense = SalaryPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->sum('net_salary');
                
                $salaryCount = SalaryPayment::where('hotel_id', $this->hotelId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->count();
                
                // Totals
                $totalRevenue = $roomRevenue + $additionalServicesRevenue;
                $totalUtilityExpense = $electricityExpense + $waterExpense + $gasExpense + $internetExpense + $otherUtilitiesExpense;
                $totalExpense = $totalUtilityExpense + $salaryExpense;
                $netProfit = $totalRevenue - $totalExpense;
                
                // ============================================================
                // SECTION 1: PEMASUKAN (REVENUE) - GREEN THEME
                // ============================================================
                
                $sheet->setCellValue("A{$currentRow}", 'PEMASUKAN (REVENUE)');
                $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10b981']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Table Header
                $sheet->setCellValue("A{$currentRow}", 'Kategori');
                $sheet->setCellValue("B{$currentRow}", 'Jumlah Transaksi');
                $sheet->setCellValue("C{$currentRow}", 'Total (Rp)');
                $sheet->setCellValue("D{$currentRow}", 'Persentase');
                $sheet->setCellValue("E{$currentRow}", 'Rata-rata per Hari');
                
                $sheet->getRowDimension($currentRow)->setRowHeight(22);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1f2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                $currentRow++;
                
                // Room Revenue Row
                $sheet->setCellValue("A{$currentRow}", 'Pemasukan Kamar');
                $sheet->setCellValue("B{$currentRow}", $roomCount);
                $sheet->setCellValue("C{$currentRow}", $roomRevenue);
                $sheet->setCellValue("D{$currentRow}", $totalRevenue > 0 ? number_format(($roomRevenue / $totalRevenue) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($roomRevenue / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Services Row
                $sheet->setCellValue("A{$currentRow}", 'Layanan Tambahan');
                $sheet->setCellValue("B{$currentRow}", $servicesCount);
                $sheet->setCellValue("C{$currentRow}", $additionalServicesRevenue);
                $sheet->setCellValue("D{$currentRow}", $totalRevenue > 0 ? number_format(($additionalServicesRevenue / $totalRevenue) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($additionalServicesRevenue / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Total Revenue Row
                $sheet->setCellValue("A{$currentRow}", 'TOTAL PEMASUKAN');
                $sheet->setCellValue("B{$currentRow}", $roomCount + $servicesCount);
                $sheet->setCellValue("C{$currentRow}", $totalRevenue);
                $sheet->setCellValue("D{$currentRow}", '100%');
                $sheet->setCellValue("E{$currentRow}", number_format($totalRevenue / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getRowDimension($currentRow)->setRowHeight(24);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd1fae5']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '10b981']],
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '10b981']],
                    ],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                $currentRow++; // Empty row
                $currentRow++; // Empty row
                
                // ============================================================
                // SECTION 2: PENGELUARAN (EXPENSE) - RED THEME
                // ============================================================
                
                $sheet->setCellValue("A{$currentRow}", 'PENGELUARAN (EXPENSE)');
                $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ef4444']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Table Header
                $sheet->setCellValue("A{$currentRow}", 'Kategori');
                $sheet->setCellValue("B{$currentRow}", 'Jumlah Transaksi');
                $sheet->setCellValue("C{$currentRow}", 'Total (Rp)');
                $sheet->setCellValue("D{$currentRow}", 'Persentase');
                $sheet->setCellValue("E{$currentRow}", 'Rata-rata per Hari');
                
                $sheet->getRowDimension($currentRow)->setRowHeight(22);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1f2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                $currentRow++;
                
                // Utilities Sub-header
                $sheet->setCellValue("A{$currentRow}", 'UTILITAS');
                $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(20);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '991b1b']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                // Electricity
                $sheet->setCellValue("A{$currentRow}", '  - Listrik');
                $sheet->setCellValue("B{$currentRow}", $electricityCount);
                $sheet->setCellValue("C{$currentRow}", $electricityExpense);
                $sheet->setCellValue("D{$currentRow}", $totalExpense > 0 ? number_format(($electricityExpense / $totalExpense) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($electricityExpense / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Water
                $sheet->setCellValue("A{$currentRow}", '  - Air');
                $sheet->setCellValue("B{$currentRow}", $waterCount);
                $sheet->setCellValue("C{$currentRow}", $waterExpense);
                $sheet->setCellValue("D{$currentRow}", $totalExpense > 0 ? number_format(($waterExpense / $totalExpense) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($waterExpense / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Gas
                $sheet->setCellValue("A{$currentRow}", '  - Gas');
                $sheet->setCellValue("B{$currentRow}", $gasCount);
                $sheet->setCellValue("C{$currentRow}", $gasExpense);
                $sheet->setCellValue("D{$currentRow}", $totalExpense > 0 ? number_format(($gasExpense / $totalExpense) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($gasExpense / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Internet
                $sheet->setCellValue("A{$currentRow}", '  - Internet');
                $sheet->setCellValue("B{$currentRow}", $internetCount);
                $sheet->setCellValue("C{$currentRow}", $internetExpense);
                $sheet->setCellValue("D{$currentRow}", $totalExpense > 0 ? number_format(($internetExpense / $totalExpense) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($internetExpense / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Other Utilities
                $sheet->setCellValue("A{$currentRow}", '  - Lainnya');
                $sheet->setCellValue("B{$currentRow}", $otherUtilitiesCount);
                $sheet->setCellValue("C{$currentRow}", $otherUtilitiesExpense);
                $sheet->setCellValue("D{$currentRow}", $totalExpense > 0 ? number_format(($otherUtilitiesExpense / $totalExpense) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($otherUtilitiesExpense / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Subtotal Utilities
                $totalUtilityCount = $electricityCount + $waterCount + $gasCount + $internetCount + $otherUtilitiesCount;
                $sheet->setCellValue("A{$currentRow}", 'Subtotal Utilitas');
                $sheet->setCellValue("B{$currentRow}", $totalUtilityCount);
                $sheet->setCellValue("C{$currentRow}", $totalUtilityExpense);
                $sheet->setCellValue("D{$currentRow}", $totalExpense > 0 ? number_format(($totalUtilityExpense / $totalExpense) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($totalUtilityExpense / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getRowDimension($currentRow)->setRowHeight(22);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fef3c7']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f59e0b']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Salary Row
                $sheet->setCellValue("A{$currentRow}", 'Gaji Karyawan');
                $sheet->setCellValue("B{$currentRow}", $salaryCount);
                $sheet->setCellValue("C{$currentRow}", $salaryExpense);
                $sheet->setCellValue("D{$currentRow}", $totalExpense > 0 ? number_format(($salaryExpense / $totalExpense) * 100, 2) . '%' : '0%');
                $sheet->setCellValue("E{$currentRow}", number_format($salaryExpense / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Total Expense Row
                $sheet->setCellValue("A{$currentRow}", 'TOTAL PENGELUARAN');
                $sheet->setCellValue("B{$currentRow}", $totalUtilityCount + $salaryCount);
                $sheet->setCellValue("C{$currentRow}", $totalExpense);
                $sheet->setCellValue("D{$currentRow}", '100%');
                $sheet->setCellValue("E{$currentRow}", number_format($totalExpense / max(1, $start->diffInDays($end) + 1), 0));
                
                $sheet->getRowDimension($currentRow)->setRowHeight(24);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => 'ef4444']],
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'ef4444']],
                    ],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                $currentRow++; // Empty row
                $currentRow++; // Empty row
                
                // ============================================================
                // SECTION 3: PROFIT/LOSS - GOLD THEME
                // ============================================================
                
                $sheet->setCellValue("A{$currentRow}", 'LABA / RUGI (PROFIT / LOSS)');
                $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(28);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'c4a962']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Summary Table
                $sheet->setCellValue("A{$currentRow}", 'Keterangan');
                $sheet->setCellValue("B{$currentRow}", 'Total (Rp)');
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getRowDimension($currentRow)->setRowHeight(22);
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1f2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                $currentRow++;
                
                // Total Revenue
                $sheet->setCellValue("A{$currentRow}", 'Total Pemasukan');
                $sheet->setCellValue("B{$currentRow}", $totalRevenue);
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd1fae5']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Total Expense
                $sheet->setCellValue("A{$currentRow}", 'Total Pengeluaran');
                $sheet->setCellValue("B{$currentRow}", $totalExpense);
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Net Profit/Loss
                $profitLabel = $netProfit >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH';
                $profitColor = $netProfit >= 0 ? '10b981' : 'ef4444';
                
                $sheet->setCellValue("A{$currentRow}", $profitLabel);
                $sheet->setCellValue("B{$currentRow}", $netProfit);
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getRowDimension($currentRow)->setRowHeight(30);
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $profitColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => $profitColor]]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                $currentRow++; // Empty row
                $currentRow++; // Empty row
                
                // ============================================================
                // SECTION 4: ADDITIONAL METRICS
                // ============================================================
                
                $sheet->setCellValue("A{$currentRow}", 'METRIK TAMBAHAN');
                $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
                $sheet->getRowDimension($currentRow)->setRowHeight(24);
                $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '374151']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f3f4f6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $currentRow++;
                
                $currentRow++; // Empty row
                
                // Metrics Table
                $sheet->setCellValue("A{$currentRow}", 'Metrik');
                $sheet->setCellValue("B{$currentRow}", 'Nilai');
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getRowDimension($currentRow)->setRowHeight(22);
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1f2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e5e7eb']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9ca3af']]],
                ]);
                $currentRow++;
                
                // Profit Margin
                $sheet->setCellValue("A{$currentRow}", 'Profit Margin');
                $sheet->setCellValue("B{$currentRow}", $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 2) . '%' : '0%');
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fef3c7']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Average Revenue per Day
                $sheet->setCellValue("A{$currentRow}", 'Rata-rata Pemasukan per Hari');
                $sheet->setCellValue("B{$currentRow}", number_format($totalRevenue / max(1, $start->diffInDays($end) + 1), 0));
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Average Expense per Day
                $sheet->setCellValue("A{$currentRow}", 'Rata-rata Pengeluaran per Hari');
                $sheet->setCellValue("B{$currentRow}", number_format($totalExpense / max(1, $start->diffInDays($end) + 1), 0));
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
                
                // Duration
                $sheet->setCellValue("A{$currentRow}", 'Jumlah Hari dalam Periode');
                $sheet->setCellValue("B{$currentRow}", ($start->diffInDays($end) + 1) . ' hari');
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                
                $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'd1d5db']]],
                ]);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Number formatting
                $sheet->getStyle("C:E")->getNumberFormat()->setFormatCode('#,##0');
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
            'A' => 35,
            'B' => 20,
            'C' => 20,
            'D' => 15,
            'E' => 20,
        ];
    }
    
    public function title(): string
    {
        return 'SUMMARY';
    }
}
