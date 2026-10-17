<?php

namespace App\Services;

use App\Models\Airlines;
use App\Models\AirlineTopup;
use App\Models\Airport;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Hotel_invoice;
use App\Models\Hotel_rate;
use App\Models\Hotel_voucher;
use App\Models\Hotel_voucher_guest;
use App\Models\Hotel_voucher_room;
use App\Models\Invoice;
use App\Models\Invoice_detail;
use App\Models\Passenger;
use App\Models\RecycleBinEntry;
use App\Models\Room;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecycleBinService
{
    private $models = [
        'Ticket' => Ticket::class,
        'Invoice' => Invoice::class,
        'Invoice_detail' => Invoice_detail::class,
        'Customer' => Customer::class,
        'Passenger' => Passenger::class,
        'Hotel' => Hotel::class,
        'Hotel_rate' => Hotel_rate::class,
        'Room' => Room::class,
        'Hotel_invoice' => Hotel_invoice::class,
        'Hotel_voucher' => Hotel_voucher::class,
        'Hotel_voucher_room' => Hotel_voucher_room::class,
        'Hotel_voucher_guest' => Hotel_voucher_guest::class,
        'Airlines' => Airlines::class,
        'Airport' => Airport::class,
        'AirlineTopup' => AirlineTopup::class,
    ];

    public function archive(Model $record, string $label, ?string $description = null, bool $cascadeRelated = true): RecycleBinEntry
    {
        return DB::transaction(function () use ($record, $label, $description, $cascadeRelated) {
            $related = [];
            if ($cascadeRelated) {
                $this->softDeleteRelated($record, $related);
            }
            $record->delete();

            if ($record instanceof Ticket) {
                $invoice = Invoice::withTrashed()->find($record->invoice_id);
                if ($invoice && !$invoice->trashed() && !$invoice->tickets()->exists()) {
                    $invoice->delete();
                    $this->remember($related, $invoice);
                }
            }

            $user = auth()->user();
            return RecycleBinEntry::create([
                'resource_type' => class_basename($record),
                'record_id' => $record->getKey(),
                'label' => $label,
                'description' => $description,
                'related_records' => $related,
                'deleted_by' => $user ? $user->id : null,
                'deleted_by_name' => $user ? $user->name : null,
                'deleted_by_email' => $user ? $user->email : null,
            ]);
        });
    }

    public function restore(RecycleBinEntry $entry): void
    {
        DB::transaction(function () use ($entry) {
            $modelClass = $this->modelClass($entry->resource_type);
            $model = $modelClass::withTrashed()->findOrFail($entry->record_id);
            $model->restore();

            foreach (array_reverse($this->relatedRecords($entry), true) as $type => $ids) {
                $relatedClass = $this->modelClass($type);
                foreach ($relatedClass::withTrashed()->whereIn((new $relatedClass())->getKeyName(), $ids)->get() as $related) {
                    $related->restore();
                }
            }

            $entry->delete();
        });
    }

    public function permanentlyDelete(RecycleBinEntry $entry): void
    {
        DB::transaction(function () use ($entry) {
            $deleteAfterRoot = [];
            foreach ($this->relatedRecords($entry) as $type => $ids) {
                if ($entry->resource_type === 'Ticket' && $type === 'Invoice') {
                    $deleteAfterRoot = array_merge($deleteAfterRoot, $ids);
                    continue;
                }

                $relatedClass = $this->modelClass($type);
                foreach ($relatedClass::withTrashed()->whereIn((new $relatedClass())->getKeyName(), $ids)->get() as $related) {
                    $related->forceDelete();
                }
            }

            $modelClass = $this->modelClass($entry->resource_type);
            $modelClass::withTrashed()->findOrFail($entry->record_id)->forceDelete();

            if ($deleteAfterRoot) {
                $invoiceClass = $this->modelClass('Invoice');
                foreach ($invoiceClass::withTrashed()->whereIn('id', $deleteAfterRoot)->get() as $invoice) {
                    $invoice->forceDelete();
                }
            }

            $entry->delete();
        });
    }

    public function empty(): void
    {
        DB::transaction(function () {
            RecycleBinEntry::orderBy('id')->chunkById(100, function ($entries) {
                foreach ($entries as $entry) {
                    $this->permanentlyDelete($entry);
                }
            });
        });
    }

    public function modelClass(string $type): string
    {
        if (!isset($this->models[$type])) {
            throw new RuntimeException('Jenis data Sampah tidak dikenal.');
        }

        return $this->models[$type];
    }

    public function types(): array
    {
        return array_keys($this->models);
    }

    public function typeLabels(): array
    {
        return [
            'Ticket' => 'Tiket',
            'Invoice' => 'Invoice tiket',
            'Invoice_detail' => 'Detail invoice',
            'Customer' => 'Customer',
            'Passenger' => 'Penumpang',
            'Hotel' => 'Hotel',
            'Hotel_rate' => 'Tipe kamar hotel',
            'Room' => 'Kategori kamar',
            'Hotel_invoice' => 'Invoice hotel',
            'Hotel_voucher' => 'Voucher hotel',
            'Hotel_voucher_room' => 'Kamar voucher',
            'Hotel_voucher_guest' => 'Tamu voucher',
            'Airlines' => 'Maskapai',
            'Airport' => 'Bandara',
            'AirlineTopup' => 'Top up maskapai',
        ];
    }

    private function softDeleteRelated(Model $record, array &$related): void
    {
        if ($record instanceof Customer) {
            foreach (Invoice::where('customer_id', $record->getKey())->get() as $invoice) {
                $this->softDeleteRelated($invoice, $related);
                $invoice->delete();
                $this->remember($related, $invoice);
            }

            foreach (Hotel_invoice::where('customer_id', $record->getKey())->get() as $invoice) {
                $this->softDeleteRelated($invoice, $related);
                $invoice->delete();
                $this->remember($related, $invoice);
            }
            return;
        }

        if ($record instanceof Airlines) {
            foreach (Invoice_detail::where('airline_id', $record->getKey())->get() as $detail) {
                $detail->delete();
                $this->remember($related, $detail);
            }
            return;
        }

        if ($record instanceof Ticket) {
            foreach (Invoice_detail::where('ticket_id', $record->getKey())->get() as $detail) {
                $detail->delete();
                $this->remember($related, $detail);
            }
            return;
        }

        if ($record instanceof Invoice) {
            foreach (Ticket::where('invoice_id', $record->getKey())->get() as $ticket) {
                $this->softDeleteRelated($ticket, $related);
                $ticket->delete();
                $this->remember($related, $ticket);
            }
            foreach (Invoice_detail::where('invoice_id', $record->getKey())->get() as $detail) {
                if (!$detail->trashed()) {
                    $detail->delete();
                }
                $this->remember($related, $detail);
            }
            return;
        }

        if ($record instanceof Hotel_invoice) {
            $voucherIds = Hotel_voucher::where('booking_id', $record->getKey())->pluck('id')->all();
            foreach ($voucherIds as $voucherId) {
                $voucher = Hotel_voucher::find($voucherId);
                if ($voucher) {
                    $this->softDeleteRelated($voucher, $related);
                    $voucher->delete();
                    $this->remember($related, $voucher);
                }
            }
            return;
        }

        if ($record instanceof Hotel_voucher) {
            foreach (Hotel_voucher_room::where('hotel_voucher_id', $record->getKey())->get() as $room) {
                $this->softDeleteRelated($room, $related);
                $room->delete();
                $this->remember($related, $room);
            }
            return;
        }

        if ($record instanceof Hotel_voucher_room) {
            foreach (Hotel_voucher_guest::where('hotel_voucher_room_id', $record->getKey())->get() as $guest) {
                $guest->delete();
                $this->remember($related, $guest);
            }
        }
    }

    private function remember(array &$related, Model $record): void
    {
        $type = class_basename($record);
        $related[$type] = array_values(array_unique(array_merge($related[$type] ?? [], [$record->getKey()])));
    }

    private function relatedRecords(RecycleBinEntry $entry): array
    {
        $related = $entry->related_records;
        if (!is_array($related)) {
            return [];
        }

        return $related;
    }
}
