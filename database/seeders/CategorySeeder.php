<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Kiến trúc nội thất',
                'slug' => 'kien-truc-noi-that',
            ],
            [
                'name' => 'Kiến trúc ngoại thất',
                'slug' => 'kien-truc-ngoai-that',
            ],
            [
                'name' => 'Kiến trúc công trình',
                'slug' => 'kien-truc-cong-trinh',
            ],
            [
                'name' => 'Kiến trúc cảnh quan',
                'slug' => 'kien-truc-canh-quan',
            ],
            [
                'name' => 'Kiến trúc nghệ thuật',
                'slug' => 'kien-truc-nghe-thuat',
            ],
        ]);
    }
}
