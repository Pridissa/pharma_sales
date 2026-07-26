<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Requisition;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Admin & 2 Separate Cashiers/Sellers)
        $admin = User::create([
            'name' => 'Dr. Admin Pharmaceutique',
            'email' => 'admin@pharmacy.com',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'phone' => '+243 80 000 0001',
            'is_active' => true,
        ]);

        $caissier1 = User::create([
            'name' => 'Caissier Principal (Vendeur 1)',
            'email' => 'caisse@pharmacy.com',
            'password' => Hash::make('Password123!'),
            'role' => 'caissier',
            'phone' => '+243 80 000 0002',
            'is_active' => true,
        ]);

        $caissier2 = User::create([
            'name' => 'Jean-Marc (Vendeur 2)',
            'email' => 'vendeur2@pharmacy.com',
            'password' => Hash::make('Password123!'),
            'role' => 'caissier',
            'phone' => '+243 80 000 0003',
            'is_active' => true,
        ]);

        // 2. Seed Categories
        $categoriesData = [
            [
                'name' => 'Antalgiques & Anti-pyrétiques',
                'slug' => 'antalgiques-anti-pyretiques',
                'description' => 'Médicaments contre la douleur et la fièvre.',
            ],
            [
                'name' => 'Antibiotiques & Anti-infectieux',
                'slug' => 'antibiotiques-anti-infectieux',
                'description' => 'Traitement des infections bactériennes.',
            ],
            [
                'name' => 'Anti-inflammatoires',
                'slug' => 'anti-inflammatoires',
                'description' => 'Réduction des inflammations et douleurs associées.',
            ],
            [
                'name' => 'Gastro-entérologie',
                'slug' => 'gastro-enterologie',
                'description' => 'Soins du système digestif et de l stomac.',
            ],
            [
                'name' => 'Vitamines & Compléments',
                'slug' => 'vitamines-complements',
                'description' => 'Apport en vitamines, minéraux et toniques.',
            ],
            [
                'name' => 'Matériel Médical & Pansements',
                'slug' => 'materiel-medical-pansements',
                'description' => 'Soins de premier secours et accessoires.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['slug']] = Category::create($catData);
        }

        // 3. Seed Products with diverse stock statuses
        $productsData = [
            [
                'code_barre' => '3400930000018',
                'name' => 'Doliprane 1000mg',
                'dci' => 'Paracétamol',
                'category_id' => $categories['antalgiques-anti-pyretiques']->id,
                'price' => 3500.00,
                'purchase_price' => 2200.00,
                'stock_quantity' => 120,
                'min_stock_alert' => 20,
                'expiration_date' => Carbon::now()->addMonths(18),
                'requires_prescription' => false,
                'dosage_unit' => 'Boîte de 8 comprimés',
            ],
            [
                'code_barre' => '3400930000025',
                'name' => 'Paracétamol Mylan 500mg',
                'dci' => 'Paracétamol',
                'category_id' => $categories['antalgiques-anti-pyretiques']->id,
                'price' => 2500.00,
                'purchase_price' => 1500.00,
                'stock_quantity' => 85,
                'min_stock_alert' => 15,
                'expiration_date' => Carbon::now()->addMonths(24),
                'requires_prescription' => false,
                'dosage_unit' => 'Boîte de 16 gélules',
            ],
            [
                'code_barre' => '3400930000032',
                'name' => 'Amoxicilline Biogaran 1g',
                'dci' => 'Amoxicilline',
                'category_id' => $categories['antibiotiques-anti-infectieux']->id,
                'price' => 8500.00,
                'purchase_price' => 5400.00,
                'stock_quantity' => 45,
                'min_stock_alert' => 10,
                'expiration_date' => Carbon::now()->addMonths(12),
                'requires_prescription' => true,
                'dosage_unit' => 'Boîte de 14 comprimés',
            ],
            [
                'code_barre' => '3400930000049',
                'name' => 'Augmentin 1g/125mg',
                'dci' => 'Amoxicilline / Acide Clavulanique',
                'category_id' => $categories['antibiotiques-anti-infectieux']->id,
                'price' => 14500.00,
                'purchase_price' => 9800.00,
                'stock_quantity' => 3, // Seuil d'alerte atteint (min 10)
                'min_stock_alert' => 10,
                'expiration_date' => Carbon::now()->addMonths(9),
                'requires_prescription' => true,
                'dosage_unit' => 'Boîte de 16 sachets',
            ],
            [
                'code_barre' => '3400930000056',
                'name' => 'Ibuprofène Sandoz 400mg',
                'dci' => 'Ibuprofène',
                'category_id' => $categories['anti-inflammatoires']->id,
                'price' => 5000.00,
                'purchase_price' => 3200.00,
                'stock_quantity' => 12, // Approche du seuil d'alerte (12 vs min 10)
                'min_stock_alert' => 10,
                'expiration_date' => Carbon::now()->addMonths(14),
                'requires_prescription' => false,
                'dosage_unit' => 'Boîte de 20 comprimés',
            ],
            [
                'code_barre' => '3400930000063',
                'name' => 'Spasfon 80mg',
                'dci' => 'Phloroglucinol',
                'category_id' => $categories['gastro-enterologie']->id,
                'price' => 6000.00,
                'purchase_price' => 4000.00,
                'stock_quantity' => 90,
                'min_stock_alert' => 20,
                'expiration_date' => Carbon::now()->addMonths(20),
                'requires_prescription' => false,
                'dosage_unit' => 'Boîte de 30 lyoc',
            ],
            [
                'code_barre' => '3400930000070',
                'name' => 'Oméprazole Teva 20mg',
                'dci' => 'Oméprazole',
                'category_id' => $categories['gastro-enterologie']->id,
                'price' => 9500.00,
                'purchase_price' => 6200.00,
                'stock_quantity' => 14, // Approche du seuil d'alerte (14 vs min 10)
                'min_stock_alert' => 10,
                'expiration_date' => Carbon::now()->addDays(20),
                'requires_prescription' => false,
                'dosage_unit' => 'Flacon 28 gélules',
            ],
            [
                'code_barre' => '3400930000087',
                'name' => 'Smecta 3g',
                'dci' => 'Diosmectite',
                'category_id' => $categories['gastro-enterologie']->id,
                'price' => 5500.00,
                'purchase_price' => 3500.00,
                'stock_quantity' => 0, // Rupture totale
                'min_stock_alert' => 15,
                'expiration_date' => Carbon::now()->addMonths(15),
                'requires_prescription' => false,
                'dosage_unit' => 'Boîte de 30 sachets',
            ],
            [
                'code_barre' => '3400930000094',
                'name' => 'Vitascorbol C 1000mg',
                'dci' => 'Acide Ascorbique (Vitamine C)',
                'category_id' => $categories['vitamines-complements']->id,
                'price' => 7000.00,
                'purchase_price' => 4500.00,
                'stock_quantity' => 70,
                'min_stock_alert' => 15,
                'expiration_date' => Carbon::now()->addMonths(11),
                'requires_prescription' => false,
                'dosage_unit' => 'Tube 20 comprimés effervescents',
            ],
            [
                'code_barre' => '3400930000100',
                'name' => 'Sirop Toplexil 150ml',
                'dci' => 'Oxomémazine',
                'category_id' => $categories['antalgiques-anti-pyretiques']->id,
                'price' => 7500.00,
                'purchase_price' => 4800.00,
                'stock_quantity' => 5, // Seuil d'alerte atteint (5 vs min 10)
                'min_stock_alert' => 10,
                'expiration_date' => Carbon::now()->addMonths(8),
                'requires_prescription' => false,
                'dosage_unit' => 'Flacon 150ml + cuillère-mesure',
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $prod) {
            $productModel = Product::create($prod);
            $createdProducts[$prod['name']] = $productModel;

            // Seed 1 or 2 batches for each product
            if ($prod['stock_quantity'] > 0) {
                \App\Models\ProductBatch::create([
                    'product_id' => $productModel->id,
                    'batch_number' => 'LOT-' . strtoupper(substr(md5($prod['name']), 0, 6)),
                    'expiration_date' => $prod['expiration_date'],
                    'quantity' => $prod['stock_quantity'],
                    'purchase_price' => $prod['purchase_price'],
                    'supplier_name' => 'Labo Pharma Grossiste',
                    'is_active' => true,
                ]);
            }
        }

        // 4. Seed Sales for Vendeur 1 (Caissier Principal)
        $sale1 = Sale::create([
            'invoice_number' => 'FAC-' . Carbon::now()->format('Ymd') . '-0001',
            'user_id' => $caissier1->id,
            'subtotal' => 12000.00,
            'tax_amount' => 0.00,
            'discount_amount' => 0.00,
            'total_amount' => 12000.00,
            'amount_paid' => 15000.00,
            'change_amount' => 3000.00,
            'payment_method' => 'Espèces',
            'patient_name' => 'M. Kasongo Alain',
            'doctor_name' => 'Dr. Mbayo',
            'notes' => 'Vente régulière',
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $createdProducts['Doliprane 1000mg']->id,
            'product_name' => 'Doliprane 1000mg',
            'unit_price' => 3500.00,
            'quantity' => 2,
            'subtotal' => 7000.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $createdProducts['Ibuprofène Sandoz 400mg']->id,
            'product_name' => 'Ibuprofène Sandoz 400mg',
            'unit_price' => 5000.00,
            'quantity' => 1,
            'subtotal' => 5000.00,
        ]);

        // 5. Seed Sales for Vendeur 2 (Jean-Marc)
        $sale2 = Sale::create([
            'invoice_number' => 'FAC-' . Carbon::now()->format('Ymd') . '-0002',
            'user_id' => $caissier2->id,
            'subtotal' => 17000.00,
            'tax_amount' => 0.00,
            'discount_amount' => 0.00,
            'total_amount' => 17000.00,
            'amount_paid' => 20000.00,
            'change_amount' => 3000.00,
            'payment_method' => 'Mobile Money',
            'patient_name' => 'Mme Kabange Claire',
            'doctor_name' => null,
            'notes' => 'Paiement M-Pesa',
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $createdProducts['Amoxicilline Biogaran 1g']->id,
            'product_name' => 'Amoxicilline Biogaran 1g',
            'unit_price' => 8500.00,
            'quantity' => 2,
            'subtotal' => 17000.00,
        ]);

        // 6. Seed Manual Client Requisition for missing/requested products
        Requisition::create([
            'product_id' => null,
            'product_name' => 'Insuline Mixtard 30 Penfill 100 UI/ml',
            'requested_quantity' => 5,
            'user_id' => $caissier1->id,
            'status' => 'en_attente',
            'type' => 'demande_client',
            'notes' => 'Demande d\'un client diabétique régulier - Produit non répertorié',
        ]);

        // 7. Auto-sync low & near-low stock requisitions
        Requisition::syncAlertRequisitions();
    }
}
