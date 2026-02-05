<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Hotel_invoice;
use Illuminate\Support\Facades\DB;

class RenumberInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:renumber {--dry-run} {--target=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renumber invoices to PREFIXYYYYMMDD###. Use --dry-run to preview. --target=all|invoices|hotel';

    public function handle()
    {
        $dry = $this->option('dry-run');
        $target = $this->option('target') ?? 'all';

        if (!in_array($target, ['all','invoices','hotel'])) {
            $this->error('Invalid --target. Use all, invoices or hotel');
            return 1;
        }

        if (($target === 'all' || $target === 'invoices')) {
            $this->info('Processing flight invoices (invoices table)...');
            $this->processInvoices();
        }

        if (($target === 'all' || $target === 'hotel')) {
            $this->info('Processing hotel invoices (hotel_invoices table)...');
            $this->processHotelInvoices();
        }

        $this->info('Done.');
        return 0;
    }

    protected function processInvoices()
    {
        $dry = $this->option('dry-run');

        $dates = Invoice::selectRaw('DATE(created_at) as dt')
                    ->groupBy('dt')
                    ->orderBy('dt')
                    ->pluck('dt');

        foreach ($dates as $dt) {
            $invoices = Invoice::whereDate('created_at', $dt)
                        ->orderBy('created_at')
                        ->get();

            $prefix = 'LGT';
            $datePart = date('Ymd', strtotime($dt));
            $seq = 1;

            foreach ($invoices as $inv) {
                $newNo = $prefix . $datePart . str_pad($seq, 3, '0', STR_PAD_LEFT);
                $this->line("[Invoice ID {$inv->id}] {$inv->invoiceno} -> {$newNo}");
                if (!$dry) {
                    $inv->invoiceno = $newNo;
                    $inv->save();
                }
                $seq++;
            }
        }
    }

    protected function processHotelInvoices()
    {
        $dry = $this->option('dry-run');

        $dates = Hotel_invoice::selectRaw('DATE(created_at) as dt')
                    ->groupBy('dt')
                    ->orderBy('dt')
                    ->pluck('dt');

        foreach ($dates as $dt) {
            $invoices = Hotel_invoice::whereDate('created_at', $dt)
                        ->orderBy('created_at')
                        ->get();

            $prefix = 'LGH';
            $datePart = date('Ymd', strtotime($dt));
            $seq = 1;

            foreach ($invoices as $inv) {
                $newNo = $prefix . $datePart . str_pad($seq, 3, '0', STR_PAD_LEFT);
                $this->line("[Hotel Invoice ID {$inv->id}] {$inv->invoiceno} -> {$newNo}");
                if (!$dry) {
                    $inv->invoiceno = $newNo;
                    $inv->save();
                }
                $seq++;
            }
        }
    }
}
