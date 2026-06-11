# TODO - Pagination Rows Per Page (10/20/50/100)

## Step 1 (Done/Next)
- Implement dynamic `per_page` pagination for all list controllers:
  - TicketController
  - AirlinesController
  - AirportController
  - CustomerController
  - InvoiceController
  - PassengerController
  - TopUpController
  - HotelController
  - RoomController
  - HotelVoucherController
  - HotelInvoiceController

## Step 2
- Add UI selector (10/20/50/100) di halaman list dengan tema yang sudah ada.
  - Update masing-masing Blade index:
    - resources/views/ticket/index.blade.php
    - resources/views/airlines/index.blade.php
    - resources/views/airports/index.blade.php
    - resources/views/customer/index.blade.php
    - resources/views/invoice/index.blade.php
    - resources/views/passenger/index.blade.php
    - resources/views/topup/index.blade.php
    - resources/views/hotel/index.blade.php
    - resources/views/room/index.blade.php
    - resources/views/hotelvoucher/index.blade.php
    - resources/views/hotelinvoice/index.blade.php

## Step 3
- Pastikan parameter `per_page` ikut terbawa saat pindah page dan saat search/filter.

## Step 4
- Jalankan aplikasi dan test:
  - Ubah per_page 10/20/50/100
  - Pastikan pagination & search/filter tidak rusak

