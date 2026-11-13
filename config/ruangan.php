<?php

/*
|--------------------------------------------------------------------------
| Peminjaman Ruangan
|--------------------------------------------------------------------------
| Jam operasional dan langkah waktu pada pemilih jadwal. Dipakai server (validasi)
| dan diteruskan ke pemilih jadwal di browser.
*/

return [
    'jam_buka'  => 7,    // 07.00
    'jam_tutup' => 17,   // 17.00
    'langkah'   => 15,   // menit; waktu mulai dan selesai harus kelipatan ini
];
