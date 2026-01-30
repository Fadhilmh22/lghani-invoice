// JavaScript untuk halaman Report Hotel
function reportTypeChange() {
    var value = document.getElementById("report_type").value;

    if( value == 1 ) {
        document.getElementById("report_month").style.display = 'block';
        document.getElementById("report_month").setAttribute('required', 'required');

        document.getElementById("report_start_date").style.display = 'none';
        document.getElementById("report_start_date").removeAttribute('required');
        document.getElementById("report_end_date").style.display = 'none';
        document.getElementById("report_end_date").removeAttribute('required');
    } else if( value == 2 ) {
        document.getElementById("report_month").style.display = 'none';
        document.getElementById("report_month").removeAttribute('required');

        document.getElementById("report_start_date").style.display = 'block';
        document.getElementById("report_start_date").setAttribute('required', 'required');
        document.getElementById("report_end_date").style.display = 'block';
        document.getElementById("report_end_date").setAttribute('required', 'required');
    } else {
        document.getElementById("report_month").style.display = 'none';
        document.getElementById("report_month").removeAttribute('required');
        document.getElementById("report_start_date").style.display = 'none';
        document.getElementById("report_start_date").removeAttribute('required');
        document.getElementById("report_end_date").style.display = 'none';
        document.getElementById("report_end_date").removeAttribute('required');
    }
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    var reportTypeSelect = document.getElementById("report_type");
    if (reportTypeSelect) {
        reportTypeSelect.onchange = function() {
            reportTypeChange();
        };
        reportTypeChange();
    }
});









