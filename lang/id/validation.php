<?php

return [

    'accepted' => ':attribute harus diterima.',
    'accepted_if' => ':attribute harus diterima ketika :other adalah :value.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus berupa tanggal setelah :date.',
    'after_or_equal' => ':attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'any_of' => ':attribute harus merupakan salah satu dari nilai yang diizinkan.',
    'array' => ':attribute harus berupa array.',
    'array_keys' => ':attribute harus memiliki kunci yang valid.',
    'ascii' => ':attribute hanya boleh berisi karakter ASCII.',
    'before' => ':attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus berupa tanggal sebelum atau sama dengan :date.',

    'between' => [
        'array' => ':attribute harus memiliki antara :min dan :max item.',
        'file' => ':attribute harus berukuran antara :min dan :max kilobyte.',
        'numeric' => ':attribute harus bernilai antara :min dan :max.',
        'string' => ':attribute harus memiliki antara :min dan :max karakter.',
    ],

    'boolean' => ':attribute harus berupa benar atau salah.',
    'can' => ':attribute berisi nilai yang tidak diizinkan.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'contains' => ':attribute harus mengandung nilai yang diperlukan.',
    'current_password' => 'Kata sandi saat ini salah.',

    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => ':attribute tidak sesuai dengan format :format.',
    'decimal' => ':attribute harus memiliki :decimal angka di belakang koma.',
    'declined' => ':attribute harus ditolak.',
    'declined_if' => ':attribute harus ditolak ketika :other adalah :value.',
    'different' => ':attribute harus berbeda dengan :other.',
    'digits' => ':attribute harus terdiri dari :digits digit.',
    'digits_between' => ':attribute harus terdiri dari :min hingga :max digit.',
    'dimensions' => 'Ukuran gambar :attribute tidak valid.',
    'distinct' => ':attribute memiliki nilai yang duplikat.',
    'doesnt_contain' => ':attribute tidak boleh mengandung salah satu dari nilai berikut: :values.',
    'doesnt_end_with' => ':attribute tidak boleh diakhiri dengan salah satu dari nilai berikut: :values.',
    'doesnt_start_with' => ':attribute tidak boleh diawali dengan salah satu dari nilai berikut: :values.',

    'email' => ':attribute harus berupa alamat email yang valid.',
    'encoding' => ':attribute harus menggunakan encoding :encoding.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu dari nilai berikut: :values.',
    'enum' => ':attribute yang dipilih tidak valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'extensions' => ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => ':attribute harus berupa file.',
    'filled' => ':attribute wajib memiliki nilai.',

    'gt' => [
        'array' => ':attribute harus memiliki lebih dari :value item.',
        'file' => ':attribute harus berukuran lebih dari :value kilobyte.',
        'numeric' => ':attribute harus lebih besar dari :value.',
        'string' => ':attribute harus memiliki lebih dari :value karakter.',
    ],

    'gte' => [
        'array' => ':attribute harus memiliki :value item atau lebih.',
        'file' => ':attribute harus berukuran minimal :value kilobyte.',
        'numeric' => ':attribute harus lebih besar dari atau sama dengan :value.',
        'string' => ':attribute harus memiliki minimal :value karakter.',
    ],

    'hex_color' => ':attribute harus berupa warna hexadecimal yang valid.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute harus terdapat di dalam :other.',
    'in_array_keys' => ':attribute harus mengandung kunci yang ditentukan.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus berupa alamat IP yang valid.',
    'ipv4' => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus berupa alamat IPv6 yang valid.',
    'json' => ':attribute harus berupa JSON yang valid.',
    'list' => ':attribute harus berupa daftar.',

    'lowercase' => ':attribute harus menggunakan huruf kecil.',

    'lt' => [
        'array' => ':attribute harus memiliki kurang dari :value item.',
        'file' => ':attribute harus berukuran kurang dari :value kilobyte.',
        'numeric' => ':attribute harus kurang dari :value.',
        'string' => ':attribute harus memiliki kurang dari :value karakter.',
    ],

    'lte' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :value item.',
        'file' => ':attribute harus berukuran maksimal :value kilobyte.',
        'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
        'string' => ':attribute harus memiliki maksimal :value karakter.',
    ],

    'mac_address' => ':attribute harus berupa alamat MAC yang valid.',

    'max' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :max item.',
        'file' => ':attribute tidak boleh berukuran lebih dari :max kilobyte.',
        'numeric' => ':attribute tidak boleh lebih besar dari :max.',
        'string' => ':attribute tidak boleh memiliki lebih dari :max karakter.',
    ],

    'max_digits' => ':attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => ':attribute harus berupa file dengan tipe: :values.',
    'mimetypes' => ':attribute harus berupa file dengan tipe MIME: :values.',

    'min' => [
        'array' => ':attribute harus memiliki setidaknya :min item.',
        'file' => ':attribute harus berukuran setidaknya :min kilobyte.',
        'numeric' => ':attribute harus bernilai setidaknya :min.',
        'string' => ':attribute harus memiliki setidaknya :min karakter.',
    ],

    'min_digits' => ':attribute harus memiliki setidaknya :min digit.',
    'missing' => 'Kolom :attribute harus tidak ada.',
    'missing_if' => 'Kolom :attribute harus tidak ada ketika :other adalah :value.',
    'missing_unless' => 'Kolom :attribute harus tidak ada kecuali :other adalah :value.',
    'missing_with' => 'Kolom :attribute harus tidak ada ketika :values tersedia.',
    'missing_with_all' => 'Kolom :attribute harus tidak ada ketika semua :values tersedia.',
    'multiple_of' => ':attribute harus merupakan kelipatan dari :value.',
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus berupa angka.',

    'password' => [
        'letters' => ':attribute harus mengandung setidaknya satu huruf.',
        'mixed' => ':attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => ':attribute harus mengandung setidaknya satu angka.',
        'symbols' => ':attribute harus mengandung setidaknya satu simbol.',
        'uncompromised' => 'Nilai :attribute ditemukan dalam kebocoran data. Silakan gunakan :attribute yang berbeda.',
    ],

    'present' => 'Kolom :attribute harus tersedia.',
    'present_if' => 'Kolom :attribute harus tersedia ketika :other adalah :value.',
    'present_unless' => 'Kolom :attribute harus tersedia kecuali :other adalah :value.',
    'present_with' => 'Kolom :attribute harus tersedia ketika :values tersedia.',
    'present_with_all' => 'Kolom :attribute harus tersedia ketika semua :values tersedia.',

    'prohibited' => 'Kolom :attribute dilarang.',
    'prohibited_if' => 'Kolom :attribute dilarang ketika :other adalah :value.',
    'prohibited_if_accepted' => 'Kolom :attribute dilarang ketika :other diterima.',
    'prohibited_if_declined' => 'Kolom :attribute dilarang ketika :other ditolak.',
    'prohibited_unless' => 'Kolom :attribute dilarang kecuali :other termasuk dalam :values.',
    'prohibits' => 'Kolom :attribute melarang :other untuk tersedia.',

    'regex' => 'Format :attribute tidak valid.',

    'required' => 'Kolom :attribute wajib diisi.',
    'required_array_keys' => 'Kolom :attribute harus memiliki entri untuk: :values.',
    'required_if' => 'Kolom :attribute wajib diisi ketika :other adalah :value.',
    'required_if_accepted' => 'Kolom :attribute wajib diisi ketika :other diterima.',
    'required_if_declined' => 'Kolom :attribute wajib diisi ketika :other ditolak.',
    'required_unless' => 'Kolom :attribute wajib diisi kecuali :other termasuk dalam :values.',
    'required_with' => 'Kolom :attribute wajib diisi ketika :values tersedia.',
    'required_with_all' => 'Kolom :attribute wajib diisi ketika semua :values tersedia.',
    'required_without' => 'Kolom :attribute wajib diisi ketika :values tidak tersedia.',
    'required_without_all' => 'Kolom :attribute wajib diisi ketika semua :values tidak tersedia.',

    'same' => ':attribute dan :other harus sama.',

    'size' => [
        'array' => ':attribute harus memiliki :size item.',
        'file' => ':attribute harus berukuran :size kilobyte.',
        'numeric' => ':attribute harus bernilai :size.',
        'string' => ':attribute harus terdiri dari :size karakter.',
    ],

    'starts_with' => ':attribute harus diawali dengan salah satu dari nilai berikut: :values.',
    'string' => ':attribute harus berupa teks.',
    'timezone' => ':attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'uppercase' => ':attribute harus menggunakan huruf kapital.',
    'url' => ':attribute harus berupa URL yang valid.',
    'ulid' => ':attribute harus berupa ULID yang valid.',
    'uuid' => ':attribute harus berupa UUID yang valid.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'Pesan validasi khusus.',
        ],
    ],

    'attributes' => [
        'address' => 'alamat',
        'amount' => 'nominal',
        'branch' => 'cabang',
        'code' => 'kode autentikasi',
        'contract' => 'nomor kontrak',
        'current_password' => 'kata sandi saat ini',
        'email' => 'alamat email',
        'file' => 'file',
        'genre' => 'genre',
        'name' => 'nama',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'phone' => 'nomor telepon',
        'status' => 'status',
        'title' => 'judul',
        'username' => 'nama pengguna',
    ],

];
