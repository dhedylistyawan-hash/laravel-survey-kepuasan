@extends('layouts.app')

@push('head')
<script>
// Ensure functions are available immediately
window.createBackup = function() {
    console.log('createBackup called');
    showLoading('Membuat backup database...');
    
    $.ajax({
        url: '{{ route("admin.backup.create") }}',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 3000
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            hideLoading();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat membuat backup'
            });
        }
    });
};

window.downloadBackup = function(filename) {
    window.open('{{ route("admin.backup.download", ":filename") }}'.replace(':filename', filename), '_blank');
};

window.deleteBackup = function(filename) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Yakin ingin menghapus backup "${filename}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading('Menghapus backup...');
            
            $.ajax({
                url: '{{ route("admin.backup.delete", ":filename") }}'.replace(':filename', filename),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus backup'
                    });
                }
            });
        }
    });
};

window.cleanupBackups = function() {
    Swal.fire({
        title: 'Konfirmasi Cleanup',
        text: 'Yakin ingin menghapus semua backup lama (>7 hari)?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f39c12',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading('Membersihkan backup lama...');
            
            $.ajax({
                url: '{{ route("admin.backup.cleanup") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 3000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat cleanup'
                    });
                }
            });
        }
    });
};

window.refreshBackups = function() {
    location.reload();
};

window.showLoading = function(message) {
    document.getElementById('loading-message').textContent = message;
    document.getElementById('loadingModal').classList.remove('hidden');
};

window.hideLoading = function() {
    document.getElementById('loadingModal').classList.add('hidden');
};
</script>
@endpush

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">🛡️ Backup Database</h1>
                <p class="text-gray-600 dark:text-gray-400">Kelola backup database survey kepuasan</p>
            </div>
            <div class="flex gap-2">
                <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2" onclick="createBackup()">
                    <i class="fas fa-plus"></i> Buat Backup
                </button>
                <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center gap-2" onclick="cleanupBackups()">
                    <i class="fas fa-trash"></i> Hapus Backup Lama
                </button>
                <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2" onclick="refreshBackups()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-database text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Backup</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" id="total-backups">{{ count($backups) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-hdd text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Ukuran</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" id="total-size">{{ $totalSize }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Backup Terbaru</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" id="latest-backup">
                                    {{ $backups->isNotEmpty() ? $backups->first()['created_at']->format('d/m/Y H:i') : 'Tidak ada' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Backup Lama (>7 hari)</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" id="old-backups">
                                    {{ $backups->where('is_old', true)->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Table -->
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    <i class="fas fa-list"></i> Daftar Backup Database
                </h3>
            </div>
            
            @if(count($backups) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Dibuat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ukuran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($backups as $index => $backup)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-file-archive text-blue-500"></i>
                                    {{ $backup['filename'] }}
                                    @if($backup['is_compressed'])
                                        <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">GZ</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $backup['created_at']->format('d/m/Y H:i:s') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $backup['size'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $backup['days_old'] }} hari
                                    @if($backup['days_old'] > 7)
                                        <i class="fas fa-exclamation-triangle text-yellow-500 ml-1"></i>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($backup['is_old'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Lama</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Baru</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button type="button" class="text-blue-600 hover:text-blue-900" 
                                                onclick="downloadBackup('{{ $backup['filename'] }}')"
                                                title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="text-red-600 hover:text-red-900" 
                                                onclick="deleteBackup('{{ $backup['filename'] }}')"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-database text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada backup database</h3>
                    <p class="text-gray-500 dark:text-gray-400">Klik tombol "Buat Backup" untuk membuat backup pertama</p>
                </div>
            @endif
        </div>

        <!-- Info Panel -->
        <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    <i class="fas fa-info-circle"></i> Informasi Backup
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">📋 Jadwal Backup Otomatis</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li><i class="fas fa-clock text-blue-500"></i> Backup Harian: 02:00 WIB</li>
                            <li><i class="fas fa-trash text-yellow-500"></i> Cleanup Mingguan: Minggu 03:00 WIB</li>
                            <li><i class="fas fa-calendar text-green-500"></i> Retensi: 7 hari</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">🔧 Command Manual</h4>
                        <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
                            <code class="text-sm text-gray-800 dark:text-gray-200">php artisan backup:database --compress</code><br>
                            <code class="text-sm text-gray-800 dark:text-gray-200">php artisan backup:status</code><br>
                            <code class="text-sm text-gray-800 dark:text-gray-200">php artisan backup:cleanup</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-blue-100">
                <i class="fas fa-spinner fa-spin text-blue-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Memproses...</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2" id="loading-message">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>
@endsection
