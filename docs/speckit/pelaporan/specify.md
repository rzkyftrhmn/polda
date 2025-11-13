# Specify: Squence Fitur Pelaporan

## Purpose
Membuat sequence diagram untuk fitur pelaporan

## Model
- Report.php
- Suspect.php
- ReportJourney.php
- ReportEvidence.php

## Flow User Store Data
1. User membuka fitur pelaporan
2. User akan menginputkan data laporan sesuai dengan model Report.php
3. User akan menginputkan data suspect sesuai dengan model Suspect.php
4. User akan menginputkan data report journey sesuai dengan model ReportJourney.php
5. User akan menginputkan data report evidence sesuai dengan model ReportEvidence.php
6. User akan mengklik tombol submit
7. System akan menyimpan data laporan, suspect, report journey, dan report evidence
8. System akan mengembalikan response success
9. User akan melihat response success
10. User akan melihat data laporan, suspect, report journey, dan report evidence di database
