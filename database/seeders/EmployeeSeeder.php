<?php

namespace Database\Seeders;

use App\Models\Employees;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // [ name, cpf, gender, birth_date, role, department, salary, work_schedule, admission_date, email, phone ]
        $employees = [
            // ── Diretoria ─────────────────────────────────────────────
            ['Marcos Antônio Silveira',    '00100200300', 'M', '1972-03-15', 'Diretor Geral',         'Diretoria',      18500.00, 'ADM',       '2010-03-01', 'marcos.silveira@supermercadobh.com.br',  '31991000001'],
            ['Luciana Ferreira Matos',     '00200300400', 'F', '1975-07-22', 'Diretor Comercial',     'Diretoria',      15800.00, 'ADM',       '2012-06-01', 'luciana.matos@supermercadobh.com.br',    '31991000002'],
            ['Eduardo Costa Drummond',     '00300400500', 'M', '1979-11-08', 'Diretor Financeiro',    'Diretoria',      16200.00, 'ADM',       '2013-01-01', 'eduardo.drummond@supermercadobh.com.br', '31991000003'],

            // ── Gerência ─────────────────────────────────────────────
            ['Renata Borges Araújo',       '00400500600', 'F', '1980-04-12', 'Gerente de Loja',       'Operações',       9800.00, 'ADM',       '2015-02-01', 'renata.araujo@supermercadobh.com.br',    '31991000004'],
            ['Carlos Henrique Lima',       '00500600700', 'M', '1983-09-28', 'Gerente Administrativo','Administrativo',  8500.00, 'ADM',       '2016-05-01', 'carlos.lima@supermercadobh.com.br',      '31991000005'],
            ['Patricia Oliveira Nunes',    '00600700800', 'F', '1986-01-15', 'Gerente Financeiro',    'Financeiro',      8200.00, 'ADM',       '2017-03-01', 'patricia.nunes@supermercadobh.com.br',   '31991000006'],
            ['André Luiz Pereira',         '00700800900', 'M', '1978-06-30', 'Gerente de Compras',    'Compras',         8800.00, 'ADM',       '2014-08-01', 'andre.pereira@supermercadobh.com.br',    '31991000007'],
            ['Fernanda Rocha Santos',      '00800901000', 'F', '1985-12-05', 'Gerente de Estoque',    'Estoque',         7900.00, 'ADM',       '2015-11-01', 'fernanda.santos@supermercadobh.com.br',  '31991000008'],
            ['Ricardo Alves Moreira',      '00900011100', 'M', '1988-08-18', 'Gerente de TI',         'TI',              9200.00, 'ADM',       '2018-04-01', 'ricardo.moreira@supermercadobh.com.br',  '31991000009'],

            // ── Supervisão ───────────────────────────────────────────
            ['Débora Cristina Campos',     '01000111200', 'F', '1990-05-20', 'Supervisor de Checkout','Operações',       4800.00, 'TURNOA',    '2019-01-15', 'debora.campos@supermercadobh.com.br',    '31991000010'],
            ['Thiago Roberto Pinto',       '01100211300', 'M', '1987-10-14', 'Supervisor de Estoque', 'Estoque',         4600.00, 'TURNOB',    '2018-07-01', 'thiago.pinto@supermercadobh.com.br',     '31991000011'],
            ['Valério Santos Gomes',       '01200311400', 'M', '1985-03-07', 'Supervisor de Segurança','Segurança',      4500.00, 'TURNOC',    '2017-09-01', 'valerio.gomes@supermercadobh.com.br',    '31991000012'],
            ['Sônia Maria Figueiredo',     '01300411500', 'F', '1982-07-25', 'Supervisor de Padaria', 'Padaria',         4700.00, 'TURNOA',    '2016-03-01', 'sonia.figueiredo@supermercadobh.com.br', '31991000013'],
            ['José Carlos Barbosa',        '01400511600', 'M', '1984-11-02', 'Supervisor de Açougue', 'Açougue',         4800.00, 'TURNOA',    '2016-06-01', 'jose.barbosa@supermercadobh.com.br',     '31991000014'],

            // ── Administrativo / RH / Financeiro ─────────────────────
            ['Camila Duarte Vieira',       '01500611700', 'F', '1992-02-18', 'Analista de RH',        'RH',              3800.00, 'ADM',       '2020-03-01', 'camila.vieira@supermercadobh.com.br',    '31991000015'],
            ['Bruno César Magalhães',      '01600711800', 'M', '1991-06-10', 'Analista Financeiro',   'Financeiro',      3900.00, 'ADM',       '2020-01-15', 'bruno.magalhaes@supermercadobh.com.br',  '31991000016'],
            ['Isabela Nascimento Reis',    '01700811900', 'F', '1994-09-23', 'Analista de TI',        'TI',              4200.00, 'ADM',       '2021-06-01', 'isabela.reis@supermercadobh.com.br',     '31991000017'],
            ['Rodrigo Teixeira Carvalho',  '01800912000', 'M', '1993-04-05', 'Analista Administrativo','Administrativo', 3600.00, 'ADM',       '2021-03-15', 'rodrigo.carvalho@supermercadobh.com.br', '31991000018'],
            ['Luana Sousa Andrade',        '01901012100', 'F', '1996-01-30', 'Auxiliar Administrativo','Administrativo', 2200.00, 'ADM',       '2022-08-01', 'luana.andrade@supermercadobh.com.br',    '31991000019'],

            // ── Operadores de Caixa ──────────────────────────────────
            ['Ana Paula Ferreira',         '02001112200', 'F', '1998-03-14', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOA',    '2021-11-01', 'ana.ferreira@supermercadobh.com.br',     '31991000020'],
            ['Daniel Luís Souza',          '02101212300', 'M', '1999-07-08', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOB',    '2022-01-10', 'daniel.souza@supermercadobh.com.br',     '31991000021'],
            ['Mariana Cristina Leite',     '02201312400', 'F', '1997-11-22', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOC',    '2022-03-15', 'mariana.leite@supermercadobh.com.br',    '31991000022'],
            ['Gustavo Henrique Alves',     '02301412500', 'M', '2000-05-16', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOA',    '2022-06-01', 'gustavo.alves@supermercadobh.com.br',    '31991000023'],
            ['Juliana Moreira Lima',       '02401512600', 'F', '1998-09-03', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOB',    '2022-07-20', 'juliana.lima@supermercadobh.com.br',     '31991000024'],
            ['Rafael Costa Mendes',        '02501612700', 'M', '2001-02-27', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOC',    '2023-01-05', 'rafael.mendes@supermercadobh.com.br',    '31991000025'],
            ['Bruna Tavares Lopes',        '02601712800', 'F', '1999-12-11', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOA',    '2023-02-01', 'bruna.lopes@supermercadobh.com.br',      '31991000026'],
            ['Felipe Nunes Ribeiro',       '02701812900', 'M', '2000-08-19', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOB',    '2023-04-15', 'felipe.ribeiro@supermercadobh.com.br',   '31991000027'],
            ['Larissa Fonseca Gomes',      '02801913000', 'F', '2001-06-04', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOC',    '2023-06-01', 'larissa.gomes@supermercadobh.com.br',    '31991000028'],
            ['Marcos Paulo Oliveira',      '02902013100', 'M', '1999-04-29', 'Operador(a) de Caixa',  'Checkout',        1800.00, 'TURNOA',    '2023-09-10', 'marcos.oliveira@supermercadobh.com.br',  '31991000029'],

            // ── Repositores ──────────────────────────────────────────
            ['Sebastião Morais Cunha',     '03002113200', 'M', '1990-01-17', 'Repositor(a)',           'Estoque',         1700.00, 'TURNOA',    '2019-05-01', 'sebastiao.cunha@supermercadobh.com.br',  '31991000030'],
            ['Nilza Aparecida Correia',    '03102213300', 'F', '1993-10-08', 'Repositor(a)',           'Estoque',         1700.00, 'TURNOB',    '2020-02-10', 'nilza.correia@supermercadobh.com.br',    '31991000031'],
            ['Wellington José Barbosa',    '03202313400', 'M', '1996-07-21', 'Repositor(a)',           'Estoque',         1700.00, 'TURNOC',    '2021-08-15', 'wellington.barbosa@supermercadobh.com.br','31991000032'],
            ['Simone Cristina Martins',    '03302413500', 'F', '1994-03-13', 'Repositor(a)',           'Estoque',         1700.00, 'TURNOA',    '2021-10-01', 'simone.martins@supermercadobh.com.br',   '31991000033'],
            ['Edson Carlos Vieira',        '03402513600', 'M', '1988-12-26', 'Repositor(a)',           'Estoque',         1700.00, 'TURNOB',    '2018-03-01', 'edson.vieira@supermercadobh.com.br',     '31991000034'],
            ['Tatiana Rocha Pires',        '03502613700', 'F', '1997-08-09', 'Repositor(a)',           'Estoque',         1700.00, 'TURNOC',    '2022-09-20', 'tatiana.pires@supermercadobh.com.br',    '31991000035'],
            ['Douglas Machado Freitas',    '03602713800', 'M', '2000-11-15', 'Repositor(a)',           'Estoque',         1700.00, 'TURNOA',    '2023-03-01', 'douglas.freitas@supermercadobh.com.br',  '31991000036'],
            ['Cíntia Pereira Araújo',      '03702813900', 'F', '1999-05-27', 'Auxiliar de Estoque',   'Estoque',         1600.00, 'TURNOB',    '2023-07-01', 'cintia.araujo@supermercadobh.com.br',    '31991000037'],
            ['Vitor Gonçalves Ramos',      '03802914000', 'M', '2002-02-10', 'Auxiliar de Estoque',   'Estoque',         1600.00, 'TURNOC',    '2024-01-15', 'vitor.ramos@supermercadobh.com.br',      '31991000038'],

            // ── Padaria e Confeitaria ─────────────────────────────────
            ['Antônio Carlos Rezende',     '03903014100', 'M', '1981-04-05', 'Padeiro(a)',             'Padaria',         2400.00, 'MADRUGADA', '2017-01-10', 'antonio.rezende@supermercadobh.com.br',  '31991000039'],
            ['Maria do Carmo Leal',        '04003114200', 'F', '1985-10-18', 'Padeiro(a)',             'Padaria',         2400.00, 'MADRUGADA', '2018-06-01', 'maria.leal@supermercadobh.com.br',       '31991000040'],
            ['Josefa Cristina Batista',    '04103214300', 'F', '1989-06-30', 'Confeiteiro(a)',         'Padaria',         2600.00, 'TURNOA',    '2019-04-01', 'josefa.batista@supermercadobh.com.br',   '31991000041'],
            ['Henrique Soares Dias',       '04203314400', 'M', '1993-02-14', 'Auxiliar de Padaria',   'Padaria',         1700.00, 'TURNOA',    '2022-05-01', 'henrique.dias@supermercadobh.com.br',    '31991000042'],

            // ── Açougue ──────────────────────────────────────────────
            ['Welington da Cruz Santos',   '04303414500', 'M', '1979-09-12', 'Açougueiro(a)',          'Açougue',         2800.00, 'TURNOA',    '2015-07-01', 'welington.santos@supermercadobh.com.br', '31991000043'],
            ['Rogério Mendes Coelho',      '04403514600', 'M', '1983-05-03', 'Açougueiro(a)',          'Açougue',         2800.00, 'TURNOB',    '2017-02-01', 'rogerio.coelho@supermercadobh.com.br',   '31991000044'],
            ['Cristiano Lima Faria',       '04503614700', 'M', '1991-12-19', 'Auxiliar de Açougue',   'Açougue',         1750.00, 'TURNOA',    '2021-09-01', 'cristiano.faria@supermercadobh.com.br',  '31991000045'],

            // ── Hortifruti ───────────────────────────────────────────
            ['Irene Nogueira Pereira',     '04603714800', 'F', '1987-08-14', 'Atendente de Hortifruti','Hortifruti',     1750.00, 'TURNOA',    '2020-11-01', 'irene.pereira@supermercadobh.com.br',    '31991000046'],
            ['Jonas Ferreira da Silva',    '04703814900', 'M', '1992-04-22', 'Atendente de Hortifruti','Hortifruti',     1750.00, 'TURNOB',    '2022-03-01', 'jonas.silva@supermercadobh.com.br',      '31991000047'],

            // ── Segurança ─────────────────────────────────────────────
            ['Cleber Augusto Moura',       '04803915000', 'M', '1980-07-31', 'Vigilante/Segurança',   'Segurança',       2200.00, 'TURNOA',    '2016-08-01', 'cleber.moura@supermercadobh.com.br',     '31991000048'],
            ['Igor Henrique Teles',        '04904015100', 'M', '1988-11-06', 'Vigilante/Segurança',   'Segurança',       2200.00, 'TURNOB',    '2019-03-15', 'igor.teles@supermercadobh.com.br',       '31991000049'],
            ['Jéssica Aparecida Souza',    '05004115200', 'F', '1995-03-27', 'Vigilante/Segurança',   'Segurança',       2200.00, 'TURNOC',    '2021-05-01', 'jessica.souza@supermercadobh.com.br',    '31991000050'],

            // ── Limpeza e Manutenção ──────────────────────────────────
            ['Geralda Maria Campos',       '05104215300', 'F', '1975-02-20', 'Auxiliar de Limpeza',   'Conservação',     1412.00, 'TURNOA',    '2018-09-01', 'geralda.campos@supermercadobh.com.br',   '31991000051'],
            ['Francisco das Chagas Lopes', '05204315400', 'M', '1977-08-13', 'Auxiliar de Limpeza',   'Conservação',     1412.00, 'TURNOB',    '2019-01-10', 'francisco.lopes@supermercadobh.com.br',  '31991000052'],
            ['Neuza Santos Carvalho',      '05304415500', 'F', '1982-05-09', 'Auxiliar de Limpeza',   'Conservação',     1412.00, 'TURNOC',    '2020-06-01', 'neuza.carvalho@supermercadobh.com.br',   '31991000053'],
            ['Paulo Roberto Mendes',       '05404515600', 'M', '1979-10-24', 'Técnico de Manutenção', 'Manutenção',      2800.00, 'ADM',       '2017-11-01', 'paulo.mendes@supermercadobh.com.br',     '31991000054'],

            // ── Logística ─────────────────────────────────────────────
            ['Evandro Costa Machado',      '05504615700', 'M', '1984-01-07', 'Motorista/Entregador',  'Logística',       2400.00, 'ADM',       '2018-05-01', 'evandro.machado@supermercadobh.com.br',  '31991000055'],
            ['Leonardo Dias Ferreira',     '05604715800', 'M', '1990-09-15', 'Motorista/Entregador',  'Logística',       2400.00, 'ADM',       '2020-10-01', 'leonardo.ferreira@supermercadobh.com.br','31991000056'],
            ['Marcelino Alves Ribeiro',    '05704815900', 'M', '1995-06-28', 'Auxiliar de Entrega',   'Logística',       1700.00, 'ADM',       '2023-05-15', 'marcelino.ribeiro@supermercadobh.com.br','31991000057'],

            // ── Marketing / Promotores ────────────────────────────────
            ['Tatiany Borges Freitas',     '05804916000', 'F', '1993-12-03', 'Promotor(a) de Vendas', 'Marketing',       1900.00, 'TURNOA',    '2022-02-01', 'tatiany.freitas@supermercadobh.com.br',  '31991000058'],
            ['Roberto Assis Lima',         '05905016100', 'M', '1996-04-17', 'Promotor(a) de Vendas', 'Marketing',       1900.00, 'TURNOB',    '2023-08-01', 'roberto.lima@supermercadobh.com.br',     '31991000059'],

            // ── SAC ───────────────────────────────────────────────────
            ['Priscila Andrade Torres',    '06005116200', 'F', '1994-07-22', 'Operador(a) de SAC',    'Atendimento',     2100.00, 'ADM',       '2021-04-01', 'priscila.torres@supermercadobh.com.br',  '31991000060'],
        ];

        $inserted = 0;
        foreach ($employees as [
            $name, $cpf, $gender, $birth, $role, $dept,
            $salary, $schedule, $admission, $email, $phone
        ]) {
            if (Employees::where('identification_number', $cpf)->exists()) continue;

            Employees::create([
                'id'                    => (string) Str::uuid(),
                'name'                  => $name,
                'identification_number' => $cpf,
                'gender'                => $gender === 'M' ? 'Masculino' : 'Feminino',
                'birth_date'            => $birth,
                'role'                  => $role,
                'department'            => $dept,
                'salary'                => $salary,
                'work_schedule'         => $schedule,
                'admission_date'        => $admission,
                'email'                 => $email,
                'phone_number'          => $phone,
                'address'               => 'Belo Horizonte, MG',
                'nationality'           => 'Brasileiro(a)',
                'country'               => 'Brasil',
                'city'                  => 'Belo Horizonte',
                'state'                 => 'MG',
                'street'                => 'Rua não informada',
                'zip_code'              => '30000000',
                'is_active'             => true,
                'allow_system_access'   => false,
                'marital_status'        => 'Solteiro(a)',
            ]);
            $inserted++;
        }

        $this->command->info("✅ {$inserted} funcionários semeados.");
    }
}


