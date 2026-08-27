<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Owner Admin
        $owner = User::firstOrCreate(
            ['email' => 'admin@cinelog.vault'],
            [
                'name' => 'Alex Pratama',
                'username' => 'alexcinephile',
                'password' => Hash::make('password123'),
                'bio' => 'Cinephile & Sci-Fi enthusiast. Mendokumentasikan perjalanan menonton dan ulasan personal untuk film & serial layar kaca.',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=400&auto=format&fit=crop',
                'tmdb_api_key' => null,
                'is_setup_completed' => true,
            ]
        );

        // 2. Seed Genres
        $genreList = [
            'Sci-Fi', 'Action', 'Drama', 'Thriller', 'Horror', 'Animation',
            'Adventure', 'Crime', 'Mystery', 'Comedy', 'Fantasy', 'Romance',
        ];

        $genres = [];
        foreach ($genreList as $name) {
            $genres[$name] = Genre::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // 3. Sample Masterpiece Movie 1: Dune: Part Two
        $dune2 = MovieSeries::firstOrCreate(
            ['slug' => 'dune-part-two-2024'],
            [
                'tmdb_id' => 693134,
                'type' => 'movie',
                'title' => 'Dune: Part Two',
                'original_title' => 'Dune: Part Two',
                'release_year' => 2024,
                'release_date' => '2024-03-01',
                'synopsis' => 'Paul Atreides bersatu dengan Chani dan suku Fremen untuk membalas dendam terhadap para konspirator yang telah menghancurkan keluarganya. Menghadapi pilihan antara cinta dalam hidupnya dan nasib alam semesta.',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'backdrop_url' => 'https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg',
                'director' => 'Denis Villeneuve',
                'cast_members' => [
                    ['name' => 'Timothée Chalamet', 'character' => 'Paul Atreides'],
                    ['name' => 'Zendaya', 'character' => 'Chani'],
                    ['name' => 'Rebecca Ferguson', 'character' => 'Lady Jessica'],
                    ['name' => 'Austin Butler', 'character' => 'Feyd-Rautha'],
                ],
                'runtime_minutes' => 166,
                'original_language' => 'EN',
                'is_custom_entry' => false,
            ]
        );
        $dune2->genres()->sync([$genres['Sci-Fi']->id, $genres['Adventure']->id, $genres['Drama']->id]);

        Review::firstOrCreate(
            ['movie_series_id' => $dune2->id, 'user_id' => $owner->id],
            [
                'rating_overall' => 10.0,
                'rating_story' => 10.0,
                'rating_visual' => 10.0,
                'rating_acting' => 9.5,
                'rating_audio' => 10.0,
                'headline' => 'Standar baru sinema Sci-Fi modern dengan audio visual yang menggelegar.',
                'review_content' => "Denis Villeneuve berhasil membuktikan kapasitasnya sebagai sutradara visioner terhebat abad ini. *Dune: Part Two* bukan sekadar film, melainkan sebuah pengalaman sinematik yang langka.\n\n### Yang Bekerja dengan Sempurna:\n- **Skala & Sinematografi Greig Fraser**: Setiap frame terasa megah, dari hamparan gurun Arrakis hingga arsitektur monolitik Giedi Prime dalam format hitam-putih inframerah yang memukau.\n- **Audio Design & Hans Zimmer**: Gemuruh cacing pasir (*Shai-Hulud*) dan instrumen vokal etnik menciptakan tensi tanpa henti.\n- **Karakter Feyd-Rautha**: Austin Butler menyajikan sosok antagonis yang intimidatif dan mengerikan.",
                'favorite_quote' => 'May thy knife chip and shatter.',
                'is_spoiler' => false,
                'is_favorite' => true,
                'watch_platform' => 'IMAX XXI Gandaria City',
                'watched_date' => '2024-03-02',
                'rewatch_count' => 2,
                'is_published' => true,
            ]
        );

        // 4. Sample Movie 2: Interstellar
        $interstellar = MovieSeries::firstOrCreate(
            ['slug' => 'interstellar-2014'],
            [
                'tmdb_id' => 157336,
                'type' => 'movie',
                'title' => 'Interstellar',
                'original_title' => 'Interstellar',
                'release_year' => 2014,
                'release_date' => '2014-11-05',
                'synopsis' => 'Di masa depan ketika Bumi perlahan tak layak huni, sekelompok penjelajah melintasi lubang cacing (wormhole) dekat Saturnus untuk mencari planet baru yang dapat menopang kelangsungan hidup umat manusia.',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'backdrop_url' => 'https://image.tmdb.org/t/p/w1280/rAiYT5KGqDCRIIqo664sY9XZXbt.jpg',
                'director' => 'Christopher Nolan',
                'cast_members' => [
                    ['name' => 'Matthew McConaughey', 'character' => 'Cooper'],
                    ['name' => 'Anne Hathaway', 'character' => 'Brand'],
                    ['name' => 'Jessica Chastain', 'character' => 'Murph'],
                ],
                'runtime_minutes' => 169,
                'original_language' => 'EN',
            ]
        );
        $interstellar->genres()->sync([$genres['Sci-Fi']->id, $genres['Drama']->id]);

        Review::firstOrCreate(
            ['movie_series_id' => $interstellar->id, 'user_id' => $owner->id],
            [
                'rating_overall' => 9.6,
                'rating_story' => 9.8,
                'rating_visual' => 10.0,
                'rating_acting' => 9.6,
                'rating_audio' => 10.0,
                'headline' => 'Kombinasi sains relativitas dan ikatan emosional ayah-anak yang tak lekang waktu.',
                'review_content' => "Film yang selalu berhasil membuat merinding di setiap kali tontonan ulang. Nolan menggabungkan teori gravitasi, black hole (*Gargantua*), dan konsep cinta sebagai dimensi yang mampu melintasi ruang dan waktu.\n\nSoundtrack organ pipa karya Hans Zimmer (*Cornfield Chase, No Time for Caution*) menjadi salah satu soundtrack terbaik sepanjang sejarah perfilman.",
                'favorite_quote' => 'Love is the one thing we\'re capable of perceiving that transcends dimensions of time and space.',
                'is_spoiler' => false,
                'is_favorite' => true,
                'watch_platform' => 'Blu-ray 4K',
                'watched_date' => '2024-01-15',
                'rewatch_count' => 5,
                'is_published' => true,
            ]
        );

        // 5. Sample Series 1: Arcane
        $arcane = MovieSeries::firstOrCreate(
            ['slug' => 'arcane-2021'],
            [
                'tmdb_id' => 94605,
                'type' => 'anime',
                'title' => 'Arcane',
                'original_title' => 'Arcane',
                'release_year' => 2021,
                'release_date' => '2021-11-06',
                'synopsis' => 'Di tengah ketegangan antara kota utopia Piltover dan kota bawah tanah Zaun yang tertindas, dua saudari Vi dan Jinx berjuang di sisi yang berlawanan dalam perang sihir teknologi.',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/fqldf2t8ztc9aiwn397FvFeNz9H.jpg',
                'backdrop_url' => 'https://image.tmdb.org/t/p/w1280/vI0FpG8c1bS3Yg9MtzjA7n7X0oO.jpg',
                'director' => 'Christian Linke, Alex Yee',
                'total_seasons' => 2,
                'total_episodes' => 18,
                'original_language' => 'EN',
            ]
        );
        $arcane->genres()->sync([$genres['Animation']->id, $genres['Sci-Fi']->id, $genres['Action']->id, $genres['Drama']->id]);

        Review::firstOrCreate(
            ['movie_series_id' => $arcane->id, 'user_id' => $owner->id],
            [
                'rating_overall' => 9.8,
                'rating_story' => 9.6,
                'rating_visual' => 10.0,
                'rating_acting' => 9.8,
                'rating_audio' => 10.0,
                'headline' => 'Mahakarya animasi global dengan estetika lukisan bergerak yang revolusioner.',
                'review_content' => 'Fortiche Studio dan Riot Games berhasil menciptakan serial animasi terbaik dalam satu dekade terakhir. Penulisan karakter Jinx dan Silco begitu tragis dan manusiawi.',
                'favorite_quote' => 'In the pursuit of great, we failed to do good.',
                'is_spoiler' => false,
                'is_favorite' => true,
                'watch_platform' => 'Netflix',
                'watched_date' => '2024-02-10',
                'rewatch_count' => 1,
                'is_published' => true,
            ]
        );

        // 6. Sample Series 2 (In Watchlist): Shogun
        $shogun = MovieSeries::firstOrCreate(
            ['slug' => 'shogun-2024'],
            [
                'tmdb_id' => 126308,
                'type' => 'series',
                'title' => 'Shōgun',
                'original_title' => 'Shōgun',
                'release_year' => 2024,
                'synopsis' => 'Di Jepang abad ke-17 pada awal perang saudara yang menentukan abad tersebut, Lord Yoshii Toranaga berjuang demi kelangsungan hidupnya ketika para musuhnya di Dewan Bupati bersatu melawannya.',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/7O4iVfOMQmdCSxhOg1WnzG1AgYT.jpg',
                'total_seasons' => 1,
                'total_episodes' => 10,
            ]
        );
        $shogun->genres()->sync([$genres['Drama']->id, $genres['Adventure']->id]);

        Watchlist::firstOrCreate(
            ['movie_series_id' => $shogun->id, 'user_id' => $owner->id],
            [
                'status' => 'watching',
                'current_season' => 1,
                'current_episode' => 7,
                'priority' => 'high',
                'watch_platform' => 'Disney+ Hotstar',
                'notes' => 'Sangat intens intrik politiknya. Akting Hiroyuki Sanada spektakuler.',
            ]
        );

        // 7. Sample Movie 3 (Plan to watch in Watchlist): Severance
        $severance = MovieSeries::firstOrCreate(
            ['slug' => 'severance-2022'],
            [
                'tmdb_id' => 95396,
                'type' => 'series',
                'title' => 'Severance',
                'original_title' => 'Severance',
                'release_year' => 2022,
                'synopsis' => 'Mark memimpin tim karyawan kantor yang ingatannya telah dipisahkan secara bedah antara ingatan pekerjaan dan ingatan kehidupan pribadi.',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/jtAh65TX999U6kI09nvNWf25Oi0.jpg',
                'total_seasons' => 2,
                'total_episodes' => 19,
            ]
        );
        $severance->genres()->sync([$genres['Sci-Fi']->id, $genres['Thriller']->id, $genres['Mystery']->id]);

        Watchlist::firstOrCreate(
            ['movie_series_id' => $severance->id, 'user_id' => $owner->id],
            [
                'status' => 'plan_to_watch',
                'current_season' => 1,
                'current_episode' => 0,
                'priority' => 'high',
                'watch_platform' => 'Apple TV+',
                'notes' => 'Nunggu Season 2 tamat baru marathon.',
            ]
        );
    }
}
