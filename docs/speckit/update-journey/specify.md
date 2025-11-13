# Specify: Squence Fitur Update Journey

## Purpose
Membuat sequence diagram untuk fitur update journey

## Model
- ReportJourney.php
- ReportEvidence.php

## Flow User Store Data
1. User membuat data laporan dan redirect ke detail page
2. User memilih tipe journey data: "PEMERIKSAAN", "LIMPAH", "SIDANG", "SELESAI"
3. User menginputkan deskripsi journey
4. User Pilih file dari device untuk diupload
5. User mengklik tombol submit
6. System akan menyimpan data journey dan evidence
7. System akan mengembalikan response success
8. User akan melihat response success
9. User akan melihat data journey dan evidence di page detail laporan
