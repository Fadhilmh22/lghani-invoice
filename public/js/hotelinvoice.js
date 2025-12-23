// JavaScript untuk Hotel Invoice
$(document).ready(function() {
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
            url: '/hotel-invoice/' + invoiceId + '/ubah-statusinv', 
            method: 'POST',
            data: {
                status: newStatus,
                _token: form.find('input[name="_token"]').val() 
            },
            success: function (response) {
                // Get search param from URL
                var urlParams = new URLSearchParams(window.location.search);
                var searchParam = urlParams.get('search') || '';
                
                // Redirect kembali ke halaman saat ini setelah update
                // Ini juga akan memicu reload dan menampilkan session('success') jika ada di controller ubah-status
                var url = '/hotel-invoice?page=' + currentPage;
                if (searchParam) {
                    url += '&search=' + encodeURIComponent(searchParam);
                }
                window.location.href = url;
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

