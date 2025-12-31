<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
    <title>CERT VAULT SYSTEM</title>

    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: background-color 0.3s, color 0.3s; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #64748b; border-radius: 10px; }
        .dark-mode { background-color: #0f172a; color: #f1f5f9; }
        .light-mode { background-color: #f8fafc; color: #1e293b; }
        .glass-dark { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-light { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div id="root" class="h-screen w-full overflow-hidden"></div>

    @verbatim
    <script type="text/babel">
        const { useState, useEffect, useMemo, useRef } = React;

        // --- ICONS ---
        const Icons = {
            Dashboard: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>,
            Database: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>,
            Active: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>,
            Expired: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>,
            Logout: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>,
            Menu: () => <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>,
            Moon: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>,
            Sun: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>,
            Add: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4"></path></svg>,
            Download: () => <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        };

        const StatCard = ({ title, count, colorClass, isDarkMode, icon }) => (
            <div className={`p-6 rounded-3xl border transition-all hover:scale-[1.02] flex items-center justify-between ${isDarkMode ? 'bg-[#1e293b] border-white/5' : 'bg-white border-slate-200 shadow-sm'}`}>
                <div>
                    <p className={`text-[10px] font-bold uppercase tracking-widest mb-1 ${isDarkMode ? 'text-slate-400' : 'text-slate-500'}`}>{title}</p>
                    <h3 className={`text-4xl font-black ${colorClass}`}>{count}</h3>
                </div>
                <div className={`p-3 rounded-2xl ${isDarkMode ? 'bg-white/5' : 'bg-slate-100'} opacity-70`}>{icon}</div>
            </div>
        );

        const HeroBanner = ({ currentTime }) => (
            <div className="rounded-[30px] p-8 mb-8 relative overflow-hidden bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-xl">
                <div className="relative z-10">
                    <p className="opacity-80 text-sm font-bold uppercase tracking-widest mb-2">{currentTime.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                    <h1 className="text-3xl md:text-4xl font-black mb-2">Welcome back, Administrator!</h1>
                    <p className="opacity-90 max-w-xl">Berikut adalah ringkasan status sertifikat petugas hari ini. Cek data expired untuk tindakan segera.</p>
                </div>
                <div className="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
            </div>
        );

        const NavItem = ({ id, icon: Icon, label, activeView, setActiveView, isSidebarOpen }) => (
            <button onClick={() => setActiveView(id)} className={`w-full flex items-center gap-3 p-3 rounded-xl transition-all mb-1 group ${activeView === id ? 'bg-violet-600 text-white shadow-md' : 'hover:bg-white/5 opacity-70 hover:opacity-100'}`}>
                <Icon /> <span className={`${!isSidebarOpen && 'hidden'} text-sm font-semibold whitespace-nowrap`}>{label}</span>
            </button>
        );

        function App() {
            const [isDarkMode, setIsDarkMode] = useState(true);
            const [isLoggedIn, setIsLoggedIn] = useState(false);
            const [activeView, setActiveView] = useState('dashboard');
            const [isSidebarOpen, setIsSidebarOpen] = useState(true);
            const [certs, setCerts] = useState([]);
            const [currentTime, setCurrentTime] = useState(new Date());
            const [loginData, setLoginData] = useState({ user: '', pass: '' });

            const [searchTerm, setSearchTerm] = useState('');
            const [filterWilayah, setFilterWilayah] = useState('');
            const [editingId, setEditingId] = useState(null);
            const [form, setForm] = useState({ nama: '', ktp: '', noSertif: '', noReg: '', kualifikasi: '', wilayah: '', tahunTerbit: '' });
            const fileInputRef = useRef(null);

            useEffect(() => {
                const root = document.getElementById('root');
                if(isDarkMode) { root.className = 'h-screen w-full overflow-hidden dark-mode'; }
                else { root.className = 'h-screen w-full overflow-hidden light-mode'; }
            }, [isDarkMode]);

            useEffect(() => {
                fetchCerts();
                const timer = setInterval(() => setCurrentTime(new Date()), 1000);
                return () => clearInterval(timer);
            }, []);

            const fetchCerts = async () => {
                try {
                    const response = await fetch('/api/certs');
                    const data = await response.json();
                    const sortedData = data.map(item => ({
                        id: item.id, nama: item.nama, ktp: item.ktp, noSertif: item.no_sertif,
                        noReg: item.no_reg, kualifikasi: item.kualifikasi, wilayah: item.wilayah,
                        tahunTerbit: item.tgl_terbit, tglExpired: item.tgl_expired
                    })).sort((a, b) => a.nama.localeCompare(b.nama));
                    setCerts(sortedData);
                } catch (error) { console.error("Error fetching data"); }
            };

            const processedData = useMemo(() => {
                const today = new Date();
                today.setHours(0,0,0,0);
                return {
                    all: certs,
                    active: certs.filter(c => new Date(c.tglExpired) >= today),
                    expired: certs.filter(c => new Date(c.tglExpired) < today)
                };
            }, [certs, currentTime]);

            const tableData = useMemo(() => {
                let data = activeView === 'active' ? processedData.active : (activeView === 'expired' ? processedData.expired : processedData.all);
                return data.filter(c => {
                    const searchLower = searchTerm.toLowerCase();
                    const matchSearch = c.nama.toLowerCase().includes(searchLower) || c.ktp.includes(searchLower) || c.kualifikasi.toLowerCase().includes(searchLower);
                    const matchWilayah = filterWilayah === '' || c.wilayah === filterWilayah;
                    return matchSearch && matchWilayah;
                }).sort((a, b) => a.nama.localeCompare(b.nama));
            }, [activeView, processedData, searchTerm, filterWilayah]);

            const uniqueWilayah = useMemo(() => [...new Set(certs.map(i => i.wilayah))].sort(), [certs]);
            const regionStats = useMemo(() => uniqueWilayah.map(r => ({
                region: r, count: certs.filter(c => c.wilayah === r).length,
                percent: (certs.filter(c => c.wilayah === r).length / (certs.length || 1)) * 100
            })).sort((a,b) => b.count - a.count).slice(0,5), [certs, uniqueWilayah]);

            const handleLogin = (e) => {
                e.preventDefault();
                if(loginData.user === 'admin' && loginData.pass === '1234') {
                    setIsLoggedIn(true); Swal.fire({ icon: 'success', title: 'Welcome', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                } else { Swal.fire({ icon: 'error', title: 'Login Gagal' }); }
            };

            const addOrUpdateData = async (e) => {
                e.preventDefault();
                const tglTerbit = new Date(form.tahunTerbit);
                const tglExpired = new Date(tglTerbit);
                tglExpired.setFullYear(tglExpired.getFullYear() + 3);
                const offset = tglExpired.getTimezoneOffset();
                const cleanDate = new Date(tglExpired.getTime() - (offset*60*1000));
                const payload = { ...form, tglExpired: cleanDate.toISOString().split('T')[0] };
                const url = editingId ? `/api/certs/${editingId}` : '/api/certs';
                const method = editingId ? 'PUT' : 'POST';
                await fetch(url, { method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
                fetchCerts(); setEditingId(null); setForm({ nama: '', ktp: '', noSertif: '', noReg: '', kualifikasi: '', wilayah: '', tahunTerbit: '' });
                Swal.fire('Sukses', 'Data tersimpan', 'success');
            };

            const handleDelete = (id) => {
                Swal.fire({ title: 'Hapus?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' }).then(async (res) => {
                    if (res.isConfirmed) { await fetch(`/api/certs/${id}`, { method: 'DELETE' }); fetchCerts(); Swal.fire('Terhapus!', '', 'success'); }
                });
            };

            const handleImport = (e) => {
                const file = e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (evt) => {
                    const wb = XLSX.read(evt.target.result, { type: 'binary', cellDates: true });
                    const data = XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]);
                    const processed = data.map(row => {
                        let dateObj = new Date(row.TGL_TERBIT || row.tgl_terbit);
                        if(isNaN(dateObj)) dateObj = new Date();
                        dateObj.setHours(dateObj.getHours() + 12);
                        const tglTerbit = dateObj.toISOString().split('T')[0];
                        const parts = tglTerbit.split('-');
                        const tglExpired = `${parseInt(parts[0])+3}-${parts[1]}-${parts[2]}`;
                        return {
                            nama: row.NAMA||row.nama, ktp: row.NIK_KTP||row.ktp, no_sertif: row.NO_SERTIFIKAT||row.no_sertif,
                            no_reg: row.NO_REGISTRASI||row.no_reg, kualifikasi: row.KUALIFIKASI||row.kualifikasi,
                            wilayah: row.WILAYAH||row.wilayah, tgl_terbit: tglTerbit, tgl_expired: tglExpired
                        };
                    });
                    fetch('/api/certs/import', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(processed)})
                    .then(() => { fetchCerts(); Swal.fire('Sukses', `Import ${processed.length} data`, 'success'); });
                };
                reader.readAsBinaryString(file);
                e.target.value = null;
            };

            const exportExcel = () => {
                if(tableData.length === 0) { Swal.fire('Info', 'Tidak ada data untuk diexport', 'info'); return; }
                const cleanData = tableData.map(i => ({
                    NAMA: i.nama, NIK_KTP: i.ktp, NO_SERTIFIKAT: i.noSertif, NO_REGISTRASI: i.noReg, KUALIFIKASI: i.kualifikasi,
                    WILAYAH: i.wilayah, TGL_TERBIT: i.tahunTerbit, TGL_EXPIRED: i.tglExpired
                }));
                const ws = XLSX.utils.json_to_sheet(cleanData);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "DATA");
                let filename = activeView === 'active' ? "DATA_AKTIF.xlsx" : (activeView === 'expired' ? "DATA_EXPIRED.xlsx" : "CERT_DATA.xlsx");
                XLSX.writeFile(wb, filename);
            };

            const getStatus = (exp) => {
                const diff = Math.ceil((new Date(exp) - currentTime)/(1000*60*60*24));
                if(diff<=0) return { label: 'EXPIRED', days: diff, color: 'text-red-500', bg: 'bg-red-500/10' };
                if(diff<=30) return { label: 'WARNING', days: diff, color: 'text-orange-500', bg: 'bg-orange-500/10' };
                return { label: 'AKTIF', days: diff, color: 'text-emerald-500', bg: 'bg-emerald-500/10' };
            };

            if (!isLoggedIn) {
                return (
                    <div className="flex items-center justify-center h-full p-6">
                        <div className={`w-full max-w-md p-8 rounded-3xl shadow-2xl animate__animated animate__fadeIn ${isDarkMode ? 'glass-dark' : 'glass-light'}`}>
                            <div className="text-center mb-8">
                                <h1 className="text-3xl font-black uppercase tracking-tight mb-2">CERT VAULT <span className="text-violet-500">SYSTEM</span></h1>
                                <p className="text-sm opacity-60">Authorized Staff Only</p>
                            </div>
                            <form onSubmit={handleLogin} className="space-y-4">
                                <input className={`w-full p-4 rounded-xl outline-none border transition-all ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-white border-slate-300'}`} placeholder="Username" value={loginData.user} onChange={e => setLoginData({...loginData, user: e.target.value})} />
                                <input type="password" className={`w-full p-4 rounded-xl outline-none border transition-all ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-white border-slate-300'}`} placeholder="Password" value={loginData.pass} onChange={e => setLoginData({...loginData, pass: e.target.value})} />
                                <button className="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg">LOGIN DASHBOARD</button>
                            </form>
                            <div className="mt-6 flex justify-center">
                                <button onClick={() => setIsDarkMode(!isDarkMode)} className="p-2 rounded-full hover:bg-white/10 transition">{isDarkMode ? <Icons.Sun /> : <Icons.Moon />}</button>
                            </div>
                        </div>
                    </div>
                );
            }

            return (
                <div className="flex h-full">
                    <aside className={`flex flex-col border-r transition-all duration-300 ${isSidebarOpen ? 'w-64' : 'w-20'} ${isDarkMode ? 'bg-[#1e293b] border-white/5' : 'bg-white border-slate-200'}`}>
                        <div className="h-20 flex items-center justify-center border-b border-inherit">
                            <div className={`font-black text-xl tracking-tighter ${!isSidebarOpen && 'text-2xl'}`}>{isSidebarOpen ? <span>CV<span className="text-violet-500">.SYS</span></span> : <span className="text-violet-500">CV</span>}</div>
                        </div>
                        <nav className="flex-1 p-4 overflow-y-auto">
                            <NavItem id="dashboard" icon={Icons.Dashboard} label="Dashboard" activeView={activeView} setActiveView={setActiveView} isSidebarOpen={isSidebarOpen} />
                            <NavItem id="all" icon={Icons.Database} label="Master Data" activeView={activeView} setActiveView={setActiveView} isSidebarOpen={isSidebarOpen} />
                            <NavItem id="active" icon={Icons.Active} label="Sertifikat Aktif" activeView={activeView} setActiveView={setActiveView} isSidebarOpen={isSidebarOpen} />
                            <NavItem id="expired" icon={Icons.Expired} label="Data Expired" activeView={activeView} setActiveView={setActiveView} isSidebarOpen={isSidebarOpen} />
                        </nav>
                        <div className="p-4 border-t border-inherit">
                            <button onClick={() => setIsLoggedIn(false)} className="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-500/10 text-red-500 transition-all"><Icons.Logout /> <span className={`${!isSidebarOpen && 'hidden'} text-sm font-bold`}>Logout</span></button>
                        </div>
                    </aside>

                    <main className="flex-1 flex flex-col h-full overflow-hidden relative">
                        <header className={`h-20 border-b flex items-center justify-between px-6 z-10 sticky top-0 backdrop-blur-md ${isDarkMode ? 'bg-[#0f172a]/80 border-white/5' : 'bg-white/80 border-slate-200'}`}>
                            <div className="flex items-center gap-4">
                                <button onClick={() => setIsSidebarOpen(!isSidebarOpen)} className="p-2 rounded-lg hover:bg-white/10 opacity-70"><Icons.Menu /></button>
                                <h2 className="text-lg font-bold capitalize hidden md:block">{activeView.replace('_', ' ')} Overview</h2>
                            </div>
                            <div className="flex items-center gap-3">
                                <button onClick={() => setIsDarkMode(!isDarkMode)} className={`p-2 rounded-lg border ${isDarkMode ? 'border-white/10 hover:bg-white/5' : 'border-slate-200 hover:bg-slate-100'}`}>{isDarkMode ? <Icons.Sun /> : <Icons.Moon />}</button>
                            </div>
                        </header>

                        <div className="flex-1 overflow-y-auto p-6 md:p-8">
                            {activeView === 'dashboard' ? (
                                <div className="animate__animated animate__fadeIn">
                                    <HeroBanner currentTime={currentTime} />
                                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                        <div className="lg:col-span-2 space-y-8">
                                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <StatCard title="Total Data" count={processedData.all.length} colorClass={isDarkMode ? 'text-white' : 'text-slate-800'} isDarkMode={isDarkMode} icon={<Icons.Database />} />
                                                <StatCard title="Aktif" count={processedData.active.length} colorClass="text-emerald-500" isDarkMode={isDarkMode} icon={<Icons.Active />} />
                                                <StatCard title="Expired" count={processedData.expired.length} colorClass="text-red-500" isDarkMode={isDarkMode} icon={<Icons.Expired />} />
                                            </div>
                                            <div className={`rounded-3xl border p-6 ${isDarkMode ? 'bg-[#1e293b] border-white/5' : 'bg-white border-slate-200 shadow-sm'}`}>
                                                <div className="flex justify-between items-center mb-6"><h3 className="font-bold">⚠️ Perlu Perhatian (Urut Abjad)</h3><button onClick={() => setActiveView('expired')} className="text-xs font-bold text-violet-500">Lihat Semua</button></div>
                                                <div className="overflow-x-auto">
                                                    <table className="w-full text-left text-sm border-separate border-spacing-y-2">
                                                        <thead className="opacity-50 uppercase text-[10px] border-b border-inherit">
                                                            <tr>
                                                                <th className="pb-3 px-2">Nama Petugas</th>
                                                                <th className="pb-3 px-2">No. Registrasi</th>
                                                                <th className="pb-3 px-2">Tgl Terbit</th>
                                                                <th className="pb-3 px-2 text-center">Status Sisa Hari</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {processedData.all.sort((a,b) => a.nama.localeCompare(b.nama)).slice(0, 10).map(c => {
                                                                const st = getStatus(c.tglExpired);
                                                                return (
                                                                    <tr key={c.id} className={`${isDarkMode ? 'bg-white/5' : 'bg-slate-50'} hover:scale-[1.01] transition-all`}>
                                                                        <td className="py-3 px-3 rounded-l-xl font-bold">{c.nama}</td>
                                                                        <td className="py-3 px-3 opacity-70 text-xs font-mono">{c.noReg}</td>
                                                                        <td className="py-3 px-3 opacity-70 text-xs">{c.tahunTerbit}</td>
                                                                        <td className="py-3 px-3 rounded-r-xl text-center">
                                                                            <span className={`text-[10px] px-2 py-1 rounded font-black ${st.bg} ${st.color}`}>
                                                                                {st.days <= 0 ? 'EXPIRED' : st.days + ' Hari'}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                )
                                                            })}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="space-y-8">
                                            <div className={`rounded-3xl border p-6 ${isDarkMode ? 'bg-[#1e293b] border-white/5' : 'bg-white border-slate-200 shadow-sm'}`}>
                                                <h3 className="font-bold mb-4">⚡ Quick Actions</h3>
                                                <div className="grid grid-cols-2 gap-3">
                                                    <button onClick={() => setActiveView('all')} className="p-4 rounded-2xl bg-violet-600 text-white font-bold text-xs hover:bg-violet-700 transition flex flex-col items-center gap-2"><Icons.Add /> Input Data</button>
                                                    <button onClick={exportExcel} className="p-4 rounded-2xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition flex flex-col items-center gap-2"><Icons.Download /> Export Excel</button>
                                                </div>
                                            </div>
                                            <div className={`rounded-3xl border p-6 ${isDarkMode ? 'bg-[#1e293b] border-white/5' : 'bg-white border-slate-200 shadow-sm'}`}>
                                                <h3 className="font-bold mb-4">🗺️ Top Wilayah</h3>
                                                <div className="space-y-4">{regionStats.map((r, i) => <div key={i}><div className="flex justify-between text-xs font-bold mb-1"><span>{r.region}</span><span>{r.count}</span></div><div className="w-full bg-slate-700 rounded-full h-2"><div className="bg-violet-500 h-2 rounded-full" style={{width: `${r.percent}%`}}></div></div></div>)}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="flex flex-col lg:flex-row gap-6 animate__animated animate__fadeIn">
                                    {activeView === 'all' && (
                                        <div className={`lg:w-1/3 p-6 rounded-3xl border h-fit ${isDarkMode ? 'bg-[#1e293b] border-white/5' : 'bg-white border-slate-200 shadow-sm'}`}>
                                            <h3 className="font-bold mb-4 pb-4 border-b border-inherit">📝 {editingId ? 'Edit Data' : 'Input Baru'}</h3>
                                            <form onSubmit={addOrUpdateData} className="space-y-3">
                                                <input className={`w-full p-3 rounded-xl text-sm font-bold outline-none border ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-slate-50 border-slate-200'}`} placeholder="Nama" value={form.nama} onChange={e => setForm({...form, nama: e.target.value})} required />
                                                <input className={`w-full p-3 rounded-xl text-sm font-bold outline-none border ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-slate-50 border-slate-200'}`} placeholder="NIK" value={form.ktp} onChange={e => setForm({...form, ktp: e.target.value})} required />
                                                <div className="grid grid-cols-2 gap-2">
                                                    <input className={`w-full p-3 rounded-xl text-sm font-bold outline-none border ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-slate-50 border-slate-200'}`} placeholder="No. Sertif" value={form.noSertif} onChange={e => setForm({...form, noSertif: e.target.value})} required />
                                                    <input className={`w-full p-3 rounded-xl text-sm font-bold outline-none border ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-slate-50 border-slate-200'}`} placeholder="No. Reg" value={form.noReg} onChange={e => setForm({...form, noReg: e.target.value})} required />
                                                </div>
                                                <input className={`w-full p-3 rounded-xl text-sm font-bold outline-none border ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-slate-50 border-slate-200'}`} placeholder="Kualifikasi" value={form.kualifikasi} onChange={e => setForm({...form, kualifikasi: e.target.value})} required />
                                                <input className={`w-full p-3 rounded-xl text-sm font-bold outline-none border ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-slate-50 border-slate-200'}`} placeholder="Wilayah" value={form.wilayah} onChange={e => setForm({...form, wilayah: e.target.value})} required />
                                                <input type="date" className="w-full p-3 rounded-xl text-sm font-bold outline-none border bg-transparent" value={form.tahunTerbit} onChange={e => setForm({...form, tahunTerbit: e.target.value})} required />
                                                <button className="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 rounded-xl transition-all text-sm">{editingId ? 'Update' : 'Simpan'}</button>
                                            </form>
                                            <div className="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-inherit">
                                                <button onClick={() => fileInputRef.current.click()} className="bg-slate-500 text-white py-2 rounded-xl text-xs font-bold">Import</button>
                                                <input type="file" ref={fileInputRef} className="hidden" onChange={handleImport} accept=".xlsx, .xls" />
                                                <button onClick={exportExcel} className="bg-emerald-600 text-white py-2 rounded-xl text-xs font-bold">Export</button>
                                            </div>
                                        </div>
                                    )}
                                    <div className={`flex-1 rounded-3xl border overflow-hidden flex flex-col ${isDarkMode ? 'bg-[#1e293b] border-white/5' : 'bg-white border-slate-200 shadow-sm'}`}>
                                        <div className="p-4 border-b border-inherit flex flex-wrap gap-2 justify-between items-center">
                                            <div className="flex items-center gap-2"><h3 className="font-bold">Urut Abjad ({tableData.length})</h3></div>
                                            <div className="flex gap-2">
                                                <select className={`text-xs font-bold p-2 rounded-xl border outline-none ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-slate-50 border-slate-200'}`} onChange={e => setFilterWilayah(e.target.value)}><option value="">🌍 Semua</option>{uniqueWilayah.map(w => <option key={w} value={w}>{w}</option>)}</select>
                                                <input className={`text-xs font-bold p-2 rounded-xl border outline-none w-32 ${isDarkMode ? 'bg-black/20 border-white/10' : 'bg-slate-50 border-slate-200'}`} placeholder="🔍 Cari..." onChange={e => setSearchTerm(e.target.value)} />
                                            </div>
                                        </div>
                                        <div className="flex-1 overflow-auto">
                                            <table className="w-full text-left text-sm border-separate border-spacing-0">
                                                <thead className={`text-[10px] uppercase font-bold sticky top-0 z-10 ${isDarkMode ? 'bg-[#0f172a] text-slate-400' : 'bg-slate-100 text-slate-500'}`}>
                                                    <tr><th className="p-4">Identitas Petugas</th><th className="p-4">Detail Sertifikat</th><th className="p-4 text-center">Masa Berlaku</th>{activeView === 'all' && <th className="p-4 text-right">Aksi</th>}</tr>
                                                </thead>
                                                <tbody>
                                                    {tableData.length === 0 ? <tr><td colSpan="4" className="p-8 text-center opacity-50">Tidak ada data</td></tr> :
                                                    tableData.map(c => {
                                                        const st = getStatus(c.tglExpired);
                                                        return (
                                                            <tr key={c.id} className="border-b border-inherit hover:bg-black/5 transition group">
                                                                <td className="p-4 border-b border-inherit">
                                                                    <div className="font-bold uppercase tracking-tight">{c.nama}</div>
                                                                    <div className="text-[10px] opacity-60">NIK: {c.ktp}</div>
                                                                    <div className="text-[10px] text-violet-500 font-black mt-1 uppercase italic">{c.wilayah}</div>
                                                                </td>
                                                                <td className="p-4 border-b border-inherit">
                                                                    <div className="mb-1">
                                                                        <span className={`text-[10px] px-1.5 py-0.5 rounded font-mono border ${isDarkMode ? 'bg-white/5 border-white/10' : 'bg-slate-100 border-slate-200'}`}>Sertif: {c.noSertif}</span>
                                                                    </div>
                                                                    <div className="text-[10px] font-bold text-orange-400 uppercase tracking-tighter">Kual: {c.kualifikasi}</div>
                                                                    <div className="text-[10px] opacity-60 font-mono">Reg: {c.noReg}</div>
                                                                    <div className="text-[9px] mt-1 opacity-40 uppercase font-bold italic">Terbit: {c.tahunTerbit}</div>
                                                                </td>
                                                                <td className="p-4 border-b border-inherit text-center">
                                                                    <div className={`text-lg font-black leading-none ${st.color}`}>{st.days <= 0 ? 'EXPIRED' : st.days + ' Hari'}</div>
                                                                    <div className={`text-[9px] px-2 py-1 rounded font-black w-fit mx-auto mt-1 ${st.bg} ${st.color}`}>{st.label}</div>
                                                                    <div className="text-[9px] mt-1 opacity-40 font-mono">Exp: {c.tglExpired}</div>
                                                                </td>
                                                                {activeView === 'all' && (<td className="p-4 border-b border-inherit text-right whitespace-nowrap"><button onClick={() => {setForm(c); setEditingId(c.id)}} className="text-violet-500 p-2 hover:bg-violet-500/10 rounded-xl transition">✏️</button><button onClick={() => handleDelete(c.id)} className="text-red-500 p-2 hover:bg-red-500/10 rounded-xl transition">🗑️</button></td>)}
                                                            </tr>
                                                        )
                                                    })}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </main>
                </div>
            );
        }
        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>
    @endverbatim
</body>
</html>
