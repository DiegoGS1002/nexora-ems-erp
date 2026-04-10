<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cats  = ProductCategory::pluck('id', 'slug');
        $catId = fn(string $slug) => $cats[$slug] ?? null;

        // [ name, ean, unit, sale_price, cost_price, stock, stock_min, category_slug, exp_days? ]
        $products = [
            [
                'name' => 'Arroz Branco Tipo 1 5kg',
                'ean' => '7891000100101',
                'description' => 'Pacote de arroz branco tipo 1 com 5kg.',
                'unit_of_measure' => 'KG',
                'sale_price' => 27.90,
                'stock' => 120,
                'expiration_date' => now()->addMonths(10)->toDateString(),
                'category' => 'alimentos',
                'image' => null,
            ],
            [
                'name' => 'Feijao Carioca 1kg',
                'ean' => '7891000100102',
                'description' => 'Feijao carioca selecionado em pacote de 1kg.',
                'unit_of_measure' => 'KG',
                'sale_price' => 8.50,
                'stock' => 200,
                'expiration_date' => now()->addMonths(8)->toDateString(),
                'category' => 'alimentos',
                'image' => null,
            ],
            [
                'name' => 'Detergente Neutro 500ml',
                'ean' => '7891000100103',
                'description' => 'Detergente liquido neutro para loucas.',
                'unit_of_measure' => 'UN',
                'sale_price' => 2.99,
                'stock' => 350,
                'expiration_date' => null,
                'category' => 'outro',
                'image' => null,
            ],
            [
                'name' => 'Papel Higienico Folha Dupla 12un',
                'ean' => '7891000100104',
                'description' => 'Fardo com 12 rolos de papel higienico folha dupla.',
                'unit_of_measure' => 'CX',
                'sale_price' => 19.90,
                'stock' => 90,
                'expiration_date' => null,
                'category' => 'outro',
                'image' => null,
            ],
            [
                'name' => 'Sabonete em Barra 90g',
                'ean' => '7891000100105',
                'description' => 'Sabonete em barra perfumado de 90g.',
                'unit_of_measure' => 'UN',
                'sale_price' => 1.99,
                'stock' => 500,
                'expiration_date' => now()->addMonths(18)->toDateString(),
                'category' => 'outro',
                'image' => null,
            ],
            // ══ MERCEARIA SECA ══
            ['Arroz Tipo 1 Branco Camil 5kg',            '7896006702055', 'PCT', 27.90, 19.50, 380, 50,  'mercearia-seca', null],
            ['Arroz Integral Urbano 1kg',                 '7896005302019', 'PCT',  8.49,  5.80, 220, 30,  'mercearia-seca', null],
            ['Feijão Carioca Camil 1kg',                  '7896006706060', 'PCT',  9.99,  6.70, 450, 60,  'mercearia-seca', null],
            ['Feijão Preto Kicaldo 1kg',                  '7896193501024', 'PCT', 10.49,  7.10, 300, 40,  'mercearia-seca', null],
            ['Macarrão Espaguete Barilla 500g',           '8076800195057', 'PCT',  5.99,  3.90, 600, 80,  'mercearia-seca', null],
            ['Macarrão Parafuso Renata 500g',             '7896022203052', 'PCT',  4.49,  2.80, 550, 80,  'mercearia-seca', null],
            ['Farinha de Trigo Dona Benta 1kg',           '7891080400102', 'PCT',  4.99,  3.20, 400, 50,  'mercearia-seca', null],
            ['Açúcar Cristal União 1kg',                  '7891910000197', 'PCT',  4.69,  3.00, 700, 100, 'mercearia-seca', null],
            ['Açúcar Refinado Guarani 1kg',               '7896065200015', 'PCT',  4.89,  3.10, 600, 80,  'mercearia-seca', null],
            ['Sal Refinado Cisne 1kg',                    '7896110002053', 'PCT',  2.49,  1.40, 500, 60,  'mercearia-seca', null],
            ['Óleo de Soja Soya 900ml',                   '7891107101621', 'UN',   6.99,  4.70, 480, 60,  'mercearia-seca', null],
            ['Azeite de Oliva Gallo 500ml',               '5601252066022', 'UN',  32.90, 22.00, 180, 20,  'mercearia-seca', null],
            ['Molho de Tomate Heinz 340g',                '7896102503133', 'UN',   3.49,  2.20, 750, 100, 'mercearia-seca', null],
            ['Extrato de Tomate Elefante 340g',           '7891083001115', 'UN',   2.89,  1.70, 600, 80,  'mercearia-seca', null],
            ['Vinagre de Álcool Castelo 750ml',           '7897793000024', 'UN',   3.29,  1.90, 320, 40,  'mercearia-seca', null],
            ['Maionese Hellmanns 500g',                   '7891150062039', 'UN',   8.99,  5.90, 420, 50,  'mercearia-seca', null],
            ['Ketchup Heinz 397g',                        '7896102506455', 'UN',   6.99,  4.50, 350, 40,  'mercearia-seca', null],
            ['Tempero Completo Knorr 500g',               '7891150060295', 'UN',   9.49,  6.20, 280, 30,  'mercearia-seca', null],
            ['Caldo de Carne Maggi 57g (6 cubos)',        '7891000310212', 'CX',   2.59,  1.50, 800, 100, 'mercearia-seca', null],
            ['Farinha de Mandioca Yoki 500g',             '7896336402100', 'PCT',  4.29,  2.70, 350, 50,  'mercearia-seca', null],
            ['Aveia em Flocos Quaker 200g',               '7891000100010', 'PCT',  5.99,  3.80, 300, 40,  'mercearia-seca', null],
            ['Biscoito Cream Cracker Piraquê 400g',       '7896024700109', 'PCT',  4.79,  3.00, 480, 60,  'mercearia-seca', null],
            ['Biscoito Recheado Oreo 144g',               '7622300489052', 'PCT',  5.49,  3.30, 550, 70,  'mercearia-seca', null],
            ['Biscoito Passatempo Nestlé 130g',           '7891000053409', 'PCT',  3.99,  2.40, 500, 60,  'mercearia-seca', null],
            ['Achocolatado Nescau 400g',                  '7891000024705', 'UN',   7.49,  4.80, 400, 50,  'mercearia-seca', null],
            ['Café Pilão Torrado e Moído 500g',           '7896229900539', 'PCT', 15.99, 10.80, 350, 50,  'mercearia-seca', null],
            ['Café 3 Corações Extra Forte 500g',          '7896224400017', 'PCT', 16.49, 11.20, 300, 40,  'mercearia-seca', null],
            ['Chá Preto Leão 10 Sachês',                  '7896021002108', 'CX',   4.99,  3.00, 200, 30,  'mercearia-seca', null],
            ['Ervilha em Lata Bonduelle 170g',            '3083680000041', 'UN',   3.99,  2.40, 400, 50,  'mercearia-seca', null],
            ['Milho Verde em Lata Yoki 200g',             '7896336400021', 'UN',   3.29,  2.00, 450, 60,  'mercearia-seca', null],
            ['Amido de Milho Maizena 200g',               '7891000056348', 'PCT',  3.79,  2.30, 350, 45,  'mercearia-seca', null],
            ['Fermento em Pó Royal 100g',                 '7891000093308', 'UN',   3.49,  2.10, 300, 40,  'mercearia-seca', null],
            ['Chocolate em Pó Nestlé 200g',               '7891000095005', 'UN',   6.99,  4.40, 280, 35,  'mercearia-seca', null],
            ['Geleia de Morango Queensberry 320g',        '7896429300028', 'UN',   8.99,  5.80, 200, 25,  'mercearia-seca', null],
            ['Mel Puro Baldoni 280g',                     '7896000566016', 'UN',  14.99,  9.60, 150, 20,  'mercearia-seca', null],

            // ══ BEBIDAS ══
            ['Água Mineral Crystal 500ml',                '7891149101706', 'UN',   1.99,  0.90, 2400, 300, 'bebidas', null],
            ['Água Mineral Bonafont 1,5L',                '7891149102505', 'UN',   3.49,  1.80, 1200, 150, 'bebidas', null],
            ['Refrigerante Coca-Cola 2L',                 '7894900700015', 'UN',   8.99,  5.60,  800, 100, 'bebidas', null],
            ['Refrigerante Coca-Cola Lata 350ml',         '7894900011517', 'UN',   3.49,  2.10, 1800, 200, 'bebidas', null],
            ['Refrigerante Guaraná Antarctica 2L',        '7891991010507', 'UN',   7.99,  4.90,  700,  80, 'bebidas', null],
            ['Refrigerante Sprite 2L',                    '7894900720015', 'UN',   7.99,  4.90,  600,  80, 'bebidas', null],
            ['Suco Del Valle Laranja 1L',                 '7894900171212', 'UN',   6.49,  4.00,  500,  60, 'bebidas', null],
            ['Suco Ades Caju 1L',                         '7891000250501', 'UN',   5.99,  3.70,  400,  50, 'bebidas', null],
            ['Cerveja Brahma Lata 350ml',                 '7891991010484', 'UN',   3.49,  2.10, 2400, 300, 'bebidas', null],
            ['Cerveja Skol Lata 350ml',                   '7891991055034', 'UN',   3.29,  1.90, 2400, 300, 'bebidas', null],
            ['Cerveja Heineken Long Neck 330ml',          '8712000023968', 'UN',   5.99,  3.80, 1200, 120, 'bebidas', null],
            ['Cerveja Stella Artois 600ml',               '7891991221007', 'UN',   9.99,  6.50,  800,  80, 'bebidas', null],
            ['Vinho Tinto Seco Miolo 750ml',              '7898190600113', 'UN',  38.90, 24.00,  150,  20, 'bebidas', null],
            ['Leite de Coco Sococo 200ml',                '7896004400148', 'UN',   4.99,  3.10,  300,  40, 'bebidas', null],
            ['Energético Monster Energy 473ml',           '7898381500046', 'UN',   9.99,  6.50,  400,  50, 'bebidas', null],
            ['Água de Coco Boa Fruta 1L',                 '7896693800019', 'UN',   7.99,  4.90,  250,  30, 'bebidas', null],

            // ══ LATICÍNIOS E FRIOS ══
            ['Leite Integral Itambé 1L',                  '7896051130241', 'UN',   4.89,  3.20, 1800, 200, 'laticinios-frios', 30],
            ['Leite Desnatado Itambé 1L',                 '7896051130258', 'UN',   4.99,  3.30, 1200, 150, 'laticinios-frios', 30],
            ['Leite sem Lactose Itambé 1L',               '7896051130265', 'UN',   6.49,  4.10,  800, 100, 'laticinios-frios', 30],
            ['Iogurte Natural Activia 170g',              '7891025110512', 'UN',   3.99,  2.50,  600,  80, 'laticinios-frios', 20],
            ['Iogurte Grego Danone 100g',                 '7891025114121', 'UN',   4.49,  2.80,  500,  60, 'laticinios-frios', 20],
            ['Queijo Minas Frescal Itambé 400g',          '7896051140028', 'UN',  14.99,  9.50,  300,  40, 'laticinios-frios', 15],
            ['Queijo Prato Fatiado Polenghi 150g',        '7896214502015', 'UN',  12.49,  7.80,  280,  35, 'laticinios-frios', 20],
            ['Manteiga com Sal Aviação 200g',             '7891007301128', 'UN',   9.99,  6.40,  400,  50, 'laticinios-frios', 60],
            ['Creme de Leite Nestlé 200g',                '7891000251034', 'UN',   3.99,  2.50,  800, 100, 'laticinios-frios', 90],
            ['Leite Condensado Moça 395g',                '7891000009306', 'UN',   5.99,  3.80,  700,  90, 'laticinios-frios', 180],
            ['Presunto Fatiado Sadia 200g',               '7896085038027', 'UN',  10.49,  6.80,  300,  40, 'laticinios-frios', 30],
            ['Salame Italiano Rezende 200g',              '7896085001113', 'UN',  12.99,  8.50,  200,  25, 'laticinios-frios', 45],
            ['Mozzarella Fatiada Polenghi 150g',          '7896214500011', 'UN',  13.99,  9.00,  250,  30, 'laticinios-frios', 20],

            // ══ HORTIFRUTI ══
            ['Banana Prata (kg)',                         '2000000000011', 'KG',   4.99,  2.80,  200,  30, 'hortifruti',  7],
            ['Maçã Gala (kg)',                            '2000000000012', 'KG',   7.99,  4.90,  150,  20, 'hortifruti', 14],
            ['Laranja Lima (kg)',                         '2000000000013', 'KG',   3.99,  2.10,  250,  30, 'hortifruti', 10],
            ['Tomate Italiano (kg)',                      '2000000000014', 'KG',   5.99,  3.50,  180,  25, 'hortifruti',  5],
            ['Cebola (kg)',                               '2000000000015', 'KG',   3.49,  1.90,  220,  30, 'hortifruti', 20],
            ['Batata Inglesa (kg)',                       '2000000000016', 'KG',   4.49,  2.50,  300,  40, 'hortifruti', 15],
            ['Cenoura (kg)',                              '2000000000017', 'KG',   3.99,  2.10,  200,  25, 'hortifruti', 10],
            ['Alface Americana (unidade)',                '2000000000018', 'UN',   2.99,  1.50,  120,  20, 'hortifruti',  5],
            ['Brócolis (maço)',                           '2000000000019', 'UN',   4.99,  2.80,  100,  15, 'hortifruti',  4],
            ['Abacate Hass (unidade)',                    '2000000000020', 'UN',   3.49,  1.90,  150,  20, 'hortifruti',  7],
            ['Limão Taiti (kg)',                          '2000000000021', 'KG',   5.99,  3.20,  180,  25, 'hortifruti', 14],
            ['Alho (100g)',                               '2000000000022', 'PCT',  3.99,  2.10,  300,  40, 'hortifruti', 30],
            ['Mamão Formosa (unidade)',                   '2000000000023', 'UN',   6.49,  3.80,  100,  15, 'hortifruti',  7],
            ['Uva Itália (kg)',                           '2000000000024', 'KG',  12.99,  8.00,   80,  10, 'hortifruti',  7],

            // ══ CARNES E PEIXES ══
            ['Frango Inteiro Sadia Congelado (kg)',       '2001000000001', 'KG',  10.99,  7.20,  350,  50, 'carnes-e-peixes', 60],
            ['Peito de Frango Filé BRF (kg)',             '2001000000002', 'KG',  17.99, 11.80,  280,  40, 'carnes-e-peixes',  5],
            ['Carne Bovina Patinho Moído (kg)',           '2001000000003', 'KG',  34.99, 23.00,  200,  30, 'carnes-e-peixes',  3],
            ['Picanha Bovina (kg)',                       '2001000000004', 'KG',  79.90, 54.00,   80,  10, 'carnes-e-peixes',  3],
            ['Costela Bovina (kg)',                       '2001000000005', 'KG',  39.90, 26.00,  120,  15, 'carnes-e-peixes',  3],
            ['Alcatra Bovina (kg)',                       '2001000000006', 'KG',  44.90, 29.00,  100,  12, 'carnes-e-peixes',  3],
            ['Filé de Tilápia Congelado 800g',            '7896010000021', 'PCT', 27.90, 18.00,  150,  20, 'carnes-e-peixes', 90],
            ['Camarão Pequeno Congelado 300g',            '7896010000038', 'PCT', 19.90, 12.80,  120,  15, 'carnes-e-peixes', 90],
            ['Linguiça Calabresa Defumada Sadia 500g',   '7896085034012', 'PCT', 14.99,  9.70,  250,  30, 'carnes-e-peixes', 30],
            ['Salsicha Viena Perdigão 500g',              '7896085051019', 'PCT', 11.49,  7.40,  300,  40, 'carnes-e-peixes', 30],
            ['Coxa e Sobrecoxa de Frango BRF (kg)',       '2001000000007', 'KG',  12.99,  8.40,  300,  40, 'carnes-e-peixes',  5],

            // ══ HIGIENE PESSOAL ══
            ['Shampoo Pantene Restauração 400ml',         '7500435125284', 'UN',  18.99, 12.20,  350,  40, 'higiene-pessoal', null],
            ['Condicionador Dove Hidratação 400ml',       '7891150057012', 'UN',  17.49, 11.20,  300,  35, 'higiene-pessoal', null],
            ['Sabonete Dove Hidratação Profunda 90g',     '7891150034113', 'UN',   3.99,  2.40,  800, 100, 'higiene-pessoal', null],
            ['Creme Dental Colgate Total 12 90g',         '7509546060019', 'UN',   6.49,  4.10,  600,  80, 'higiene-pessoal', null],
            ['Escova Dental Oral-B Pro Saúde',            '7500435080324', 'UN',   7.99,  5.10,  500,  60, 'higiene-pessoal', null],
            ['Desodorante Rexona Men Aerosol 150ml',      '7891150023018', 'UN',  12.99,  8.30,  400,  50, 'higiene-pessoal', null],
            ['Papel Higiênico Neve Premium 4 Rolos',      '7896027203020', 'PCT',  5.99,  3.70,  700,  90, 'higiene-pessoal', null],
            ['Absorvente Always Ultrafino 8un',           '7500435073500', 'PCT',  7.99,  5.10,  400,  50, 'higiene-pessoal', null],
            ['Aparelho de Barbear Gillette Prestobarba',  '7500435107037', 'UN',   3.49,  2.10,  600,  80, 'higiene-pessoal', null],
            ['Fralda Pampers Confort Sec G 28un',         '7500435143225', 'PCT', 44.90, 29.00,  150,  20, 'higiene-pessoal', null],
            ['Protetor Solar Neutrogena FPS50 120ml',     '3574660417500', 'UN',  29.90, 19.00,  200,  25, 'higiene-pessoal', null],
            ['Shampoo Clear Men Anticaspa 400ml',         '7891150048712', 'UN',  17.49, 11.20,  280,  35, 'higiene-pessoal', null],

            // ══ LIMPEZA ══
            ['Detergente Ypê Neutro 500ml',               '7896098900027', 'UN',   2.49,  1.40, 1200, 150, 'limpeza-conservacao', null],
            ['Lava-Roupas OMO Multiação 3kg',             '7891150014619', 'UN',  37.90, 24.00,  250,  30, 'limpeza-conservacao', null],
            ['Amaciante Downy Brisa do Mar 1L',           '7500435108539', 'UN',  12.99,  8.30,  350,  40, 'limpeza-conservacao', null],
            ['Desinfetante Pinho Sol Lavanda 500ml',      '7891035110011', 'UN',   4.49,  2.80,  600,  80, 'limpeza-conservacao', null],
            ['Alvejante Água Sanitária Qboa 2L',          '7891035312508', 'UN',   5.99,  3.70,  500,  60, 'limpeza-conservacao', null],
            ['Limpador Multiuso Mr. Músculo 500ml',       '7891035128504', 'UN',   6.49,  4.10,  400,  50, 'limpeza-conservacao', null],
            ['Esponja Scotch-Brite Dupla Face',           '7891040012116', 'UN',   2.99,  1.70,  800, 100, 'limpeza-conservacao', null],
            ['Saco de Lixo 100L Vonder 10un',             '7896060200010', 'PCT',  7.99,  4.90,  400,  50, 'limpeza-conservacao', null],
            ['Sabão em Barra Omo 5 unid.',                '7891150063036', 'PCT', 12.99,  8.30,  300,  40, 'limpeza-conservacao', null],
            ['Limpador WD-40 300ml',                      '0078590001007', 'UN',  19.90, 12.80,  150,  20, 'limpeza-conservacao', null],

            // ══ CONGELADOS ══
            ['Pizza Congelada Sadia Mozzarella 460g',     '7896085071017', 'UN',  18.99, 12.20,  200,  25, 'congelados-refrigerados', 120],
            ['Lasanha Bolonhesa Perdigão 600g',           '7896085061018', 'UN',  17.49, 11.20,  180,  20, 'congelados-refrigerados', 120],
            ['Sorvete Kibon Flocos Pote 2L',              '7891151051014', 'UN',  24.90, 16.00,  120,  15, 'congelados-refrigerados', 180],
            ['Hambúrguer Sadia Blend 672g',               '7896085033015', 'PCT', 24.99, 16.50,  200,  25, 'congelados-refrigerados', 60],
            ['Batata Frita McCain 1,05kg',                '7896019400014', 'PCT', 19.90, 13.00,  150,  20, 'congelados-refrigerados', 180],
            ['Nuggets de Frango Sadia 300g',              '7896085072014', 'PCT', 14.99,  9.70,  250,  30, 'congelados-refrigerados', 90],

            // ══ BAZAR ══
            ['Papel Alumínio 7,5m Reynolds',              '7896005303047', 'RL',   8.99,  5.70,  300,  40, 'bazar-utilidades', null],
            ['Filme PVC para Alimentos 30mx30cm',         '7896005303054', 'RL',   7.99,  5.00,  300,  40, 'bazar-utilidades', null],
            ['Pilha Duracell AA 2 unid.',                 '5000394004023', 'PCT',  9.99,  6.40,  400,  50, 'bazar-utilidades', null],
            ['Prato Descartável Cristal 10un',            '7896025900010', 'PCT',  4.99,  3.10,  600,  80, 'bazar-utilidades', null],
            ['Copo Descartável 200ml 50un',               '7896098210104', 'PCT',  3.99,  2.40,  800, 100, 'bazar-utilidades', null],
            ['Vela de Aniversário 8 unid.',               '7898039320015', 'PCT',  4.49,  2.70,  400,  50, 'bazar-utilidades', null],
            ['Palito de Dente 100 unid.',                 '7896000100107', 'PCT',  2.49,  1.40,  500,  60, 'bazar-utilidades', null],

            // ══ PET SHOP ══
            ['Ração Pedigree Adulto 3kg',                 '7896029040058', 'SC',  39.90, 26.00,  120,  15, 'pet-shop', null],
            ['Ração Purina Cat Chow Adulto 3kg',          '7891000302230', 'SC',  49.90, 32.00,  100,  15, 'pet-shop', null],
            ['Areia Higiênica Pipicat 4kg',               '7898413690034', 'SC',  22.90, 14.80,   80,  10, 'pet-shop', null],
            ['Petisco Pedigree Dentastix 77g',            '7896029040256', 'PCT',  6.99,  4.30,  200,  25, 'pet-shop', null],

            // ══ BEBÊ E CRIANÇA ══
            ['Leite em Pó Nan Nestlé 400g',              '7891000306048', 'UN',  34.90, 22.00,  150,  20, 'bebe-crianca', 180],
            ['Papinha Nestlé Maçã 115g',                  '7891000300091', 'UN',   3.99,  2.40,  300,  40, 'bebe-crianca',  90],
            ['Lenço Umedecido Huggies 50 unid.',         '7896007541020', 'PCT', 12.99,  8.30,  200,  25, 'bebe-crianca', null],
        ];

        $inserted = 0;
        foreach ($products as [$name, $ean, $unit, $sale, $cost, $stock, $min, $catSlug, $expDays]) {
            if (Product::where('ean', $ean)->exists()) continue;

            Product::create([
                'id'                  => (string) Str::uuid(),
                'name'                => $name,
                'ean'                 => $ean,
                'description'         => $name,
                'unit_of_measure'     => $unit,
                'sale_price'          => $sale,
                'cost_price'          => $cost,
                'stock'               => $stock,
                'stock_min'           => $min,
                'category'            => $catSlug,
                'product_category_id' => $catId($catSlug),
                'product_type'        => 'produto_fisico',
                'nature'              => 'mercadoria_revenda',
                'is_active'           => true,
                'expiration_date'     => $expDays ? now()->addDays($expDays)->toDateString() : null,
            ]);
            $inserted++;
        }

        $this->command->info("✅ {$inserted} produtos semeados.");
    }
}

