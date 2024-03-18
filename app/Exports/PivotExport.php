<?php

namespace App\Exports;

use App\Models\Pivot;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable; 
use PhpOffice\PhpSpreadsheet\Style\Style; 
use Maatwebsite\Excel\Concerns\WithTitle;



class PivotExport implements FromCollection, WithTitle , WithHeadings, WithMapping, WithEvents, WithMultipleSheets
{
    private $serialNumber = 0;
    public function title(): string
    {
        return 'Worksheet';
    }
    public function headings(): array
    {
        return [
            'Seller Name',
            'Product Category',
            'Product Name',
            'Product Description', 
            'Product Price', 
        ];
    }

    public function map($record): array
    {
        $this->serialNumber++;

        return [
            $record->seller_name,
            $record->product_category,
            $record->product_name,
            $record->product_description, 
            '$' . $record->product_price, 
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Pivot::all();
    }
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new class($this->serialNumber) extends PivotExport
        {
            public function title(): string
            {
                return 'Product Summary';
            }
            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        $this->addGroups($event->sheet);
                    },
                ];
            }
            public function styles(Worksheet $sheet)
            {

                $sheet->getColumnDimension('A')->setWidth(70);
                $sheet->getColumnDimension('b')->setWidth(30);
       
                return $sheet;
            }

            public function addGroups(Sheet $sheet)
            {
                $style = [
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DCE6F1'],
                    ],
                ];
                $lastColumn = $sheet->getHighestColumn();
                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'], 
                    ],
                ]);
                $highestRow = $sheet->getHighestRow();
                $sheet->removeRow(2, $highestRow);
                foreach ($sheet->getRowIterator() as $row) {
                    $cellValue = $sheet->getCellByColumnAndRow(1, $row->getRowIndex())->getValue();
                    if (empty($cellValue)) {
                        $sheet->removeRow($row->getRowIndex());
                    }
                }
                $uniqueSellers =  $this->collection()->unique('seller_name')->pluck('seller_name')->toArray();
            //    dd($uniqueSellers);
                $rowIndex = 1;
                $sheet->setCellValue("A{$rowIndex}", 'seller_name');
                $sheet->getStyle("A{$rowIndex}:h{$rowIndex}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '366092'],
                    ],
                ]);
                $columnsToHide = [1,2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19];
                foreach ($sheet->getRowIterator() as $row) {
                    foreach ($columnsToHide as $column) {
                        $sheet->getCellByColumnAndRow($column, $row->getRowIndex())->setValue(null);
                    }
                }
                $sheet->garbageCollect();
                $newHeadings = ['Seller Name', 'Product Price'];
                foreach ($newHeadings as $index => $heading) {
                    $column = $index + 1;
                    $sheet->setCellValueByColumnAndRow($column, 1, $heading);
                }
                $firstRun = true;
                $sellerCount = 0;
                foreach ($uniqueSellers as $seller) {
                    $sellerCount++;
                    try {
                        $displayedSellers = [];
                        if ($firstRun && in_array($seller, $displayedSellers)) {
                            continue;
                        }
                        $sellerRows = $this->collection()->filter(function ($row) use ($seller) {
                            return $row->seller_name === $seller;
                        });
                        if ($sellerRows->isEmpty()) {
                            continue;
                        }
                        $groupedData = $sellerRows->groupBy(['product_category','product_name']);
                       
                        if ($sellerRows->isNotEmpty()) {
                            $startRow = $rowIndex + 1;
                            $sheet->setCellValue("A{$startRow}", $seller);
                            $sheet->getStyle("A{$startRow}:h{$startRow}")->applyFromArray([
                                'font' => ['bold' => true],
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => '95B3D7'],
                                ],
                            ]);
                            for ($i = 2; $i <= 6; $i++) {
                                $sheet->setCellValueByColumnAndRow($i, $startRow, '');
                            }
                            $totalProductPriceSum = $sellerRows->sum('product_price');
                            $sheet->setCellValueByColumnAndRow(2, $startRow, $totalProductPriceSum);
                            $row = $startRow + 1;
                            foreach ($groupedData as $product_category => $product_category_name) {
                                if ($sellerRows->isEmpty()) {
                                    continue;
                                }
                                $displayedCategoriesa = [];
                                if (!in_array($product_category, $displayedCategoriesa)) {
                                    $sheet->setCellValue("A{$row}", ' ' . ' ' . $product_category . ' ');
                                    $displayedCategoriesa[] = $product_category;
                                    $sheet->getStyle("A{$row}:h{$row}")->applyFromArray($style);
                                    $row++;
                                }
                              
                                $retailerCount = 0;
                                foreach ($product_category_name as $productName) {
                                    $retailerCount++;
                                    if ($sellerRows->isEmpty()) {
                                        continue;
                                    }
                                    $productName = $productName[0];
                                    $displayedCategoriesa = [];
                                    if (!in_array($productName['product_name'], $displayedCategoriesa)) {
                                        $sheet->setCellValue("A{$row}", ' ' . ' ' . ' ' . ' ' . ' ' . ' ' . ' ' . ' ' . $productName['product_name']);
                                        $displayedCategoriesa[] = $productName['product_name'];
                                        $row++;
                                    }
                                    $retailerOpeningSum = $sellerRows
                                        ->where('product_category', $product_category)
                                        ->where('product_name', $productName['product_name'])
                                        ->sum('product_price');

                                    $sheet->setCellValueByColumnAndRow(2, $row - 1, $retailerOpeningSum);
                                }
                                $rowIndex++;
                            }
                            $firstRun = false;
                        }
                        $rowIndex++;
                        $displayedSellers[] = $seller;
                    } catch (\Throwable $e) {
                        Log::error('Caught exception: ' . $e->getMessage());

                        $defaultValue = 'Default Value';
                        $sheet->setCellValueByColumnAndRow(1, $row, $defaultValue);
                    }
                }
               
                if (is_int($row)) {
                    $lastRowIndex = $row;
                } else {
                    $lastRowIndex = $row->getRowIndex();
                }
                
                $sheet->removeRow($lastRowIndex + 1, $sheet->getHighestRow() - $lastRowIndex);
            }
        };
        $sheets[] = new  class($this->serialNumber) extends PivotExport
        {
           
        };            
        return $sheets;


    }
    
    public function registerEvents(): array
    {
        return [];
    }

    public function addGroups(Sheet $sheet)
    {       
    }
}
