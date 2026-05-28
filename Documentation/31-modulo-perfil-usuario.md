# Módulo de Perfil do Usuário

## 1. Visão Geral

Permite que usuários gerenciem suas próprias informações, senha, avatar e preferências do sistema.

### Funcionalidades
- Atualização de dados pessoais
- Troca de senha
- Upload de avatar
- Preferências de interface
- Histórico de atividades
- Sessões ativas

---

## 2. Estrutura

### Livewire: `Profile`
```php
class Profile extends Component
{
    public $name;
    public $email;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    public $avatar;
    
    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);
        
        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
    
    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        auth()->user()->update([
            'password' => Hash::make($this->new_password),
        ]);
    }
    
    public function updateAvatar()
    {
        $this->validate(['avatar' => 'required|image|max:2048']);
        
        $path = $this->avatar->store('avatars', 'public');
        
        auth()->user()->update(['avatar' => $path]);
    }
}
```

---

## 3. View (Abas)
- **Dados Pessoais**: Nome, email, telefone
- **Segurança**: Troca de senha, 2FA (futuro)
- **Avatar**: Upload de foto
- **Preferências**: Tema, idioma, notificações
- **Atividades**: Histórico de login e ações

---

**Caminho**: `app/Livewire/Perfil/Profile.php`, `routes/perfil.php`

