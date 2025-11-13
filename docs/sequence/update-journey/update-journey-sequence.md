# Sequence Diagram: Fitur Update Journey

Mengacu pada `docs/speckit/update-journey/specify.md`, berikut sequence diagram mermaid untuk alur penambahan journey dan evidence pada detail laporan.

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna
    participant Web as Detail Laporan (Blade)
    participant RC as ReportJourneyController
    participant RJ as ReportJourney (Model)
    participant RE as ReportEvidence (Model)
    participant DB as Database

    User->>Web: Buka halaman detail laporan
    Web-->>User: Tampilkan form tambah journey + upload bukti

    User->>Web: Pilih tipe journey (PEMERIKSAAN/LIMPAH/SIDANG/SELESAI)
    User->>Web: Isi deskripsi journey
    User->>Web: Pilih file untuk diupload (evidence)
    User->>Web: Klik Submit
    Web->>RC: POST /reports/{id}/journeys (payload: type, description, files[])

    RC->>RC: Validasi input (type, description, files)
    alt Validasi gagal
        RC-->>Web: Redirect back + errors (validation)
        Web-->>User: Tampilkan pesan error (SweetAlert/alert)
    else Validasi lolos
        RC->>RJ: create({ report_id, type, description, ... })
        RJ->>DB: INSERT INTO report_journeys
        DB-->>RJ: id (journey_id)

        alt Evidence diunggah
            loop Untuk setiap file evidence
                RC->>RE: create({ report_id, journey_id, path/metadata })
                RE->>DB: INSERT INTO report_evidence
                DB-->>RE: OK
            end
        else Tidak ada evidence
            RC->>RC: Lewati penyimpanan evidence
        end

        RC-->>Web: Redirect ke detail laporan + flash success
        Web-->>User: Tampilkan pesan sukses (SweetAlert)
        User->>Web: Lihat data journey & evidence terbaru pada detail laporan
    end
```

Catatan:
- Journey ditambahkan pada laporan yang sudah ada (report_id diketahui dari route/detail).
- Evidence (bukti) dikaitkan ke report dan journey agar riwayat tertata.
- Bukti file bisa lebih dari satu.
- Feedback validasi/sukses menggunakan SweetAlert sesuai integrasi global.
- Jika menggunakan Service/Repository, layer tersebut disisipkan di antara Controller dan Model tanpa mengubah alur utama.
```