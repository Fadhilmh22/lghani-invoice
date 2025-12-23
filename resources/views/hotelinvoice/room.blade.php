@extends('master')

@section('konten')
<style type="text/css">
    .mb-0 {
        margin-bottom: 0;
    }
</style>
    <div class="container">
        <div class="row">
            <div class="col-md-16">
                <div class="card">
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-16">
                                <form id="form-detail" action="{{ route('hotelvoucher.updateRoom', ['id' => $voucher->id]) }}" method="post">
                                    @csrf
                                    <input type="hidden" name="_method" value="PUT" class="form-control">
                                    <input type="hidden" name="hotel_voucher_room_id">
                                    <div class="row">
                                        <div class="col-md-4 form-group mb-0">
                                            <label>Room</label>
                                            <select id="room_id" name="room_id" class="form-control">
                                                <option value="">Select Room</option>
                                                @foreach ($rooms as $room)
                                                    <option value="{{ $room->id }}" @if ( $roomselected == $room->id ) selected="selected" @endif>{{ $room->room_code }} - {{ $room->room_type }} - {{ $room->room_name }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-danger">{{ $errors->first('room_id') }}</p>
                                        </div>
                                        <div class="col-md-2 form-group mb-0">
                                            <label for="">Room No</label>
                                            <input type="text" name="room_no" class="form-control {{ $errors->has('room_no') ? 'is-invalid':'' }}">
                                            <p class="text-danger">{{ $errors->first('room_no') }}</p>
                                        </div>
                                        <div class="col-md-2 form-group mb-0">
                                            <label>Meal Type</label>
                                            <input type="text" name="meal_type" class="form-control {{ $errors->has('meal_type') ? 'is-invalid':'' }}">
                                            <p class="text-danger">{{ $errors->first('meal_type') }}</p>
                                        </div>
                                        <div class="col-md-2 form-group mb-0">
                                            <label>Use Allotment</label>
                                            <select name="use_allotment" class="form-control">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group mb-0">
                                            <label for="">No Of Extra Bed</label>
                                            <input id="no_of_extrabed" name="no_of_extrabed" type="number" class="form-control" value="0" min="0" max="3">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">Room Price Detail</div>
                                                <div class="panel-body">
                                                    <table id="room-detail" class="table" style="width: 100%">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 10%;font-weight: 500;" class="detail-day">#</th>
                                                                <th style="width: 30%;font-weight: 500;" class="detail-day">Hari</th>
                                                                <th style="width: 20%;font-weight: 500;" class="detail-date">Tanggal</th>
                                                                <th style="width: 20%;font-weight: 500;" class="detail-price">Price</th>
                                                                <th style="width: 20%;font-weight: 500;" class="detail-nta">NTA</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                $checkIn = strtotime($voucher->check_in);
                                                                $days = (strtotime($voucher->check_out) - $checkIn) / 86400;

                                                                for ($i=0; $i < $days; $i++) :
                                                                    $currDay = $checkIn + (86400 * $i);
                                                                    $dayIndex = date("w", $currDay);
                                                                    $isWeekEnd = $dayIndex == 5 || $dayIndex == 6 ? true : false;
                                                            ?>
                                                                <tr>
                                                                    <td><?=$i + 1?></td>
                                                                    <td><?=$dayName[ $dayIndex ]?></td>
                                                                    <td><?=date("d-m-Y", $currDay)?></td>
                                                                    <td class="<?=$isWeekEnd ? 'td-weekend-price' : 'td-weekday-price'?>"></td>
                                                                    <td class="<?=$isWeekEnd ? 'td-weekend-nta' : 'td-weekday-nta'?>"></td>
                                                                </tr>
                                                            <?php endfor ?>
                                                        </tbody>
                                                    </table>
                                                    <a href="javascript:void(0)" id="btnUpdatePrice" class="btn btn-sm btn-warning" style="margin-top: 15px;" disabled=disabled>Update Room Price</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">Guest Detail</div>
                                                <div class="panel-body">
                                                    <table id="guest-detail" class="table" style="width: 100%">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Type</th>
                                                            <th>Gender</th>
                                                            <th>First Name</th>
                                                            <th>Last Name</th>
                                                            <th>Age</th>
                                                        </tr>
                                                        <tr class="guest-row" id="guest1">
                                                            <input type="hidden" name="hotel_voucher_guest_id[]" class="hotel_voucher_guest_id">
                                                            <td class="td-num">1</td>
                                                            <td>
                                                                <select name="guest_type[]" class="form-control guest_type" required>
                                                                    <option value="">Pilih</option>
                                                                    <option value="adult">Adult</option>
                                                                    <option value="children">Children</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input name="guest_gender[]" type="text" class="form-control guest_gender" readonly required>
                                                            </td>
                                                            <td>
                                                                <input name="guest_first_name[]" type="text" class="form-control guest_first_name" readonly required>
                                                            </td>
                                                            <td>
                                                                <input name="guest_last_name[]" type="text" class="form-control guest_last_name" readonly>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input name="guest_age[]" type="number" class="form-control guest_age" min="0" readonly>
                                                                    <div class="input-group-addon">tahun</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="guest-row" id="guest2">
                                                            <input type="hidden" name="hotel_voucher_guest_id[]" class="hotel_voucher_guest_id">
                                                            <td class="td-num">2</td>
                                                            <td>
                                                                <select name="guest_type[]" class="form-control guest_type">
                                                                    <option value="">Pilih</option>
                                                                    <option value="adult">Adult</option>
                                                                    <option value="children">Children</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input name="guest_gender[]" type="text" class="form-control guest_gender" readonly >
                                                            </td>
                                                            <td>
                                                                <input name="guest_first_name[]" type="text" class="form-control guest_first_name" readonly >
                                                            </td>
                                                            <td>
                                                                <input name="guest_last_name[]" type="text" class="form-control guest_last_name" readonly >
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input name="guest_age[]" type="number" class="form-control guest_age" min="0" readonly >
                                                                    <div class="input-group-addon">tahun</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="guest-row" id="guest3" style="display:none;">
                                                            <input type="hidden" name="hotel_voucher_guest_id[]" class="hotel_voucher_guest_id">
                                                            <td class="td-num">3</td>
                                                            <td>
                                                                <select name="guest_type[]" class="form-control guest_type">
                                                                    <option value="">Pilih</option>
                                                                    <option value="adult">Adult</option>
                                                                    <option value="children">Children</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input name="guest_gender[]" type="text" class="form-control guest_gender" readonly>
                                                            </td>
                                                            <td>
                                                                <input name="guest_first_name[]" type="text" class="form-control guest_first_name" readonly>
                                                            </td>
                                                            <td>
                                                                <input name="guest_last_name[]" type="text" class="form-control guest_last_name" readonly>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input name="guest_age[]" type="number" class="form-control guest_age" min="0" readonly>
                                                                    <div class="input-group-addon">tahun</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="guest-row" id="guest4" style="display:none;">
                                                            <input type="hidden" name="hotel_voucher_guest_id[]" class="hotel_voucher_guest_id">
                                                            <td class="td-num">4</td>
                                                            <td>
                                                                <select name="guest_type[]" class="form-control guest_type">
                                                                    <option value="">Pilih</option>
                                                                    <option value="adult">Adult</option>
                                                                    <option value="children">Children</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input name="guest_gender[]" type="text" class="form-control guest_gender" readonly>
                                                            </td>
                                                            <td>
                                                                <input name="guest_first_name[]" type="text" class="form-control guest_first_name" readonly>
                                                            </td>
                                                            <td>
                                                                <input name="guest_last_name[]" type="text" class="form-control guest_last_name" readonly>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input name="guest_age[]" type="number" class="form-control guest_age" min="0" readonly>
                                                                    <div class="input-group-addon">tahun</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="guest-row" id="guest5" style="display:none;">
                                                            <input type="hidden" name="hotel_voucher_guest_id[]" class="hotel_voucher_guest_id">
                                                            <td class="td-num">5</td>
                                                            <td>
                                                                <select name="guest_type[]" class="form-control guest_type">
                                                                    <option value="">Pilih</option>
                                                                    <option value="adult">Adult</option>
                                                                    <option value="children">Children</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input name="guest_gender[]" type="text" class="form-control guest_gender" readonly>
                                                            </td>
                                                            <td>
                                                                <input name="guest_first_name[]" type="text" class="form-control guest_first_name" readonly>
                                                            </td>
                                                            <td>
                                                                <input name="guest_last_name[]" type="text" class="form-control guest_last_name" readonly>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input name="guest_age[]" type="number" class="form-control guest_age" min="0" readonly>
                                                                    <div class="input-group-addon">tahun</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 form-group mb-0">
                                            <button class="btn btn-success btn-sm">Simpan</button>
                                            <button type="button" id="btnCancel" class="btn btn-warning btn-sm">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-16">
                                <div class="text-center">
                                    <img src="{{ asset('lghani.png') }}" alt="" width="350px" height="150px">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <table style="width: 100%">
                                    <tr>
                                        <td width="30%">Nama Booker</td>
                                        <td>:</td>
                                        <td>{{ $voucher->booker }}</td>
                                    </tr>
                                    <tr>
                                        <td>No Telp</td>
                                        <td>:</td>
                                        <td>{{ $voucher->nationality }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-16">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <td>#</td>
                                            <td>Room</td>
                                            <td>Room No</td>
                                            <td>Meal Type</td>
                                            <td>Use Allotment</td>
                                            <td>Guest</td>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                            $total = 0;
                                        @endphp
                                        @foreach ($voucherRoom as $hRoom)
                                            @php
                                                $adult = 0;
                                                $children = 0;
                                                $use_allotment = $hRoom['use_allotment'] == 1 ? "Yes" : "No";
                                            @endphp
                                            @foreach($hRoom['hotelguest'] as $hGuest)
                                                @php
                                                    if( $hGuest['guest_type'] == 'adult' ) {
                                                        $adult++;
                                                    } else if( $hGuest['guest_type'] == "children" ) {
                                                        $children++;
                                                    }
                                                @endphp
                                            @endforeach
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $rooms[ $hRoom['room_id'] ]->room_code }} - {{  $rooms[ $hRoom['room_id'] ]->room_type }} - {{  $rooms[ $hRoom['room_id'] ]->room_name }}</td>
                                            <td>{{ $hRoom['room_no'] }}</td>
                                            <td>{{ $hRoom['meal_type'] }}</td>
                                            <td>{{ $use_allotment }}</td>
                                            <td>{{ $adult }} adult, {{ $children }} children</td>
                                            <td>
                                                <div style="width: 90px;">
                                                    <button type="button" class="btn btn-info btn-xs pull-left btnEdit" data-id="{{ $hRoom['id'] }}" style="margin-right: 5px;">Edit</button>
                                                    <form action="{{ route('hotelvoucher.delete_product', ['id' => $hRoom['id']]) }}" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="_method" value="DELETE" class="form-control">
                                                        <button class="btn btn-danger btn-xs d-inline" onclick="return confirmDelete()">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4 offset-md-8">
                                <?php /*
                                <table class="table table-hover table-bordered">
                                <?php
                                    $total = 0; 
                                ?>
                               
                                    <tr>
                                        <td>Total</td>
                                        <td>:</td>
                                        <td>Rp {{ number_format($total) }}</td>
                                    </tr>
                                </table>
                                */ ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            function confirmDelete() {
                return confirm("Apakah Anda yakin ingin menghapus data ini?");
            }

            $('.btnEdit').click(function() {
                var id = $(this).attr('data-id');

                $.ajax({
                    url: "{{ route('hotelvoucher.room_detail') }}",
                    type: 'POST',
                    dataType: "json",
                    data: {id: id},
                    success: function(response){
                        if( response.result ) {
                            $('#form-detail [name=hotel_voucher_room_id]').val(response.data.id);
                            $('#room_id').val(response.data.room_id).trigger('change');
                            $('#form-detail [name=room_no]').val(response.data.room_no);
                            $('#form-detail [name=meal_type]').val(response.data.meal_type);
                            $('#form-detail [name=use_allotment]').val(response.data.use_allotment);
                            $('#form-detail [name=no_of_extrabed]').val(response.data.no_of_extrabed).trigger('change');

                            var i = 0;
                            $(response.data.hotelguest).each(function(i, val) {
                                i++;

                                $('#guest' + i).find('.hotel_voucher_guest_id').val(val.id);
                                $('#guest' + i).find('.guest_type').val(val.guest_type).trigger('change');
                                $('#guest' + i).find('.guest_gender').val(val.guest_gender);
                                $('#guest' + i).find('.guest_first_name').val(val.guest_first_name);
                                $('#guest' + i).find('.guest_last_name').val(val.guest_last_name);
                                $('#guest' + i).find('.guest_age').val(val.guest_age);
                            })

                            console.log(response);

                            document.body.scrollTop = 0;
                            document.documentElement.scrollTop = 0;
                        }
                    }
                });
            })

            $('#btnCancel').click(function() {
                $('#form-detail [name=hotel_voucher_room_id]').val('');
                $('#room_id').val('').trigger('change');
                $('#form-detail [name=room_no]').val('');
                $('#form-detail [name=meal_type]').val('');
                $('#form-detail [name=use_allotment]').val(0);
                $('#form-detail [name=no_of_extrabed]').val(0).trigger('change');

                $('#guest1').find('.hotel_voucher_guest_id').val('');
                $('#guest1').find('.guest_type').val('').trigger('change');
                $('#guest1').find('.guest_gender').val('');
                $('#guest1').find('.guest_first_name').val('');
                $('#guest1').find('.guest_last_name').val('');
                $('#guest1').find('.guest_age').val('');

                $('#guest2').find('.hotel_voucher_guest_id').val('');
                $('#guest2').find('.guest_type').val('').trigger('change');
                $('#guest2').find('.guest_gender').val('');
                $('#guest2').find('.guest_first_name').val('');
                $('#guest2').find('.guest_last_name').val('');
                $('#guest2').find('.guest_age').val('');
            })

            var check_in = new Date( "<?=$voucher->check_in?>" );
            var check_out = new Date( "<?=$voucher->check_out?>" );
            var daysDiff = Math.round((check_out.getTime() - check_in.getTime()) / (24 * 60 * 60 * 1000));
            var days = [];

            var check_in_date = check_in.getDate();
            for (var i = 0; i < daysDiff; i++) {
                var newDate = new Date()
                newDate.setDate(check_in_date + i);
                var day = newDate.getDay();
                var date = newDate.getDate();
                var month = newDate.getMonth();

                if( date < 10 ) {
                    date = '0' + date;
                }

                if( month < 10 ) {
                    month = '0' + month;
                }

                days.push({
                    day: day,
                    date: newDate.getFullYear() + '-' + month + '-' + date,
                    isWeekEnd: (day > 4 ? true : false)
                })
            }

            $('#room_id').change(function() {
                var id = $(this).val();
                if( id != '' ) {
                    $.ajax({
                        url: "{{ route('room.detail') }}",
                        type: 'POST',
                        dataType: "json",
                        data: {id: id},
                        success: function(response){
                            if( response.result ) {
                                $('.td-weekday-price').text('Rp ' + formatRupiah(response.data.weekday_price));
                                $('.td-weekend-price').text('Rp ' + formatRupiah(response.data.weekend_price));
                                $('.td-weekday-nta').text('Rp ' + formatRupiah(response.data.weekday_nta));
                                $('.td-weekend-nta').text('Rp ' + formatRupiah(response.data.weekend_nta));

                                $('#btnUpdatePrice').attr('href', "{{ url('/room') }}/" + response.data.id + '?redirect=hotel-voucher/1?roomselected=' + id);
                                $('#btnUpdatePrice').removeAttr('disabled');
                            }
                        }
                    });
                } else {
                    $('.td-weekday-price').text('');
                    $('.td-weekend-price').text('');
                    $('.td-weekday-nta').text('');
                    $('.td-weekend-nta').text('');
                    $('#btnUpdatePrice').attr('disabled', 'disabled');
                }
            })

            $('#no_of_extrabed').change(function() {
                var no = parseInt($(this).val()) + 2;

                for (var i = 3; i <= 5; i++) {
                    if( i > no ) {
                        $('#guest' + i).hide();
                        $('#guest' + i).find('.guest_type').removeAttr('required');
                        $('#guest' + i).find('.guest_gender').removeAttr('required');
                        $('#guest' + i).find('.guest_first_name').removeAttr('required');
                        $('#guest' + i).find('.guest_age').removeAttr('required');
                    } else {
                        $('#guest' + i).show();
                        $('#guest' + i).find('.guest_type').attr('required', 'required');
                        $('#guest' + i).find('.guest_gender').attr('required', 'required');
                        $('#guest' + i).find('.guest_first_name').attr('required', 'required');
                        $('#guest' + i).find('.guest_age');
                    }
                }

                if( no > 2 ) {
                    $('#guest2').find('.guest_type').attr('required', 'required');
                    $('#guest2').find('.guest_gender').attr('required', 'required');
                    $('#guest2').find('.guest_first_name').attr('required', 'required');
                    $('#guest2').find('.guest_age');
                } else {
                    if( $('#guest2').find('.guest_type').val() != "" ) {
                        $('#guest2').find('.guest_type').attr('required', 'required');
                        $('#guest2').find('.guest_gender').attr('required', 'required');
                        $('#guest2').find('.guest_first_name').attr('required', 'required');
                        $('#guest2').find('.guest_age');
                    } else {
                        $('#guest2').find('.guest_type').removeAttr('required');
                        $('#guest2').find('.guest_gender').removeAttr('required');
                        $('#guest2').find('.guest_first_name').removeAttr('required');
                        $('#guest2').find('.guest_age').removeAttr('required');
                    }
                }
            })

            $('.guest_type').change(function() {
                var parentId = $(this).parent().parent().attr('id');
                var type = $(this).val();
                if( type != "" ) {
                    $('#' + parentId).find('.guest_gender').attr('required', 'required').removeAttr('readonly');
                    $('#' + parentId).find('.guest_first_name').attr('required', 'required').removeAttr('readonly');
                    $('#' + parentId).find('.guest_last_name').removeAttr('readonly');

                    if( type == 'adult' ) {
                        $('#' + parentId).find('.guest_age').attr('readonly', 'readonly').removeAttr('required');
                    } else {
                        $('#' + parentId).find('.guest_age').attr('required', 'required').removeAttr('readonly');
                    }
                } else {
                    $('#' + parentId).find('.guest_gender').val('').attr('readonly', 'readonly');
                    $('#' + parentId).find('.guest_first_name').val('').attr('readonly', 'readonly');
                    $('#' + parentId).find('.guest_last_name').val('').attr('readonly', 'readonly');
                    $('#' + parentId).find('.guest_age').val('').attr('readonly', 'readonly');

                    if( parentId != 'guest1' && parentId != 'guest1' ) {
                        $('#' + parentId).find('.guest_gender').removeAttr('required');
                        $('#' + parentId).find('.guest_first_name').removeAttr('required');
                        $('#' + parentId).find('.guest_last_name');
                        $('#' + parentId).find('.guest_age').removeAttr('required');
                    }
                }
            })

            $('#room_id').trigger('change');
        })



    </script>
@endsection