# Fitur Forgot Password - Management Project

## Deskripsi
Sistem forgot password (lupa password) telah berhasil diimplementasikan untuk memungkinkan pengguna mereset password mereka jika lupa.

## Fitur yang Ditambahkan

### 1. **Halaman Forgot Password**
   - URL: `/forgot-password`
   - User memasukkan email mereka
   - Sistem mengirim link reset password ke email

### 2. **Halaman Reset Password**
   - URL: `/reset-password/{token}`
   - User memasukkan password baru
   - Token valid selama 60 menit

### 3. **Email Notification**
   - Email otomatis dikirim dengan link reset password
   - Link berlaku selama 60 menit
   - Template email yang profesional

## File yang Dibuat/Dimodifikasi

### File Baru:
1. **Migration**: `database/migrations/2024_01_08_000001_create_password_reset_tokens_table.php`
   - Membuat tabel untuk menyimpan token reset password

2. **Views**:
   - `resources/views/auth/forgot-password.blade.php` - Form untuk request reset password
   - `resources/views/auth/reset-password.blade.php` - Form untuk set password baru

3. **Notification**: `app/Notifications/ResetPasswordNotification.php`
   - Notification class untuk mengirim email reset password

### File yang Dimodifikasi:
1. **Controller**: `app/Http/Controllers/Auth/AuthController.php`
   - Menambahkan method:
     - `showForgotPasswordForm()` - Menampilkan form forgot password
     - `sendResetLink()` - Mengirim link reset ke email
     - `showResetPasswordForm()` - Menampilkan form reset password
     - `resetPassword()` - Memproses reset password

2. **Routes**: `routes/web.php`
   - Menambahkan routes:
     - `GET /forgot-password` - Halaman forgot password
     - `POST /forgot-password` - Submit forgot password
     - `GET /reset-password/{token}` - Halaman reset password
     - `POST /reset-password` - Submit reset password

3. **View**: `resources/views/auth/login.blade.php`
   - Menambahkan link "Lupa password?" di halaman login

## Cara Menggunakan

### Untuk User:
1. Buka halaman login
2. Klik link "Lupa password?"
3. Masukkan email yang terdaftar
4. Cek email untuk link reset password
5. Klik link di email (valid 60 menit)
6. Masukkan password baru
7. Klik "Reset Password"
8. Login dengan password baru

### Untuk Developer:

#### Testing dengan Mailtrap atau Log:
Secara default, email akan dikirim menggunakan driver 'log' (cek di `storage/logs/laravel.log`).

Untuk testing dengan Mailtrap:
1. Daftar di [Mailtrap.io](https://mailtrap.io)
2. Update `.env`:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@managementproject.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

#### Testing dengan Gmail SMTP:
Update `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Note**: Untuk Gmail, gunakan App Password, bukan password biasa.

## Keamanan

1. **Token Expiration**: Token reset password kedaluwarsa setelah 60 menit
2. **One-time Use**: Token dihapus setelah berhasil digunakan
3. **Hashed Token**: Token disimpan dalam bentuk hash di database
4. **Email Validation**: Hanya email yang terdaftar yang bisa request reset
5. **Activity Log**: Setiap reset password dicatat dalam activity log

## Troubleshooting

### Email tidak terkirim:
1. Periksa konfigurasi MAIL di `.env`
2. Cek log di `storage/logs/laravel.log`
3. Pastikan MAIL_MAILER diset dengan benar
4. Jika menggunakan Gmail, pastikan "Less secure app access" aktif atau gunakan App Password

### Token tidak valid:
1. Pastikan token belum expired (< 60 menit)
2. Token hanya bisa digunakan sekali
3. Pastikan email sesuai dengan yang direquest

### Link reset tidak berfungsi:
1. Pastikan APP_URL di `.env` sudah benar
2. Pastikan routes sudah terdaftar: `php artisan route:list --name=password`

## Testing Routes

Untuk melihat semua routes password reset:
```bash
php artisan route:list --name=password
```

Output:
```
GET|HEAD   forgot-password ......... password.request
POST       forgot-password ......... password.email
GET|HEAD   reset-password/{token} .. password.reset
POST       reset-password .......... password.update
```
