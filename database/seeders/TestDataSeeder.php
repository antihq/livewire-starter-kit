<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $team = \App\Models\Team::where('name', 'Personal')->first();
        $creator = \App\Models\User::where('email', 'test@example.com')->first();

        $marketplaceId = DB::table('marketplaces')->insertGetId([
            'team_id' => $team->id,
            'creator_id' => $creator->id,
            'name' => 'Artisan Collective',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('marketplaces')->insert([
            [
                'team_id' => $team->id,
                'creator_id' => $creator->id,
                'name' => 'Vintage Finds',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $team->id,
                'creator_id' => $creator->id,
                'name' => 'Style Exchange',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $team->id,
                'creator_id' => $creator->id,
                'name' => 'Creative Corner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $team->id,
                'creator_id' => $creator->id,
                'name' => 'Home & Garden Hub',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $team->id,
                'creator_id' => $creator->id,
                'name' => 'Kid\'s Closet',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $team->id,
                'creator_id' => $creator->id,
                'name' => 'Tech Trade',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $listings = [
            [
                'title' => 'Handwoven Silver Chain Necklace with Labradorite Pendant',
                'description' => 'This stunning necklace features a handwoven sterling silver chain with a beautiful labradorite pendant. The chain measures 18 inches and the labradorite stone is approximately 1 inch in diameter. Each piece is carefully crafted in my studio using traditional wire-wrapping techniques. The labradorite displays beautiful flashes of blue and green when the light hits it. Perfect for everyday wear or special occasions. Finished with a sterling silver lobster clasp.',
            ],
            [
                'title' => 'Hand-Thrown Ceramic Coffee Mug - Speckled Stoneware',
                'description' => 'Handcrafted ceramic mug perfect for your morning coffee or tea. Made from durable speckled stoneware clay and glazed in a warm cream color with subtle speckles. Each mug is thrown on the wheel and trimmed by hand. The comfortable handle and generous 12-ounce capacity make it ideal for daily use. Food-safe, dishwasher, and microwave safe. Each piece is unique and may vary slightly from the photos.',
            ],
            [
                'title' => 'Hand-Knitted Merino Wool Scarf in Forest Green',
                'description' => 'Luxurious hand-knitted scarf made from 100% extra fine merino wool. This soft, warm scarf measures 8 inches wide by 72 inches long with subtle ribbing and fringe details. The forest green color is perfect for autumn and winter styling. The merino wool is incredibly soft against the skin and provides excellent warmth without bulk. Hand wash in cold water and lay flat to dry to maintain its shape and softness.',
            ],
            [
                'title' => 'Hand-Carved Cherry Wood Serving Spoon',
                'description' => 'Beautiful hand-carved serving spoon crafted from locally sourced cherry wood. This elegant spoon measures 12 inches in length, perfect for serving salads, rice, or side dishes. The wood has been finished with food-safe mineral oil and beeswax for a smooth, durable finish. Each spoon is unique due to the natural variations in the wood grain. Hand wash only and occasionally reapply mineral oil to maintain the finish.',
            ],
            [
                'title' => 'Lavender & Eucalyptus Soy Wax Candle - 8oz Tin',
                'description' => 'Natural soy wax candle infused with pure lavender and eucalyptus essential oils. Hand-poured in small batches in an 8-ounce reusable tin. This calming blend is perfect for relaxation and stress relief. Burns approximately 50 hours with a clean, even burn. Made with 100% natural soy wax, cotton wick, and pure essential oils. No synthetic fragrances or dyes. Trim wick to 1/4 inch before each use for best results.',
            ],
            [
                'title' => 'Original Watercolor Botanical Print - Wildflower Meadow',
                'description' => 'Original watercolor painting featuring a beautiful wildflower meadow. Painted on archival-quality 140lb cold-press watercolor paper using professional-grade pigments. The artwork measures 9x12 inches and is signed by the artist. This piece captures the delicate beauty of native wildflowers with vibrant colors and fine details. Perfect for framing and adding a touch of nature to your home. Comes unframed, carefully packaged in a protective sleeve.',
            ],
            [
                'title' => 'Hand-Stitched Leather Wallet - Natural Veg-Tan',
                'description' => 'Classic bifold wallet crafted from 4-5 ounce vegetable-tanned leather. Features 6 card slots, 2 bill compartments, and a hidden pocket. All edges are hand-burnished and sewn with waxed thread for durability. The natural leather will develop a beautiful patina over time. Measures 4.25 x 3.5 inches when closed. Each wallet is made to order and may take 1-2 weeks to ship. Perfect for those who appreciate quality craftsmanship and natural materials.',
            ],
            [
                'title' => 'Handmade Lavender Soap Bar with Oatmeal',
                'description' => 'All-natural handmade soap bar made with nourishing oils, real lavender buds, and colloidal oatmeal. Each bar weighs approximately 4.5-5 ounces and is cut by hand. The lavender provides a calming scent while the oatmeal gently exfoliates and soothes the skin. Made using the cold process method with olive oil, coconut oil, shea butter, and castor oil. No synthetic fragrances, dyes, or harsh detergents. Vegan and cruelty-free.',
            ],
            [
                'title' => 'Macramé Wall Hanging - Geometric Pattern',
                'description' => 'Modern macramé wall hanging featuring a geometric diamond pattern. Handcrafted using 100% cotton rope in natural cream color. Measures approximately 24 inches wide by 36 inches long including the fringe. Includes a wooden dowel for easy hanging. This piece adds texture and bohemian style to any room. Perfect for living rooms, bedrooms, or nurseries. Each piece is handmade and may have slight variations.',
            ],
            [
                'title' => 'Handmade Leather Dog Collar with Brass Hardware',
                'description' => 'Premium dog collar made from full-grain leather and solid brass hardware. Available in multiple sizes (S, M, L) to fit your pup perfectly. The leather becomes softer and more comfortable with wear while maintaining its strength. Features a sturdy brass buckle and D-ring for leash attachment. Each collar is handcrafted and can be personalized with engraved nameplates. Made to order, please allow 3-5 business days for production.',
            ],
        ];

        foreach ($listings as $listing) {
            DB::table('listings')->insert([
                'team_id' => $team->id,
                'marketplace_id' => $marketplaceId,
                'creator_id' => $creator->id,
                'title' => $listing['title'],
                'description' => $listing['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Test data seeded successfully!');
    }
}
