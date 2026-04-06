<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class EmployeeForm extends Form
{
    // ── Dados Pessoais ──────────────────────────────
    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $social_name = '';

    #[Validate('required|string|max:14')]
    public string $identification_number = '';

    public string $rg = '';
    public string $rg_issuer = '';
    public string $rg_date = '';
    public string $birth_date = '';
    public string $gender = '';
    public string $marital_status = '';
    public string $nationality = 'Brasileiro(a)';
    public string $birthplace = '';

    // ── Contato ──────────────────────────────────────
    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:20')]
    public string $phone_number = '';

    public string $phone_secondary = '';

    // ── Endereço ─────────────────────────────────────
    public string $zip_code = '';
    public string $street = '';
    public string $number = '';
    public string $complement = '';
    public string $neighborhood = '';
    public string $city = '';
    public string $state = '';
    public string $country = 'Brasil';

    // Campo legado (endereço concatenado — mantido para compatibilidade)
    public string $address = '';

    // ── Contato de Emergência ────────────────────────
    public string $emergency_contact_name = '';
    public string $emergency_contact_relationship = '';
    public string $emergency_contact_phone = '';

    // ── Profissional ─────────────────────────────────
    #[Validate('required|string|max:255')]
    public string $role = '';

    public string $department = '';
    public string $access_profile = '';
    public bool   $is_active = true;
    public string $admission_date = '';
    public string $work_schedule = '';
    public bool   $allow_system_access = false;
    public string $salary = '';
    public string $internal_code = '';

    // ── Observações ──────────────────────────────────
    public string $observations = '';
}

