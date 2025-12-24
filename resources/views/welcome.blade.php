<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}?v=2">

    <title>CERT-VAULT</title>

    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.3s ease; }
        .dark { background: #020617; color: #f8fafc; }
        .light { background: #f1f5f9; color: #0f172a; }
        .glass-dark { background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05); }
        .glass-light { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(0,0,0,0.05); }
        .danger-pulse { animation: pulse-red 1.5s infinite; }
        @keyframes pulse-red {
            0%, 100% { background: #dc2626; box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
            50% { background: #991b1b; box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #8b5cf6; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #7c3aed; }
    </style>
</head>
<body id="main-body" class="dark h-screen overflow-hidden">
    <div id="root" class="h-full"></div>

    <script type="text/babel">
        const { useState, useEffect, useMemo, useRef } = React;

        function App() {
            const [isDarkMode, setIsDarkMode] = useState(true);
            const [isLoggedIn, setIsLoggedIn] = useState(false);
            const [loginData, setLoginData] = useState({ user: '', pass: '' });
            const [searchTerm, setSearchTerm] = useState('');
            const [filterWilayah, setFilterWilayah] = useState('');
            const [currentTime, setCurrentTime] = useState(new Date());
            const [certs, setCerts] = useState([]);
            const [editingId, setEditingId] = useState(null);
            const [form, setForm] = useState({
                nama: '', ktp: '', noSertif: '', noReg: '', kualifikasi: '', wilayah: '', tahunTerbit: ''
            });

            const fileInputRef = useRef(null);

            const fetchCerts = async () => {
                try {
                    const response = await fetch('/api/certs');
                    const data = await response.json();
                    const mappedData = data.map(item => ({
                        id: item.id,
                        nama: item.nama,
                        ktp: item.ktp,
                        noSertif: item.no_sertif,
                        noReg: item.no_reg,
                        kualifikasi: item.kualifikasi,
                        wilayah: item.wilayah,
                        tahunTerbit: item.tgl_terbit,
                        tglExpired: item.tgl_expired
                    }));
                    setCerts(mappedData);
                } catch (error) {
                    console.error("Gagal mengambil data:", error);
                }
            };

            useEffect(() => {
                fetchCerts();
                const timer = setInterval(() => setCurrentTime(new Date()), 1000);
                return () => clearInterval(timer);
            }, []);

            useEffect(() => {
                document.getElementById('main-body').className = isDarkMode ? 'dark h-screen overflow-hidden' : 'light h-screen overflow-hidden';
            }, [isDarkMode]);

            const uniqueWilayah = useMemo(() => {
                return [...new Set(certs.map(item => item.wilayah))].sort();
            }, [certs]);

            const filteredAndSortedData = useMemo(() => {
                return certs
                    .filter(c => {
                        const matchSearch = c.nama.toLowerCase().includes(searchTerm.toLowerCase()) || c.ktp.includes(searchTerm);
                        const matchWilayah = filterWilayah === '' || c.wilayah === filterWilayah;
                        return matchSearch && matchWilayah;
                    })
                    .sort((a, b) => a.nama.localeCompare(b.nama));
            }, [certs, searchTerm, filterWilayah]);

            const showToast = (title, icon) => {
                Swal.fire({ title, icon, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                    background: isDarkMode ? '#1e293b' : '#fff', color: isDarkMode ? '#fff' : '#000' });
            };

            const handleLogin = (e) => {
                e.preventDefault();
                if(loginData.user === 'admin' && loginData.pass === '1234') {
                    setIsLoggedIn(true);
                    showToast('Welcome!', 'success');
                } else {
                    Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: 'Kredensial Salah!',
                        background: isDarkMode ? '#1e293b' : '#fff', color: isDarkMode ? '#fff' : '#000' });
                }
            };

            const addOrUpdateData = async (e) => {
                e.preventDefault();
                if(Object.values(form).some(val => val === '')) return;

                const tglTerbit = new Date(form.tahunTerbit);
                const tglExpired = new Date(tglTerbit);
                tglExpired.setFullYear(tglExpired.getFullYear() + 3);
                const formatDate = (date) => date.toISOString().split('T')[0];
                const payload = { ...form, tglExpired: formatDate(tglExpired) };

                try {
                    if(editingId) {
                        await fetch(`/api/certs/${editingId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });
                        setEditingId(null);
                    } else {
                        await fetch('/api/certs', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });
                    }
                    fetchCerts();
                    setForm({ nama: '', ktp: '', noSertif: '', noReg: '', kualifikasi: '', wilayah: '', tahunTerbit: '' });
                    showToast('Data Berhasil Disimpan', 'success');
                } catch (error) {
                    Swal.fire('Error', 'Gagal menyimpan', 'error');
                }
            };

            // --- FUNGSI IMPORT EXCEL (FINAL FIX: TIMEZONE + MANUAL PARSE + ERROR HANDLING) ---
            const handleImportExcel = (e) => {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (evt) => {
                    const bstr = evt.target.result;
                    const wb = XLSX.read(bstr, { type: 'binary', cellDates: true });
                    const wsname = wb.SheetNames[0];
                    const ws = wb.Sheets[wsname];
                    const data = XLSX.utils.sheet_to_json(ws);

                    // --- FUNGSI PEMBERSIH TANGGAL ---
                    const cleanDate = (inputDate) => {
                        if (!inputDate) return new Date().toISOString().split('T')[0];

                        let dateObj;

                        // KASUS 1: Excel Object Date -> Tambah 12 Jam biar aman dari timezone mundur
                        if (inputDate instanceof Date) {
                            dateObj = new Date(inputDate);
                            dateObj.setHours(dateObj.getHours() + 12);
                        }
                        // KASUS 2: Excel Text (23-01-2023 atau 23/05/2024) -> Manual Parse
                        else if (typeof inputDate === 'string') {
                            const parts = inputDate.split(/[-/]/);
                            if (parts.length === 3) {
                                let d = parseInt(parts[0]);
                                let m = parseInt(parts[1]);
                                let y = parseInt(parts[2]);

                                // Antisipasi YYYY-MM-DD
                                if (parts[0].length === 4) { y = parseInt(parts[0]); m = parseInt(parts[1]); d = parseInt(parts[2]); }

                                dateObj = new Date(y, m - 1, d);
                            } else {
                                dateObj = new Date(inputDate);
                            }
                        }
                        else {
                            dateObj = new Date(inputDate);
                        }

                        if (isNaN(dateObj.getTime())) return new Date().toISOString().split('T')[0];

                        const year = dateObj.getFullYear();
                        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                        const day = String(dateObj.getDate()).padStart(2, '0');

                        return `${year}-${month}-${day}`;
                    };

                    const processedData = data.map(row => {
                        let rawTgl = row.TGL_TERBIT || row.tgl_terbit;
                        let validTglTerbit = cleanDate(rawTgl);

                        let tglPart = validTglTerbit.split('-');
                        let expYear = parseInt(tglPart[0]) + 3;
                        let validTglExpired = `${expYear}-${tglPart[1]}-${tglPart[2]}`;

                        return {
                            nama: row.NAMA || row.nama,
                            ktp: row.NIK_KTP || row.ktp,
                            no_sertif: row.NO_SERTIFIKAT || row.no_sertif,
                            no_reg: row.NO_REGISTRASI || row.no_reg,
                            kualifikasi: row.KUALIFIKASI || row.kualifikasi,
                            wilayah: row.WILAYAH || row.wilayah,
                            tgl_terbit: validTglTerbit,
                            tgl_expired: validTglExpired
                        };
                    });

                    // --- KIRIM KE SERVER DENGAN PENGECEKAN STATUS ---
                    fetch('/api/certs/import', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(processedData)
                    })
                    .then(async res => {
                        if (!res.ok) {
                            const errorData = await res.json();
                            throw new Error(errorData.message || 'Gagal Import Data');
                        }
                        return res.json();
                    })
                    .then(res => {
                        fetchCerts();
                        showToast(`Berhasil import ${processedData.length} data!`, 'success');
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Gagal Import', 'Cek format header Excel atau data duplikat!', 'error');
                    });
                };
                reader.readAsBinaryString(file);
                e.target.value = null;
            };

            const exportToExcel = () => {
                if (certs.length === 0) return;
                const cleanData = filteredAndSortedData.map(item => ({
                    NAMA: item.nama.toUpperCase(),
                    NIK_KTP: item.ktp,
                    NO_SERTIFIKAT: item.noSertif.toUpperCase(),
                    NO_REGISTRASI: item.noReg.toUpperCase(),
                    KUALIFIKASI: item.kualifikasi.toUpperCase(),
                    WILAYAH: item.wilayah.toUpperCase(),
                    TGL_TERBIT: item.tahunTerbit,
                    TGL_EXPIRED: item.tglExpired
                }));
                const ws = XLSX.utils.json_to_sheet(cleanData);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "DATABASE");
                XLSX.writeFile(wb, "CERT_VAULT_DATABASE.xlsx");
            };

            const getStatus = (expiredDate) => {
                const diffDays = Math.ceil((new Date(expiredDate) - currentTime) / (1000 * 60 * 60 * 24));
                if (diffDays <= 30) return { label: '☣️ DANGER', color: 'danger-pulse', alert: true, days: diffDays };
                if (diffDays <= 60) return { label: '⚠️ WARNING', color: 'bg-orange-500', alert: false, days: diffDays };
                return { label: '✅ AMAN', color: 'bg-emerald-600', alert: false, days: diffDays };
            };

            const handleDelete = (id) => {
                Swal.fire({
                    title: 'Hapus?', text: "Data akan dihapus!", icon: 'warning', showCancelButton: true,
                    background: isDarkMode ? '#1e293b' : '#fff', color: isDarkMode ? '#fff' : '#000'
                }).then(async (res) => {
                    if (res.isConfirmed) {
                        await fetch(`/api/certs/${id}`, { method: 'DELETE' });
                        fetchCerts();
                        showToast('Deleted', 'success');
                    }
                });
            }

            if (!isLoggedIn) {
                return (
                     <div className={`flex items-center justify-center min-h-screen p-6 ${isDarkMode ? 'bg-slate-950' : 'bg-slate-100'}`}>
                        <div className={`flex flex-col md:flex-row max-w-4xl w-full rounded-[40px] overflow-hidden shadow-2xl animate__animated animate__zoomIn ${isDarkMode ? 'glass-dark' : 'glass-light'}`}>
                            <div className="md:w-1/2 bg-violet-600 p-12 text-white flex flex-col justify-center">
                                <h1 className="text-5xl font-black mb-6 tracking-tighter uppercase">CERT<span className="text-violet-200">VAULT</span></h1>
                                <p className="text-lg opacity-90 leading-relaxed font-semibold">Digital Compliance & Certificate Management System.</p>
                                <p className="mt-4 opacity-70 text-sm leading-relaxed">Pantau masa berlaku sertifikat petugas secara real-time. Lindungi validitas operasional dengan sistem peringatan otomatis.</p>
                            </div>
                            <div className="md:w-1/2 p-12">
                                <h2 className="text-2xl font-black mb-8 uppercase tracking-tight">Login Portal</h2>
                                <form onSubmit={handleLogin} className="space-y-4">
                                    <input className={`w-full p-4 rounded-2xl outline-none border ${isDarkMode ? 'bg-white/5 border-white/10' : 'bg-black/5 border-black/10'}`} placeholder="Username" onChange={e => setLoginData({...loginData, user: e.target.value})} />
                                    <input type="password" className={`w-full p-4 rounded-2xl outline-none border ${isDarkMode ? 'bg-white/5 border-white/10' : 'bg-black/5 border-black/10'}`} placeholder="Password" onChange={e => setLoginData({...loginData, pass: e.target.value})} />
                                    <button className="w-full bg-violet-600 py-4 rounded-2xl font-black text-white hover:bg-violet-700 transition-all uppercase tracking-widest mt-4">Authorized Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                );
            }

            return (
                <div className="h-screen w-full flex flex-col p-4 md:p-6 overflow-hidden relative">
                    {/* --- HEADER FIX (Gak Ikut Scroll) --- */}
                    <div className="flex-none flex flex-col md:flex-row justify-between items-center mb-6 gap-4 z-20">
                        <div className="flex items-center gap-4">
                            <h1 className="text-3xl font-black tracking-tighter uppercase italic">CERT<span className="text-violet-500 font-light">VAULT</span></h1>
                        </div>
                        <div className="flex flex-wrap justify-center items-center gap-3">
                            <div className="relative">
                                <select
                                    value={filterWilayah}
                                    onChange={(e) => setFilterWilayah(e.target.value)}
                                    className={`pl-5 pr-10 py-3 rounded-2xl outline-none border text-xs font-bold font-sans transition-all appearance-none cursor-pointer shadow-lg w-full md:w-auto
                                        ${isDarkMode
                                            ? 'bg-[#1e293b] border-white/10 text-white hover:bg-[#334155]'
                                            : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'
                                        }`}
                                >
                                    <option value="" className="bg-slate-800 text-white">🌍 Semua Wilayah</option>
                                    {uniqueWilayah.map(wil => (
                                        <option key={wil} value={wil} className={isDarkMode ? 'bg-slate-800 text-white' : 'bg-white text-slate-700'}>
                                            📍 {wil}
                                        </option>
                                    ))}
                                </select>
                                <div className={`absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none ${isDarkMode ? 'text-white/50' : 'text-slate-400'}`}>
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <input
                                className={`pl-6 pr-4 py-3 rounded-2xl outline-none border w-48 text-sm font-bold transition-all ${isDarkMode ? 'bg-white/5 border-white/10 focus:border-violet-500' : 'bg-white border-black/10 focus:border-violet-500 shadow-sm'}`}
                                placeholder="🔍 Cari Nama/NIK..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />

                            <input type="file" ref={fileInputRef} onChange={handleImportExcel} accept=".xlsx, .xls" className="hidden" />
                            <button onClick={() => fileInputRef.current.click()} className="bg-violet-600 text-white px-5 py-3 rounded-2xl font-black shadow-lg shadow-violet-600/20 hover:scale-105 transition uppercase text-[10px] flex items-center gap-2">📥 Import</button>
                            <button onClick={exportToExcel} className="bg-emerald-600 text-white px-5 py-3 rounded-2xl font-black shadow-lg shadow-emerald-600/20 hover:scale-105 transition uppercase text-[10px] flex items-center gap-2">📤 Export</button>
                            <button onClick={() => setIsDarkMode(!isDarkMode)} className={`p-3 rounded-2xl text-xl border ${isDarkMode ? 'bg-white/5 border-white/10' : 'bg-white border-black/10'}`}>{isDarkMode ? '☀️' : '🌙'}</button>
                            <button onClick={() => setIsLoggedIn(false)} className="bg-red-500/10 text-red-500 px-5 py-3 rounded-2xl font-black border border-red-500/20 hover:bg-red-600 hover:text-white transition text-[10px]">LOGOUT</button>
                        </div>
                    </div>

                    {/* --- MAIN CONTENT (GRID) --- */}
                    <div className="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 overflow-hidden mb-8">

                        {/* 1. INPUT PANEL (Tetap ditempat / Freeze) */}
                        <div className="lg:col-span-4 h-full flex flex-col overflow-hidden">
                            <div className={`p-6 rounded-[30px] border h-full overflow-y-auto ${isDarkMode ? 'glass-dark' : 'glass-light shadow-xl'}`}>
                                <h2 className="text-xl font-black mb-6 flex items-center gap-3 uppercase italic sticky top-0 z-10">
                                    <span className="w-1.5 h-8 bg-violet-500 rounded-full"></span>
                                    {editingId ? 'Edit Record' : 'Add Personil'}
                                </h2>
                                <form onSubmit={addOrUpdateData} className="space-y-3 text-xs font-bold">
                                    <input className={`w-full p-4 rounded-xl outline-none border ${isDarkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-black/5'}`} placeholder="Nama Lengkap" value={form.nama} onChange={e => setForm({...form, nama: e.target.value})} />
                                    <input className={`w-full p-4 rounded-xl outline-none border ${isDarkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-black/5'}`} placeholder="NIK KTP" value={form.ktp} onChange={e => setForm({...form, ktp: e.target.value})} />
                                    <div className="grid grid-cols-2 gap-2">
                                        <input className={`p-4 rounded-xl outline-none border ${isDarkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-black/5'}`} placeholder="No. Sertifikat" value={form.noSertif} onChange={e => setForm({...form, noSertif: e.target.value})} />
                                        <input className={`p-4 rounded-xl outline-none border ${isDarkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-black/5'}`} placeholder="No. Registrasi" value={form.noReg} onChange={e => setForm({...form, noReg: e.target.value})} />
                                    </div>
                                    <input className={`w-full p-4 rounded-xl outline-none border ${isDarkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-black/5'}`} placeholder="Kode Kualifikasi" value={form.kualifikasi} onChange={e => setForm({...form, kualifikasi: e.target.value})} />
                                    <input className={`w-full p-4 rounded-xl outline-none border ${isDarkMode ? 'bg-white/5 border-white/10 text-white' : 'bg-white border-black/5'}`} placeholder="Wilayah Kerja" value={form.wilayah} onChange={e => setForm({...form, wilayah: e.target.value})} />
                                    <div className="p-4 bg-violet-500/10 rounded-2xl border border-violet-500/20 text-center">
                                        <label className="text-[9px] font-black text-violet-400 block mb-2 uppercase tracking-widest">Pilih Tanggal Terbit</label>
                                        <input type="date" className="bg-transparent outline-none font-black text-sm" value={form.tahunTerbit} onChange={e => setForm({...form, tahunTerbit: e.target.value})} />
                                    </div>
                                    <button className="w-full bg-violet-600 py-4 rounded-2xl font-black text-white shadow-xl hover:scale-[1.02] active:scale-95 transition-all text-sm uppercase">Simpan ke Database</button>
                                </form>
                            </div>
                        </div>

                        {/* 2. TABLE PANEL (Scrollable / Bisa discroll) */}
                        <div className={`lg:col-span-8 h-full flex flex-col rounded-[30px] border overflow-hidden ${isDarkMode ? 'glass-dark' : 'glass-light shadow-xl border-black/5'}`}>
                            {/* Header Table (Sticky) */}
                            <div className="flex-1 overflow-y-auto relative">
                                <table className="w-full text-left border-separate border-spacing-0">
                                    <thead className={`sticky top-0 z-10 text-[10px] uppercase font-bold tracking-widest ${isDarkMode ? 'bg-[#0f172a] text-slate-500 shadow-md' : 'bg-slate-200 text-slate-500 shadow-sm'}`}>
                                        <tr>
                                            <th className="p-6">Identitas Petugas</th>
                                            <th className="p-6">Detail Sertifikat</th>
                                            <th className="p-6 text-center">Countdown</th>
                                            <th className="p-6 text-center">Masa Berlaku</th>
                                            <th className="p-6 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className={isDarkMode ? 'text-slate-300' : 'text-slate-700'}>
                                        {filteredAndSortedData.map((c) => {
                                            const status = getStatus(c.tglExpired);
                                            return (
                                                <tr key={c.id} className="transition-all border-b border-white/5 hover:bg-violet-500/10 group">
                                                    <td className="p-6 border-b border-white/[0.03]">
                                                        <div className={`font-black uppercase text-sm ${isDarkMode ? 'text-white' : 'text-slate-900'}`}>{c.nama}</div>
                                                        <div className="text-[10px] opacity-40 font-bold uppercase tracking-tighter">NIK: {c.ktp}</div>
                                                        <div className="text-[10px] text-violet-500 font-black mt-1 uppercase italic">{c.wilayah}</div>
                                                    </td>
                                                    <td className="p-6 border-b border-white/[0.03]">
                                                        <div className="mb-2">
                                                            <span className={`px-2 py-1 rounded text-[10px] font-mono font-bold border ${isDarkMode ? 'bg-white/5 border-white/10 text-slate-300' : 'bg-slate-200 border-slate-300 text-slate-600'}`}>
                                                                🏷️ {c.noSertif}
                                                            </span>
                                                        </div>
                                                        <div className="text-[10px] font-bold">Reg: <span className="opacity-60">{c.noReg}</span></div>
                                                        <div className="text-[10px] text-violet-400 font-black mt-1 uppercase tracking-tight">Kual: {c.kualifikasi}</div>
                                                        <div className="text-[9px] mt-1 font-bold opacity-40 uppercase italic">Terbit: {c.tahunTerbit}</div>
                                                    </td>
                                                    <td className="p-6 border-b border-white/[0.03] text-center">
                                                        <div className={`text-3xl font-black ${status.alert ? 'text-red-500 animate-pulse' : isDarkMode ? 'text-white' : 'text-slate-900'}`}>
                                                            {status.days}
                                                        </div>
                                                        <p className="text-[8px] font-black opacity-30 uppercase tracking-[0.2em] -mt-1">Hari Lagi</p>
                                                    </td>
                                                    <td className="p-6 border-b border-white/[0.03] text-center">
                                                        <div className={`text-[9px] px-3 py-2.5 rounded-xl font-black text-white shadow-md ${status.color}`}>
                                                            {status.label}
                                                        </div>
                                                        <div className="text-[8px] mt-2 opacity-40 font-bold tracking-widest leading-none">
                                                            EXP: {c.tglExpired}
                                                        </div>
                                                    </td>
                                                    <td className="p-6 border-b border-white/[0.03] text-right flex gap-2 justify-end">
                                                        <button onClick={() => {setForm(c); setEditingId(c.id);}} className="p-2.5 rounded-xl bg-violet-500/10 text-violet-500 hover:bg-violet-500 hover:text-white transition">✏️</button>
                                                        <button onClick={() => handleDelete(c.id)} className="p-2.5 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition">🗑️</button>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* --- COPYRIGHT FOOTER (Fixed di bawah) --- */}
                    <div className="flex-none text-center py-2">
                        <p className={`text-[10px] font-bold uppercase tracking-[0.3em] ${isDarkMode ? 'text-white/20' : 'text-slate-400'}`}>
                            © 2025 PT MAHIZA KARYA MANDIRI. All Rights Reserved.
                        </p>
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>
</body>
</html>
