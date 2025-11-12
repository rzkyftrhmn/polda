@once
    <!-- Global SweetAlert flash message handler -->
    <script>
        jQuery(function($){
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}'
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}'
                });
            @endif
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: '{{ $errors->first() }}'
                });
            @endif
        });
        // Catatan: Flash di atas akan jalan di semua halaman yang meng-include komponen ini,
        // termasuk halaman create & edit user.
    </script>
@endonce
