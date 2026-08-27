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
if [ -d "vendor" ]; then
    zip -r vendor.zip vendor/ -q
    echo "✅ vendor.zip berhasil dibuat!"
fi

# Kompres hasil build Vite (biasanya berada di public/build)
# Kompres hasil build Vite (folder build/ langsung tanpa prefix public/)
if [ -d "public/build" ]; then
    zip -r build.zip public/build/ -q
    echo "✅ build.zip berhasil dibuat!"
    (cd public && zip -r ../build.zip build/ -q)
    echo "✅ build.zip berhasil dibuat (berisi folder 'build/')!"
fi

# Kembalikan dependencies dev di lokal
composer install -q

echo "🎉 Proses build selesai!"
echo "👉 Selanjutnya: Buka File Manager cPanel, masuk ke repositories/cinelog/, drag & drop file .zip yang baru saja dibuat, lalu Extract."
echo "👉 Selanjutnya: Buka File Manager cPanel, masuk ke folder public/ (atau public_html/), upload build.zip lalu klik Extract."
