#!/bin/bash

echo "🚀 Memulai proses build cinelog..."

# 1. Build Backend (PHP)
echo "📦 Menjalankan composer install..."
composer install --optimize-autoloader --no-dev

# 2. Build Frontend (Node.js/Vite)
echo "🎨 Menjalankan npm install & build..."
npm install
npm run build

# 3. Kompresi Folder
echo "🗜️ Mengompres direktori menjadi .zip..."
rm -f vendor.zip build.zip # Hapus sisa zip sebelumnya jika ada

# Kompres vendor
zip -r vendor.zip vendor/ -q
echo "✅ vendor.zip berhasil dibuat!"

# Kompres hasil build Vite (biasanya berada di public/build)
if [ -d "public/build" ]; then
    zip -r build.zip public/build/ -q
    echo "✅ build.zip berhasil dibuat!"
fi

echo "🎉 Proses build selesai!"
echo "👉 Selanjutnya: Buka File Manager cPanel, masuk ke repositories/cinelog/, drag & drop file .zip yang baru saja dibuat, lalu Extract."
