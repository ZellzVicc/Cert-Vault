# Cert-Vault

## Project Overview
Cert-Vault is a certificate management application designed to streamline the handling of digital certificates. This application provides a user-friendly interface for managing certificates effectively and securely.

## Features
- Easy to use interface for certificate management.
- Support for generating and storing certificates.
- Integration with a MySQL database for data storage.
- User authentication and role management for enhanced security.
- Detailed logs of certificate usage and management activities.

## Technologies Used
- **PHP**: For back-end logic and server-side scripting.
- **Blade Templates**: For rendering views in a flexible and efficient manner.
- **MySQL**: For reliable and efficient data storage and retrieval.

## Installation Instructions
1. Clone the repository:
   ```bash
   git clone https://github.com/ahmadrizal-baihaqi/Cert-Vault.git
   cd Cert-Vault
   ```
2. Configure your .env file for database connectivity.
3. Run migrations to set up the database:
   ```bash
   php artisan migrate
   ```
4. php artisan tinker
$user = new \App\Models\User;
$user->name = 'Admin Libera';
$user->email = 'admin@gmail.com';
$user->password = Hash::make('123');
$user->role = 'admin';
$user->save();
4. Start the local development server:
   ```bash
   php artisan serve
   ```

## Usage Guide
- Access the application in your web browser at `http://localhost:8000`
- Follow the instructions to create and manage certificates.

## Contribution Guidelines
- Fork the repository and create a new branch for your feature or bug fix.
- Submit a pull request detailing your changes for review.
- Ensure your contributions adhere to our coding standards and practices.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

<tbody class="divide-y divide-gray-50 text-[13px]">
    @forelse($loans as $loan)
    @php
        // Logika cek apakah sekarang sudah lewat batas dan buku belum balik
        $isTerlambat = $loan->status == 'dipinjam' && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($loan->batas_kembali));
    @endphp
    <tr class="hover:bg-gray-50/50 transition-colors">
        <td class="px-8 py-6">
            <p class="font-bold text-gray-900 uppercase tracking-tighter">{{ $loan->user->name }}</p>
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">ID: #{{ $loan->user->id }}</p>
        </td>

        <td class="px-8 py-6">
            <p class="font-bold text-gray-900 uppercase tracking-tighter line-clamp-1 max-w-[200px]">{{ $loan->book->judul }}</p>
            <p class="text-[9px] text-[#0d6efd] font-bold uppercase tracking-widest mt-1">{{ $loan->book->category->nama_kategori ?? 'Umum' }}</p>
        </td>

        <td class="px-8 py-6 font-bold text-gray-500 uppercase">
            {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d/m/Y') }}
        </td>

        <td class="px-8 py-6 font-bold text-red-500 uppercase">
            {{ \Carbon\Carbon::parse($loan->batas_kembali)->format('d/m/Y') }}
        </td>

        <td class="px-8 py-6 font-bold uppercase">
            @if($loan->status == 'dikembalikan')
                <span class="text-green-500">
                    {{ \Carbon\Carbon::parse($loan->tanggal_kembali ?? $loan->updated_at)->format('d/m/Y') }}
                </span>
            @elseif($isTerlambat)
                <span class="text-red-600 animate-pulse">MELEWATI BATAS!</span>
            @else
                <span class="text-gray-200">---</span>
            @endif
        </td>

        <td class="px-8 py-6 text-center">
            @if($isTerlambat)
                <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-red-100 text-red-600 border border-red-200">
                    Terlambat
                </span>
            @else
                <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $loan->status == 'dipinjam' ? 'bg-amber-100 text-amber-600' : 'bg-green-100 text-green-600' }}">
                    {{ $loan->status }}
                </span>
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="px-8 py-20 text-center text-gray-300 font-black uppercase tracking-[0.2em] text-[10px]">
            Belum ada data transaksi peminjaman
        </td>
    </tr>
    @endforelse
</tbody>
