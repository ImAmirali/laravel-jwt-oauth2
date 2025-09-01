<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $availabilityStatuses = ['موجود در انبار', 'کمبود موجودی', 'ناموجود', 'پیش سفارش', 'متوقف شده'];
        $warrantyOptions = [
            'گارانتی یک ساله',
            'گارانتی دو ساله',
            'گارانتی تمدیدی سه ساله',
            'گارانتی شش ماهه',
            'گارانتی مادام العمر',
            'بدون گارانتی'
        ];
        $shippingOptions = [
            'ارسال در ۲۴-۴۸ ساعت',
            'ارسال در ۳-۵ روز کاری',
            'ارسال در ۱-۲ هفته',
            'ارسال اکسپرس موجود است',
            'فقط ارسال استاندارد'
        ];
        $returnPolicies = [
            'مرجوعی ۳۰ روزه',
            'مرجوعی ۶۰ روزه',
            'مرجوعی ۹۰ روزه',
            'مرجوعی ۱۴ روزه',
            'عدم پذیرش مرجوعی',
        ];

        $persianElectronics = [
            [
                'name' => 'گوشی هوشمند سامسونگ گلکسی',
                'description' => 'گوشی هوشمند پیشرفته با صفحه نمایش AMOLED و دوربین 108 مگاپیکسلی. دارای پردازنده قدرتمند و باتری 5000 میلی آمپری.'
            ],
            [
                'name' => 'لپ تاپ ایسوس VivoBook',
                'description' => 'لپ تاپ سبک و قابل حمل با پردازنده Intel Core i7 و 16GB رم. مناسب برای کار و تحصیل با طراحی مدرن.'
            ],
            [
                'name' => 'تبلت اپل آیپد پرو',
                'description' => 'تبلت حرفه‌ای با تراشه M2 و صفحه نمایش Liquid Retina. ایده‌آل برای طراحی گرافیکی و کارهای هنری.'
            ],
            [
                'name' => 'هدفون بی‌سیم سونی WH-1000XM5',
                'description' => 'هدفون بی‌سیم با حذف نویز فعال و کیفیت صدای Hi-Res. باتری 30 ساعته و اتصال بلوتوث 5.2.'
            ],
            [
                'name' => 'اسپیکر هوشمند الکسا اکو',
                'description' => 'بلندگوی هوشمند با دستیار صوتی الکسا. قابلیت کنترل خانه هوشمند و پخش موزیک با کیفیت استریو.'
            ],
            [
                'name' => 'ساعت هوشمند اپل واچ سری 9',
                'description' => 'ساعت هوشمند با سنسورهای سلامتی پیشرفته و GPS دقیق. مقاوم در برابر آب و عمر باتری یک روزه.'
            ],
            [
                'name' => 'دوربین بدون آینه کانن EOS R6',
                'description' => 'دوربین حرفه‌ای بدون آینه با سنسور فول فریم 20 مگاپیکسلی. فیلمبرداری 4K و تثبیت کننده لرزش 5 محوره.'
            ],
            [
                'name' => 'کامپیوتر رومیزی گیمینگ ASUS ROG',
                'description' => 'سیستم گیمینگ قدرتمند با کارت گرافیک RTX 4080 و پردازنده AMD Ryzen 9. مناسب برای بازی‌های مدرن.'
            ],
            [
                'name' => 'مانیتور گیمینگ Samsung Odyssey',
                'description' => 'مانیتور گیمینگ منحنی 27 اینچی با نرخ تازه‌سازی 144Hz. تکنولوژی HDR و زمان واکنش 1ms.'
            ],
            [
                'name' => 'کیبورد گیمینگ مکانیکی لاجیتک',
                'description' => 'کیبورد مکانیکی با کلیدهای Cherry MX و نورپردازی RGB. طراحی مقاوم و مناسب برای بازی‌های حرفه‌ای.'
            ],
            [
                'name' => 'موس گیمینگ ریزر DeathAdder',
                'description' => 'موس گیمینگ ارگونومیک با DPI قابل تنظیم تا 20000. دکمه‌های قابل برنامه‌ریزی و نورپردازی LED.'
            ],
            [
                'name' => 'هارد اکسترنال وسترن دیجیتال',
                'description' => 'هارد خارجی 2 ترابایت با اتصال USB 3.0. سرعت انتقال بالا و طراحی ضد ضربه برای حفاظت اطلاعات.'
            ],
            [
                'name' => 'پاوربانک شیائومی 20000 میلی آمپر',
                'description' => 'شارژر همراه با ظرفیت بالا و شارژ سریع PD. دو پورت USB و یک پورت Type-C برای شارژ همزمان.'
            ],
            [
                'name' => 'روتر بی‌سیم ASUS AX6000',
                'description' => 'روتر وای‌فای 6 با سرعت تا 6000 مگابیت بر ثانیه. پوشش گسترده و قابلیت Mesh برای خانه‌های بزرگ.'
            ],
            [
                'name' => 'وب‌کم لاجیتک C920 Pro',
                'description' => 'وب‌کم HD 1080p با میکروفون استریو. ایده‌آل برای جلسات آنلاین و استریم با کیفیت تصویر بالا.'
            ],
            [
                'name' => 'چاپگر لیزری HP LaserJet',
                'description' => 'چاپگر لیزری تک رنگ با سرعت چاپ 28 صفحه در دقیقه. اتصال Wi-Fi و مناسب برای دفاتر کوچک.'
            ],
            [
                'name' => 'دستگاه بازی PlayStation 5',
                'description' => 'کنسول بازی نسل جدید با پردازنده گرافیکی قدرتمند. پشتیبانی از بازی‌های 4K و تکنولوژی ray tracing.'
            ],
            [
                'name' => 'ایربادز اپل پرو نسل سوم',
                'description' => 'هدفون بی‌سیم درون گوشی با حذف نویز فعال. باتری 6 ساعته و شارژ بی‌سیم در کیس.'
            ],
            [
                'name' => 'اسکنر اسناد Epson Expression',
                'description' => 'اسکنر تخت A4 با رزولوشن 4800 DPI. قابلیت اسکن عکس و اسناد با سرعت بالا و کیفیت حرفه‌ای.'
            ],
            [
                'name' => 'دستگاه VR Meta Quest 3',
                'description' => 'هدست واقعیت مجازی با پردازنده Snapdragon XR2. تجربه VR بدون سیم با مجموعه بازی‌های متنوع.'
            ]
        ];

        $products = [
            [
                'name' => 'گوشی هوشمند سامسونگ گلکسی',
                'description' => 'گوشی هوشمند پیشرفته با صفحه نمایش AMOLED و دوربین 108 مگاپیکسلی. دارای پردازنده قدرتمند و باتری 5000 میلی آمپری.'
            ],
            [
                'name' => 'لپ تاپ ایسوس VivoBook',
                'description' => 'لپ تاپ سبک و قابل حمل با پردازنده Intel Core i7 و 16GB رم. مناسب برای کار و تحصیل با طراحی مدرن.'
            ],
            [
                'name' => 'تبلت اپل آیپد پرو',
                'description' => 'تبلت حرفه‌ای با تراشه M2 و صفحه نمایش Liquid Retina. ایده‌آل برای طراحی گرافیکی و کارهای هنری.'
            ],
            [
                'name' => 'هدفون بی‌سیم سونی WH-1000XM5',
                'description' => 'هدفون بی‌سیم با حذف نویز فعال و کیفیت صدای Hi-Res. باتری 30 ساعته و اتصال بلوتوث 5.2.'
            ],
            [
                'name' => 'اسپیکر هوشمند الکسا اکو',
                'description' => 'بلندگوی هوشمند با دستیار صوتی الکسا. قابلیت کنترل خانه هوشمند و پخش موزیک با کیفیت استریو.'
            ],
            [
                'name' => 'ساعت هوشمند اپل واچ سری 9',
                'description' => 'ساعت هوشمند با سنسورهای سلامتی پیشرفته و GPS دقیق. مقاوم در برابر آب و عمر باتری یک روزه.'
            ],
            // Add all other products from the array above...
        ];

        return [
            'title' => fake()->words(rand(2, 5), true),
            'description' => fake()->sentence(rand(5, 10)),
            'price' => fake()->numberBetween(10, 9000) * 1000, // price in Toman
            'user_id' => User::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'brand_id' => Brand::inRandomOrder()->first()->id,
            'discount_percentage' => fake()->numberBetween(0, 100),
            'rating' => fake()->randomFloat(1, 0, 5),
            'stock' => fake()->numberBetween(0, 500),
            'sku' => strtoupper(fake()->unique()->bothify('??##??##')),
            'weight' => fake()->numberBetween(50, 5000), // Weight in grams
            'warranty_information' => fake()->optional(0.9)->randomElement($warrantyOptions),
            'shipping_information' => fake()->optional(0.9)->randomElement($shippingOptions),
            'availability_status' => fake()->randomElement($availabilityStatuses),
            'return_policy' => fake()->optional(0.8)->randomElement($returnPolicies),
            'minimum_order_quantity' => fake()->numberBetween(1, 10),
            'barcode' => fake()->optional(0.7)->ean13(),
            'qr_code' => fake()->optional(0.6)->url(),
            'thumbnail' => fake()->optional(0.8)->imageUrl(300, 300, 'products', true),
        ];
    }

    /**
     * Configure the model factory to create a product with high stock.
     */
    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(50, 500),
            'availability_status' => 'موجود در انبار',
        ]);
    }

    /**
     * Configure the model factory to create a product with low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 10),
            'availability_status' => 'کمبود موجودی',
        ]);
    }

    /**
     * Configure the model factory to create an out of stock product.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'availability_status' => 'ناموجود',
        ]);
    }

    /**
     * Configure the model factory to create a discounted product.
     */
    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_percentage' => fake()->numberBetween(10, 70),
        ]);
    }

    /**
     * Configure the model factory to create a highly rated product.
     */
    public function highRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->randomFloat(1, 4.0, 5.0),
        ]);
    }

    /**
     * Configure the model factory to create a premium product.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->numberBetween(50000, 200000), // $500-$2000
            'warranty_information' => '3 years extended warranty',
            'rating' => fake()->randomFloat(1, 4.0, 5.0),
        ]);
    }
}
