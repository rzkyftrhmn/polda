## Specify: CRUD Event Participant

## Goal
1. Membuat CRUD Event Participant. 

## Create
From yang dibutuhkan:
1. Nama Event
2. Deskripsi Event
3. Lokasi Event
4. Waktu Mulai Event
5. Waktu Selesai Event
6. List Peserta

From list Peserta menggunakan format table seperti yang ada di crud pelaporan bagian terlapor, form yang dibutuhkan ketika input data peserta menggunakan modal:
1. Unit
2. Toggle Status Kejadiran (Wajib atau Opsional)
3. Jika Opsional, tambahkan keterangan

# List
Data yang dimunculkan di table list event:
1. Nama Event
3. Lokasi Event
4. Waktu Mulai Event
5. Waktu Selesai Event
6. Total Peserta

# Model yang digunakan
Model yang digunakan untuk menyimpan data event participant:
- EventParticipant
Model yang digunakan untuk menyimpan data event:
- Event

## Notes
- Gunakan Table untuk menampilkan data peserta
- Gunakan modal untuk input data peserta
- Cara input bisa mencontoh CRUD pelaporan bagian terlapor
