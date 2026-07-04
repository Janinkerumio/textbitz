<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'phone_num' => '+639171234567',
                'contact_name' => 'Alice Chen',
                'tags' => ['VIP', 'Regular'],
            ],
            [
                'phone_num' => '+639182345678',
                'contact_name' => 'Bob Santos',
                'tags' => ['New'],
            ],
            [
                'phone_num' => '+639193456789',
                'contact_name' => 'Charlie Reyes',
                'tags' => ['Regular', 'Promo'],
            ],
            [
                'phone_num' => '+639204567890',
                'contact_name' => 'Diana Cruz',
                'tags' => ['VIP'],
            ],
            [
                'phone_num' => '+639215678901',
                'contact_name' => 'Edward Lim',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639226789012',
                'contact_name' => 'Fiona Tan',
                'tags' => ['New Promo'],
            ],
            [
                'phone_num' => '+639237890123',
                'contact_name' => 'George Villanueva',
                'tags' => ['VIP', 'Regular'],
            ],
            [
                'phone_num' => '+6392348901243',
                'contact_name' => 'Hannah Ong',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639241234567',
                'contact_name' => 'Ian Panganiban',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639252345678',
                'contact_name' => 'Jane Dela Cruz',
                'tags' => ['VIP'],
            ],
            [
                'phone_num' => '+639263456789',
                'contact_name' => 'Kevin Mendoza',
                'tags' => ['New', 'Promo'],
            ],
            [
                'phone_num' => '+639274567890',
                'contact_name' => 'Lara Gutierrez',
                'tags' => ['Regular', 'VIP'],
            ],
            [
                'phone_num' => '+639285678901',
                'contact_name' => 'Marco Valdez',
                'tags' => ['New'],
            ],
            [
                'phone_num' => '+639296789012',
                'contact_name' => 'Nicole Aquino',
                'tags' => ['Promo'],
            ],
            [
                'phone_num' => '+639307890123',
                'contact_name' => 'Oliver Ferrer',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639318901234',
                'contact_name' => 'Patricia Ramos',
                'tags' => ['VIP', 'Promo'],
            ],
            [
                'phone_num' => '+639329012345',
                'contact_name' => 'Quinn Sy',
                'tags' => ['New', 'Regular'],
            ],
            [
                'phone_num' => '+639331234567',
                'contact_name' => 'Rhea Bautista',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639342345678',
                'contact_name' => 'Samir Khan',
                'tags' => ['VIP'],
            ],
            [
                'phone_num' => '+639353456789',
                'contact_name' => 'Tanya Corpuz',
                'tags' => ['New Promo'],
            ],
            [
                'phone_num' => '+639364567890',
                'contact_name' => 'Ulric Dee',
                'tags' => ['Regular', 'Promo'],
            ],
            [
                'phone_num' => '+639375678901',
                'contact_name' => 'Vera Manalo',
                'tags' => ['VIP'],
            ],
            [
                'phone_num' => '+639386789012',
                'contact_name' => 'Wesley Chua',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639397890123',
                'contact_name' => 'Xandra Go',
                'tags' => ['New'],
            ],
            [
                'phone_num' => '+639408901234',
                'contact_name' => 'Yuri Nakpil',
                'tags' => ['Promo'],
            ],
            [
                'phone_num' => '+639419012345',
                'contact_name' => 'Zara Lim',
                'tags' => ['VIP', 'Regular'],
            ],
            [
                'phone_num' => '+639421234567',
                'contact_name' => 'Adrian Co',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639432345678',
                'contact_name' => 'Bianca Florendo',
                'tags' => ['New Promo'],
            ],
            [
                'phone_num' => '+639443456789',
                'contact_name' => 'Cesar Dizon',
                'tags' => ['Regular', 'Promo'],
            ],
            [
                'phone_num' => '+639454567890',
                'contact_name' => 'Danica Uy',
                'tags' => ['VIP'],
            ],
            [
                'phone_num' => '+639465678901',
                'contact_name' => 'Eli Javier',
                'tags' => ['New'],
            ],
            [
                'phone_num' => '+639476789012',
                'contact_name' => 'Francine Lao',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639487890123',
                'contact_name' => 'Gabriel Tan',
                'tags' => ['VIP', 'Regular'],
            ],
            [
                'phone_num' => '+639498901234',
                'contact_name' => 'Hector Reyes',
                'tags' => ['Promo'],
            ],
            [
                'phone_num' => '+639501234567',
                'contact_name' => 'Inez Pangilinan',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639512345678',
                'contact_name' => 'Jiro Sison',
                'tags' => ['New', 'Regular'],
            ],
            [
                'phone_num' => '+639523456789',
                'contact_name' => 'Kyla Ocampo',
                'tags' => ['VIP', 'Promo'],
            ],
            [
                'phone_num' => '+639534567890',
                'contact_name' => 'Leandro Bautista',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639545678901',
                'contact_name' => 'Mikaella Espiritu',
                'tags' => ['New Promo'],
            ],
            [
                'phone_num' => '+639556789012',
                'contact_name' => 'Nathaniel Cruz',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639567890123',
                'contact_name' => 'Olivia David',
                'tags' => ['VIP'],
            ],
            [
                'phone_num' => '+639578901234',
                'contact_name' => 'Paolo Andrada',
                'tags' => ['Regular', 'Promo'],
            ],
            [
                'phone_num' => '+639589012345',
                'contact_name' => 'Reina Tobias',
                'tags' => ['New'],
            ],
            [
                'phone_num' => '+639591234567',
                'contact_name' => 'Sebastian Lee',
                'tags' => ['VIP', 'Regular'],
            ],
            [
                'phone_num' => '+639602345678',
                'contact_name' => 'Thea Navarro',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639613456789',
                'contact_name' => 'Uriel Mercado',
                'tags' => ['Promo'],
            ],
            [
                'phone_num' => '+639624567890',
                'contact_name' => 'Violeta Sarmiento',
                'tags' => ['New Promo'],
            ],
            [
                'phone_num' => '+639635678901',
                'contact_name' => 'Wyatt Dominguez',
                'tags' => ['Regular'],
            ],
            [
                'phone_num' => '+639646789012',
                'contact_name' => 'Ysabel Fortuno',
                'tags' => ['VIP', 'Promo'],
            ],
            [
                'phone_num' => '+639657890123',
                'contact_name' => 'Zachary Lim',
                'tags' => ['Regular'],
            ],
        ];

        foreach ($contacts as $contact) {
            \App\Models\Contact::create($contact);
        }
    }
}
