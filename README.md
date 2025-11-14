# Project Keuangan


## Export fitur tambahan

- `api/export_csv.php?bulan_id=ID` -> men-download rekap bulanan dalam format CSV (bisa dibuka di Excel).
- `api/export_pdf.php?bulan_id=ID` -> mencoba membuat PDF menggunakan **Dompdf** (jika `vendor/autoload.php` tersedia).
  - Untuk mengaktifkan PDF otomatis, jalankan di root project:
    ```
    composer require dompdf/dompdf
    ```
    dan pastikan folder `vendor` ikut di ZIP atau tersedia di server.

Jika kamu mau, saya bisa juga memasukkan folder `vendor` (Dompdf) ke dalam ZIP agar siap pakai. Ingat: folder `vendor` cukup besar sehingga ZIP akan lebih besar.
