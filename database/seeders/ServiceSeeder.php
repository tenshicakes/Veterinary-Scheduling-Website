<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['servicename' => 'Home Service', 'price' => 650.00, 'isactive' => true],
            ['servicename' => 'Face to Face Consultation', 'price' => 400.00, 'isactive' => true],
            ['servicename' => 'Online Consultation', 'price' => 500.00, 'isactive' => true],
            ['servicename' => 'Vet Health Certificate', 'price' => 500.00, 'isactive' => true],
            ['servicename' => 'Spay and Castration', 'price' => 1250.00, 'isactive' => true],
            ['servicename' => 'Tick and Flea', 'price' => 400.00, 'isactive' => true],
            ['servicename' => 'Laboratory Test', 'price' => 670.00, 'isactive' => true],
            ['servicename' => 'Smear Test', 'price' => 250.00, 'isactive' => true],
            ['servicename' => 'Dog - Anti-rabies Vaxination', 'price' => 360.00, 'isactive' => true],
            ['servicename' => 'Dog - 5-in-1 Vaxination', 'price' => 570.00, 'isactive' => true],
            ['servicename' => 'Dog - 6-in-1 Vaxination', 'price' => 620.00, 'isactive' => true],
            ['servicename' => 'Dog - 8-in-1 Vaxination', 'price' => 680.00, 'isactive' => true],
            ['servicename' => 'Dog - Kennel Cough Vaxination', 'price' => 650.00, 'isactive' => true],
            ['servicename' => 'Dog - Deworm 5KG below', 'price' => 225.00, 'isactive' => true],
            ['servicename' => 'Cat - 4-in-1 Vaxination', 'price' => 875.00, 'isactive' => true],
            ['servicename' => 'Cat - Anti-rabies Vaxination', 'price' => 360.00, 'isactive' => true],
            ['servicename' => 'Cat - Deworm 5KG below', 'price' => 225.00, 'isactive' => true],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}