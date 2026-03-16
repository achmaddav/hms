<?php

namespace App\Exports;

use App\Models\CheckIn;
use App\Models\UtilityPayment;
use App\Models\SalaryPayment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FinancialSummarySheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
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
    
    public function array(): array
    {
        $start = \Carbon\Carbon::parse($this->startDate);
        $end = \Carbon\Carbon::parse($this->endDate);
        
        // Get hotel name
        $hotel = \App\Models\Hotel::find($this->hotelId);
        $hotelName = $hotel ? $hotel->name : 'Hotel';
        
        $data = [];
        
        // Header
        $data[] = ['LAPORAN LABA RUGI (PROFIT & LOSS STATEMENT)'];
        $data[] = ['Periode: ' . $start->format('d F Y') . ' - ' . $end->format('d F Y')];
        $data[] = ['Hotel: ' . $hotelName];
        $data[] = ['']; // Empty row
        
        // ========== CALCULATE TOTALS ==========
        
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
        
        // Electricity
        $electricityExpense = UtilityPayment::where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('utility_type', 'electricity')
            ->sum('total_amount');
        
        $electricityCount = UtilityPayment::where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('utility_type', 'electricity')
            ->count();
        
        // Water
        $waterExpense = UtilityPayment::where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('utility_type', 'water')
            ->sum('total_amount');
        
        $waterCount = UtilityPayment::where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('utility_type', 'water')
            ->count();
        
        // Gas
        $gasExpense = UtilityPayment::where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('utility_type', 'gas')
            ->sum('total_amount');
        
        $gasCount = UtilityPayment::where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('utility_type', 'gas')
            ->count();
        
        // Internet
        $internetExpense = UtilityPayment::where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('utility_type', 'internet')
            ->sum('total_amount');
        
        $internetCount = UtilityPayment::where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('utility_type', 'internet')
            ->count();
        
        // Other Utilities
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
        
        // ========== BUILD REPORT ==========
        
        // REVENUE SECTION
        $data[] = ['PEMASUKAN (REVENUE)'];
        $data[] = [''];
        $data[] = ['Kategori', 'Jumlah Transaksi', 'Total (Rp)', 'Persentase'];
        $data[] = [
            'Pemasukan Kamar',
            $roomCount,
            $roomRevenue,
            $totalRevenue > 0 ? number_format(($roomRevenue / $totalRevenue) * 100, 2) . '%' : '0%'
        ];
        $data[] = [
            'Layanan Tambahan',
            $servicesCount,
            $additionalServicesRevenue,
            $totalRevenue > 0 ? number_format(($additionalServicesRevenue / $totalRevenue) * 100, 2) . '%' : '0%'
        ];
        $data[] = [''];
        $data[] = ['TOTAL PEMASUKAN', '', $totalRevenue, '100%'];
        $data[] = [''];
        $data[] = [''];
        
        // EXPENSE SECTION
        $data[] = ['PENGELUARAN (EXPENSE)'];
        $data[] = [''];
        $data[] = ['Kategori', 'Jumlah Transaksi', 'Total (Rp)', 'Persentase'];
        
        // Utilities
        $data[] = ['Utilitas:'];
        $data[] = [
            '  - Listrik',
            $electricityCount,
            $electricityExpense,
            $totalExpense > 0 ? number_format(($electricityExpense / $totalExpense) * 100, 2) . '%' : '0%'
        ];
        $data[] = [
            '  - Air',
            $waterCount,
            $waterExpense,
            $totalExpense > 0 ? number_format(($waterExpense / $totalExpense) * 100, 2) . '%' : '0%'
        ];
        $data[] = [
            '  - Gas',
            $gasCount,
            $gasExpense,
            $totalExpense > 0 ? number_format(($gasExpense / $totalExpense) * 100, 2) . '%' : '0%'
        ];
        $data[] = [
            '  - Internet',
            $internetCount,
            $internetExpense,
            $totalExpense > 0 ? number_format(($internetExpense / $totalExpense) * 100, 2) . '%' : '0%'
        ];
        $data[] = [
            '  - Lainnya',
            $otherUtilitiesCount,
            $otherUtilitiesExpense,
            $totalExpense > 0 ? number_format(($otherUtilitiesExpense / $totalExpense) * 100, 2) . '%' : '0%'
        ];
        $data[] = [
            'Subtotal Utilitas',
            $electricityCount + $waterCount + $gasCount + $internetCount + $otherUtilitiesCount,
            $totalUtilityExpense,
            $totalExpense > 0 ? number_format(($totalUtilityExpense / $totalExpense) * 100, 2) . '%' : '0%'
        ];
        $data[] = [''];
        
        // Salary
        $data[] = [
            'Gaji Karyawan',
            $salaryCount,
            $salaryExpense,
            $totalExpense > 0 ? number_format(($salaryExpense / $totalExpense) * 100, 2) . '%' : '0%'
        ];
        $data[] = [''];
        $data[] = ['TOTAL PENGELUARAN', '', $totalExpense, '100%'];
        $data[] = [''];
        $data[] = [''];
        
        // PROFIT/LOSS SECTION
        $data[] = ['LABA/RUGI (PROFIT/LOSS)'];
        $data[] = [''];
        $data[] = ['Total Pemasukan', '', $totalRevenue];
        $data[] = ['Total Pengeluaran', '', $totalExpense];
        $data[] = [''];
        $data[] = [$netProfit >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH', '', $netProfit];
        $data[] = [''];
        
        // Additional Metrics
        $data[] = ['METRIK TAMBAHAN'];
        $data[] = [''];
        $data[] = ['Profit Margin', '', $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 2) . '%' : '0%'];
        $data[] = ['Rata-rata Pemasukan per Hari', '', number_format($totalRevenue / max(1, $start->diffInDays($end) + 1), 2)];
        $data[] = ['Rata-rata Pengeluaran per Hari', '', number_format($totalExpense / max(1, $start->diffInDays($end) + 1), 2)];
        $data[] = ['Jumlah Hari dalam Periode', '', $start->diffInDays($end) + 1];
        
        return $data;
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Main title
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'c4a962'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            
            // Section headers
            5 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '10b981']]],
            13 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'ef4444']]],
            30 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'c4a962']]],
            
            // Totals
            11 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd1fae5']]],
            28 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']]],
            35 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'c4a962']]],
        ];
    }
    
    public function title(): string
    {
        return 'SUMMARY';
    }
}
