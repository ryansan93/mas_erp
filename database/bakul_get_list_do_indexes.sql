/* =====================================================================
   Diagnostik & index untuk pembayaran/Bakul::get_list_do()
   Target: SQL Server (T-SQL)
   Dibuat: 2026-07-06

   CARA PAKAI:
   - Jalankan BAGIAN 1 dulu (read-only) untuk melihat index yang sudah ada
     dan usulan index dari optimizer.
   - BAGIAN 2 = CREATE INDEX. Sudah di-guard (IF NOT EXISTS) sehingga aman
     dijalankan berulang. TETAP review dulu sebelum run di produksi.
   - Jalankan saat trafik rendah. Setiap CREATE INDEX bisa memakan waktu
     & I/O pada tabel besar. Pertimbangkan ONLINE = ON bila edisi Enterprise
     (lihat catatan di bawah).
   ===================================================================== */


/* =====================================================================
   BAGIAN 1 — DIAGNOSTIK (READ-ONLY, tidak mengubah apa pun)
   ===================================================================== */

-- 1a. Ukuran baris tiap tabel yang terlibat (untuk tahu mana yang besar)
SELECT
    t.name                         AS tabel,
    p.rows                         AS jml_baris,
    SUM(a.total_pages) * 8 / 1024  AS ukuran_mb
FROM sys.tables t
JOIN sys.partitions p   ON p.object_id = t.object_id AND p.index_id IN (0,1)
JOIN sys.allocation_units a ON a.container_id = p.partition_id
WHERE t.name IN (
    'det_real_sj','real_sj','det_pembayaran_pelanggan',
    'pembayaran_pelanggan','mitra_mapping','rdim_submit',
    'saldo_pelanggan','mitra'
)
GROUP BY t.name, p.rows
ORDER BY ukuran_mb DESC;


-- 1b. Index yang SUDAH ada pada tabel-tabel tersebut
SELECT
    t.name  AS tabel,
    i.name  AS index_name,
    i.type_desc,
    STUFF((
        SELECT ', ' + c.name + CASE WHEN ic.is_descending_key = 1 THEN ' DESC' ELSE '' END
        FROM sys.index_columns ic
        JOIN sys.columns c ON c.object_id = ic.object_id AND c.column_id = ic.column_id
        WHERE ic.object_id = i.object_id AND ic.index_id = i.index_id AND ic.is_included_column = 0
        ORDER BY ic.key_ordinal
        FOR XML PATH('')
    ), 1, 2, '') AS key_columns,
    STUFF((
        SELECT ', ' + c.name
        FROM sys.index_columns ic
        JOIN sys.columns c ON c.object_id = ic.object_id AND c.column_id = ic.column_id
        WHERE ic.object_id = i.object_id AND ic.index_id = i.index_id AND ic.is_included_column = 1
        ORDER BY ic.index_column_id
        FOR XML PATH('')
    ), 1, 2, '') AS included_columns
FROM sys.indexes i
JOIN sys.tables t ON t.object_id = i.object_id
WHERE t.name IN (
    'det_real_sj','real_sj','det_pembayaran_pelanggan',
    'pembayaran_pelanggan','mitra_mapping','rdim_submit',
    'saldo_pelanggan','mitra'
)
AND i.type_desc <> 'HEAP'
ORDER BY t.name, i.index_id;


-- 1c. Usulan index dari optimizer (missing index DMV).
--     Ini "keluhan" nyata optimizer berdasar query yang pernah jalan.
--     Kalau get_list_do sudah pernah dieksekusi, keluhannya sering muncul di sini.
SELECT
    ROUND(s.avg_total_user_cost * s.avg_user_impact * (s.user_seeks + s.user_scans), 0) AS skor_dampak,
    d.statement AS tabel,
    d.equality_columns,
    d.inequality_columns,
    d.included_columns,
    s.user_seeks + s.user_scans AS kena_berapa_kali
FROM sys.dm_db_missing_index_group_stats s
JOIN sys.dm_db_missing_index_groups g  ON s.group_handle = g.index_group_handle
JOIN sys.dm_db_missing_index_details d ON g.index_handle = d.index_handle
WHERE d.database_id = DB_ID()
  AND d.statement LIKE '%'+ '' +'%'
ORDER BY skor_dampak DESC;
GO


/* =====================================================================
   BAGIAN 2 — CREATE INDEX (mengubah database — review dulu!)

   Semua di-guard: hanya dibuat kalau belum ada.
   Catatan ONLINE: bila SQL Server edisi Enterprise, tambahkan
   "WITH (ONLINE = ON)" pada tiap CREATE agar tabel tidak terkunci.
   Pada Standard Edition, ONLINE tidak didukung -> biarkan seperti ini
   dan jalankan di jam sepi.
   ===================================================================== */

/* ---- det_real_sj : tabel penggerak utama (filter no_pelanggan) ---- */
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_det_real_sj_nopel' AND object_id = OBJECT_ID('det_real_sj'))
    CREATE NONCLUSTERED INDEX IX_det_real_sj_nopel
        ON det_real_sj (no_pelanggan)
        INCLUDE (id_header, harga, tonase, no_do, no_sj, ekor);
GO

-- join drs.id_header = rs.id
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_det_real_sj_idheader' AND object_id = OBJECT_ID('det_real_sj'))
    CREATE NONCLUSTERED INDEX IX_det_real_sj_idheader
        ON det_real_sj (id_header)
        INCLUDE (no_pelanggan, harga, tonase);
GO


/* ---- real_sj : filter tgl_panen >= X AND id_unit IN (...) ---- */
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_real_sj_unit_tgl' AND object_id = OBJECT_ID('real_sj'))
    CREATE NONCLUSTERED INDEX IX_real_sj_unit_tgl
        ON real_sj (id_unit, tgl_panen)
        INCLUDE (id, noreg);
GO

-- group by noreg, tgl_panen dengan max(id)
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_real_sj_noreg_tgl' AND object_id = OBJECT_ID('real_sj'))
    CREATE NONCLUSTERED INDEX IX_real_sj_noreg_tgl
        ON real_sj (noreg, tgl_panen)
        INCLUDE (id, id_unit);
GO


/* ---- det_pembayaran_pelanggan : group by id_do (max id), cek status ---- */
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_dpp_iddo_status' AND object_id = OBJECT_ID('det_pembayaran_pelanggan'))
    CREATE NONCLUSTERED INDEX IX_dpp_iddo_status
        ON det_pembayaran_pelanggan (id_do, status)
        INCLUDE (id, jumlah_bayar, id_header);
GO


/* ---- mitra_mapping : group by nim (max id), join ke mitra ---- */
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_mitra_mapping_nim' AND object_id = OBJECT_ID('mitra_mapping'))
    CREATE NONCLUSTERED INDEX IX_mitra_mapping_nim
        ON mitra_mapping (nim)
        INCLUDE (id, mitra);
GO


/* ---- rdim_submit : join noreg -> nim ---- */
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_rdim_submit_noreg' AND object_id = OBJECT_ID('rdim_submit'))
    CREATE NONCLUSTERED INDEX IX_rdim_submit_noreg
        ON rdim_submit (noreg)
        INCLUDE (nim);
GO


/* ---- saldo_pelanggan : lookup saldo & tgl_mulai_bayar ---- */
-- dipakai di baris 532: where no_pelanggan + perusahaan order by id desc
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_saldo_pel_perus' AND object_id = OBJECT_ID('saldo_pelanggan'))
    CREATE NONCLUSTERED INDEX IX_saldo_pel_perus
        ON saldo_pelanggan (no_pelanggan, perusahaan)
        INCLUDE (id, saldo);
GO

-- dipakai untuk tgl_mulai_bayar (baris 663, 675)
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'IX_saldo_pel_tglmulai' AND object_id = OBJECT_ID('saldo_pelanggan'))
    CREATE NONCLUSTERED INDEX IX_saldo_pel_tglmulai
        ON saldo_pelanggan (no_pelanggan)
        INCLUDE (tgl_mulai_bayar, id);
GO


/* =====================================================================
   BAGIAN 3 — VERIFIKASI SESUDAH (opsional)
   Setelah index dibuat, jalankan get_list_do lewat aplikasi, lalu:
   - Aktifkan "Include Actual Execution Plan" di SSMS saat menjalankan
     query utama secara manual untuk memastikan index dipakai (Index Seek,
     bukan Table Scan / Key Lookup besar).
   - Cek pemakaian index baru:
   ===================================================================== */
-- SELECT OBJECT_NAME(s.object_id) AS tabel, i.name, s.user_seeks, s.user_scans, s.user_lookups
-- FROM sys.dm_db_index_usage_stats s
-- JOIN sys.indexes i ON i.object_id = s.object_id AND i.index_id = s.index_id
-- WHERE s.database_id = DB_ID() AND i.name LIKE 'IX_%'
-- ORDER BY tabel, i.name;
