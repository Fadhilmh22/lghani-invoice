// Pastikan skrip berjalan setelah DOM dan jQuery dimuat sepenuhnya
$(document).ready(function () {
    
    let targetFormId = ''; // Variabel untuk menyimpan ID form yang akan dihapus

    // --- LOGIKA MODAL HAPUS INVOICE KUSTOM ---
    $('.delete-button').on('click', function(e) {
        e.preventDefault();
        const invoiceId = $(this).data('invoice-id');
        const invoiceNo = $(this).data('invoice-no');
        
        // Set ID form target
        targetFormId = '#delete-form-' + invoiceId;

        // Update teks modal dengan nomor invoice yang relevan
        $('#invoiceNoPlaceholder').text(invoiceNo);

        // Tampilkan modal
        $('#deleteConfirmationModal').fadeIn(200);
    });

    // Handler untuk tombol Batal
    $('#cancelDeleteBtn').on('click', function() {
        $('#deleteConfirmationModal').fadeOut(200);
        targetFormId = ''; // Reset target
    });

    // Handler untuk tombol Konfirmasi Hapus
    $('#confirmDeleteBtn').on('click', function() {
        if (targetFormId) {
            // Sembunyikan modal
            $('#deleteConfirmationModal').fadeOut(200, function() {
                // Kirim form (ini akan memicu redirect dan session flash success)
                $(targetFormId).submit();
            });
        }
    });


    // --- LOGIKA ANIMASI POP-UP SUKSES ---
    // Cek jika ada session success dari Laravel setelah page load (misalnya setelah DELETE)
    const successAlert = $('.alert-success[data-message]');
    if (successAlert.length) {
        const message = successAlert.data('message');
        
        // Update teks pesan di toast
        $('#successMessageText').html(message);
        
        // Tampilkan toast
        const toast = $('#successToast');
        toast.fadeIn(300).css('display', 'flex');

        // Sembunyikan toast setelah 3 detik
        setTimeout(function() {
            toast.fadeOut(500);
        }, 3000);
    }

    // --- LOGIKA TOGGLE STATUS PEMBAYARAN ---
    $('.toggleStatus').on('click', function(e) {
        e.preventDefault(); // Mencegah form submit bawaan

        var $toggleStatusButton = $(this);
        var form = $toggleStatusButton.closest('form');

        // 1. Dapatkan data saat ini secara dinamis
        var invoiceId = $toggleStatusButton.data('invoice-id');
        var currentPage = form.find('[name="current_page"]').val();
        
        // 2. Ekstrak status pembayaran dengan lebih aman
        var currentStatusText = $toggleStatusButton.text().trim();
        var isLunas = currentStatusText.includes('Sudah Lunas');
        var newStatus = isLunas ? "Belum Lunas" : "Sudah Lunas";
        
        // 3. Gunakan AJAX untuk mengubah status
        $.ajax({
            url: '/invoice/ubah-status/' + invoiceId, 
            method: 'POST',
            data: {
                status: newStatus,
                _token: form.find('input[name="_token"]').val() 
            },
            success: function (response) {
                // Redirect kembali ke halaman saat ini setelah update
                // Ini juga akan memicu reload dan menampilkan session('success') jika ada di controller ubah-status
                window.location.href = '/invoice?page=' + currentPage;
            },
            error: function (xhr, status, error) {
                // Tampilkan pesan error di atas halaman
                const errorMessage = xhr.responseJSON ? (xhr.responseJSON.message || 'Error tidak diketahui.') : 'Gagal terhubung ke server.';
                
                // Pastikan hanya satu alert error yang muncul
                $('.alert-error-custom').remove(); 
                
                $('.page-title').after('<div class="alert alert-danger alert-error-custom">Terjadi kesalahan saat mengubah status: ' + errorMessage + '</div>');
                
                // Sembunyikan alert setelah 5 detik
                setTimeout(function(){
                    $('.alert-error-custom').slideUp();
                }, 5000);
            }
        });
    });
});



