<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfigurationRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConfigurationController extends Controller
{
    /* ─────────────────────────────────────────
     | Exibe a página de configurações
     ───────────────────────────────────────── */
    public function index()
    {
        try {
            $settings = Setting::allKeyed();
        } catch (\Throwable $e) {
            // A tabela settings ainda não existe (migrate pendente)
            $settings = [];
        }

        return view('admin.settings.index', compact('settings'));
    }

    /* ─────────────────────────────────────────
     | Salva todas as configurações
     ───────────────────────────────────────── */
    public function store(StoreConfigurationRequest $request)
    {
        $groups = [
            'general'       => ['system_name','system_slogan','timezone','language','date_format','time_format'],
            'company'       => ['company_name','company_fantasy','company_cnpj','company_ie','company_address','company_number','company_city','company_state','company_zipcode','company_email','company_phone'],
            'financial'     => ['currency','decimal_separator','thousand_separator','default_tax'],
            'notifications' => ['notify_low_stock','notify_welcome_email','notify_browser','whatsapp_api_key'],
            'appearance'    => ['theme','primary_color','ui_density','sidebar_default'],
            'security'      => ['session_timeout','password_strength','maintenance_mode','activity_log'],
            'stock'         => ['allow_sale_no_stock','stock_reserve_moment','critical_stock_percent'],
            'fiscal'        => ['default_cfop','auto_emit_nfe','emission_environment','realtime_tax_calc'],
            'sales'         => ['allow_negative_margin','max_discount_percent','active_price_table','quote_validity_days','sale_type','require_cpf_on_note'],
            'kpi'           => ['kpi_meta_faturamento_mensal','kpi_meta_pedidos_mes','kpi_meta_ticket_medio'],
        ];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                // Toggles/checkboxes: se não vier no request, assume '0'
                $toggles = [
                    'notify_low_stock','notify_welcome_email','notify_browser',
                    'password_strength','maintenance_mode','activity_log',
                    'auto_emit_nfe','realtime_tax_calc',
                    'allow_negative_margin','require_cpf_on_note',
                ];
                $value = in_array($key, $toggles)
                    ? ($request->boolean($key) ? '1' : '0')
                    : $request->input($key, '');

                Setting::set($key, $value, $group);
            }
        }

        // Invalida todo o cache de configurações
        Cache::flush();

        return redirect()
            ->route('configuration.index')
            ->with('success', 'Configurações salvas com sucesso!');
    }
}
