<?php

namespace App\Livewire\Forms;

use App\Enums\TimeRecordStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TimeRecordForm extends Form
{
    #[Validate('required|exists:employees,id')]
    public string $employee_id = '';

    #[Validate('nullable|exists:work_shifts,id')]
    public ?int $work_shift_id = null;

    #[Validate('required|date')]
    public string $date = '';

    #[Validate('nullable|date_format:H:i')]
    public string $clock_in = '';

    #[Validate('nullable|date_format:H:i')]
    public string $break_start = '';

    #[Validate('nullable|date_format:H:i')]
    public string $break_end = '';

    #[Validate('nullable|date_format:H:i')]
    public string $clock_out = '';

    #[Validate('required|in:active,break,absent,completed')]
    public string $status = TimeRecordStatus::Absent->value;

    #[Validate('nullable|string|max:500')]
    public string $observation = '';
}

