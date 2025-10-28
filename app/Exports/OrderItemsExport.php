<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderItemsExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles, WithColumnWidths ,WithEvents
{
    /**
     * @return Collection
     */
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function collection()
    {
        $items = new Collection();

        foreach ($this->order->items as $key => $item) {
            $items->push([
                __('') ,
                __('common.product') => Product::find($item->product_id)->name,
                __('common.quantity') => $item->quantity,
                __('common.price')  => $item->price,
                __('common.total') => $item->price * $item->quantity


            ]);
        }

        return $items;
    }

    public function headings(): array
    {
        return [
            __(' '),
            __('common.product'),
            __('common.quantity'),
            __('common.price'),
            __('common.total')
        ];
    }

    public function columnFormats(): array
    {
        return [

            'D' => NumberFormat::FORMAT_CURRENCY_EUR,
            'E' => NumberFormat::FORMAT_CURRENCY_EUR,

        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 4,
            
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . 1)->getFont()->setSize(13);
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);
      
        $sheet->getStyle(1)->getFont()->setBold(true);
       // $sheet->setAutoFilter('A1:' . $sheet->getHighestColumn() . '1');
        $sheet->getStyle('A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

  

     public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getDelegate()->getHighestRow();

                // Calculate the totals
                
                $total = 0; 

                 foreach ($this->order->items as $key => $item) {
                        $total += $item->price * $item->quantity;
                        
        }
      
                // Add custom data to the last row
                $event->sheet->append([
                    '',
                    '',
                    '',
                    __('common.total'),
                Number::currency( $total , in:'EUR')
                    
                ]);

                

                $event->sheet->getStyle('B' . ($lastRow + 1) . ':F' . ($lastRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                $event->sheet->getStyle('E' . ($lastRow + 1) . ':E' . ($lastRow + 2))->applyFromArray([
                    'font' => ['italic' => true],
                    
                ]);
            },
        ];
    }
}
