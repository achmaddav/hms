<?php

namespace App\Exports;

use App\Models\CheckIn;
use App\Models\Payment\UtilityPayment;
use App\Models\Payment\SalaryPayment;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class FinancialSummaryExport implements WithMultipleSheets
{
    use Exportable;
    
    protected $hotelId;
    protected $startDate;
    protected $endDate;
    
    public function __construct($hotelId, $startDate, $endDate)
    {
        $this->hotelId = $hotelId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    
    /**
     * Generate sheets - one per date + summary sheet
     */
    public function sheets(): array
    {
        $sheets = [];
        
        // Create date range
        $start = \Carbon\Carbon::parse($this->startDate);
        $end = \Carbon\Carbon::parse($this->endDate);
        
        // Generate sheet for each date
        $currentDate = $start->copy();
        while ($currentDate->lte($end)) {
            $sheets[] = new DailyFinancialSheet(
                $this->hotelId,
                $currentDate->format('Y-m-d')
            );
            $currentDate->addDay();
        }
        
        // Add summary sheet at the end
        $sheets[] = new FinancialSummarySheet(
            $this->hotelId,
            $this->startDate,
            $this->endDate
        );
        
        return $sheets;
    }
}
