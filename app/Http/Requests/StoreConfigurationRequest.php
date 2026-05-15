<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfigurationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'system_name'                  => 'required|string|max:100',
            'system_slogan'                => 'nullable|string|max:255',
            'timezone'                     => 'required|string',
            'language'                     => 'required|string',
            'date_format'                  => 'required|string',
            'time_format'                  => 'required|string',

            'company_name'                 => 'nullable|string|max:255',
            'company_fantasy'              => 'nullable|string|max:255',
            'company_cnpj'                 => 'nullable|string|max:18',
            'company_ie'                   => 'nullable|string|max:30',
            'company_address'              => 'nullable|string|max:255',
            'company_number'               => 'nullable|string|max:20',
            'company_city'                 => 'nullable|string|max:100',
            'company_state'                => 'nullable|string|max:2',
            'company_zipcode'              => 'nullable|string|max:10',
            'company_email'                => 'nullable|email|max:150',
            'company_phone'                => 'nullable|string|max:20',

            'currency'                     => 'required|string',
            'decimal_separator'            => 'required|string',
            'thousand_separator'           => 'required|string',
            'default_tax'                  => 'nullable|numeric|min:0|max:100',

            'theme'                        => 'required|in:light,dark,system',
            'primary_color'                => 'required|string',
            'ui_density'                   => 'required|in:comfortable,compact',
            'sidebar_default'              => 'required|in:expanded,collapsed',

            'session_timeout'              => 'required|integer',
            'whatsapp_api_key'             => 'nullable|string|max:255',

            'allow_sale_no_stock'          => 'required|in:sim,nao,autorizar',
            'stock_reserve_moment'         => 'required|in:pedido,nota',
            'critical_stock_percent'       => 'nullable|integer|min:0|max:100',

            'default_cfop'                 => 'nullable|string|max:10',
            'emission_environment'         => 'required|in:homologacao,producao',

            'max_discount_percent'         => 'nullable|numeric|min:0|max:100',
            'active_price_table'           => 'required|in:varejo,atacado,promocional',
            'quote_validity_days'          => 'nullable|integer|min:1|max:365',
            'sale_type'                    => 'required|in:gerencial,fiscal,hibrido',

            'kpi_meta_faturamento_mensal'  => 'nullable|numeric|min:0',
            'kpi_meta_pedidos_mes'         => 'nullable|integer|min:0',
            'kpi_meta_ticket_medio'        => 'nullable|numeric|min:0',
        ];
    }
}

