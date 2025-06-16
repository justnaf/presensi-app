import Swal from 'sweetalert2'

/**
 * Mendefinisikan "bentuk" dari objek yang dikembalikan oleh useSweetAlert.
 * Ini membuat kode lebih mudah dibaca dan memberikan autocompletion yang lebih baik.
 */
interface UseSweetAlertReturn {
  success: (message?: string) => void;
  error: (message?: string) => void;
  confirmDelete: (callback: () => void) => void;
}

/**
 * Composable untuk menampilkan notifikasi SweetAlert2 dengan mudah.
 */
export default function useSweetAlert(): UseSweetAlertReturn {
  /**
   * Menampilkan notifikasi sukses.
   * @param message Pesan yang akan ditampilkan.
   */
  const success = (message: string = 'Berhasil!') => {
    Swal.fire({
      icon: 'success',
      title: message,
      timer: 2000,
      showConfirmButton: false, // Toast sebaiknya tidak perlu konfirmasi
      toast: true,
      position: 'bottom-end',
    })
  }

  /**
   * Menampilkan notifikasi error.
   * @param message Pesan yang akan ditampilkan.
   */
  const error = (message: string = 'Terjadi kesalahan.') => {
    Swal.fire({
      icon: 'error',
      title: message,
      timer: 3000,
      showConfirmButton: false,
      toast: true,
      position: 'bottom-end',
    })
  }

  /**
   * Menampilkan dialog konfirmasi sebelum menjalankan sebuah aksi (misal: hapus data).
   * @param callback Fungsi yang akan dieksekusi jika pengguna menekan "Ya, hapus!".
   */
  const confirmDelete = (callback: () => void) => {
    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: 'Data yang dihapus tidak dapat dikembalikan!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e53e3e', // Warna merah untuk tombol hapus
      cancelButtonColor: '#718096', // Warna abu-abu untuk batal
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
    }).then(result => {
      if (result.isConfirmed) {
        callback()
      }
    })
  }

  return { success, error, confirmDelete }
}