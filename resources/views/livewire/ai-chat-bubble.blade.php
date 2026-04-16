<div
    x-data="{
        open: false,
        init() {
            // Notifica o Livewire quando a URL mudar (navegação SPA/Livewire)
            const observer = new MutationObserver(() => {
                const path = window.location.pathname.replace(/^\//, '');
                $wire.updatePath(path);
            });
            observer.observe(document.querySelector('title') ?? document.body, {
                subtree: true, childList: true, characterData: true
            });

            // Scroll ao abrir
            this.$watch('open', val => {
                if (val) {
                    this.$nextTick(() => this.scrollBottom());
                }
            });

            // Eventos Livewire
            Livewire.on('scrollToBottom', () => this.$nextTick(() => this.scrollBottom()));
            Livewire.on('focusInput', () => this.$nextTick(() => this.$refs.input?.focus()));
        },
        scrollBottom() {
            const el = this.$refs.messages;
            if (el) el.scrollTop = el.scrollHeight;
        }
    }"
    class="fixed bottom-6 right-6 z-[9999]"
>

    {{-- ── Botão Flutuante ─────────────────────────────────────── --}}
    <button
        @click="open = !open"
        title="Assistente IA — {{ $this->moduleName }}"
        class="relative group flex items-center justify-center w-14 h-14 rounded-full shadow-2xl
               bg-gradient-to-br from-violet-600 to-indigo-600
               hover:from-violet-500 hover:to-indigo-500
               active:scale-95 transition-all duration-200 focus:outline-none
               focus:ring-4 focus:ring-violet-300"
    >
        {{-- Ícone IA (sparkle / bot) --}}
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
        </svg>
        {{-- Ícone X ao abrir --}}
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18 18 6M6 6l12 12"/>
        </svg>

        {{-- Badge com número de mensagens --}}
        @if(count($messages) > 0)
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                {{ min(count($messages), 99) }}
            </span>
        @else
            {{-- Ping animado quando fechado e sem mensagens --}}
            <span x-show="!open" class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
        @endif
    </button>

    {{-- ── Janela do Chat ──────────────────────────────────────── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="absolute bottom-16 right-0 w-[370px] max-h-[580px] flex flex-col
               bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden"
        style="display: none;"
    >

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 text-white flex-shrink-0">
            <div class="flex items-center gap-2.5">
                {{-- Ícone IA no header --}}
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold leading-tight">Assistente Nexora</p>
                    <p class="text-[11px] opacity-80 leading-tight">
                        {{ $pageName ?: $this->moduleName }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                {{-- Limpar conversa --}}
                @if(count($messages) > 0)
                    <button
                        wire:click="clearChat"
                        title="Limpar conversa"
                        class="p-1.5 rounded-lg hover:bg-white/20 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                    </button>
                @endif
                {{-- Fechar --}}
                <button @click="open = false" class="p-1.5 rounded-lg hover:bg-white/20 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mensagens --}}
        <div
            x-ref="messages"
            class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 min-h-0"
            style="max-height: 380px;"
        >
            {{-- Estado inicial: sem mensagens --}}
            @if(count($messages) === 0)
                <div class="flex flex-col items-center justify-center h-full text-center py-6 px-2 gap-4">
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-violet-100 to-indigo-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Olá, sou o Assistente Nexora!</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Estou especializado em <span class="font-medium text-violet-600">{{ $pageName ?: $this->moduleName }}</span>.<br>
                            Como posso ajudar?
                        </p>
                    </div>
                    {{-- Sugestões --}}
                    <div class="w-full space-y-1.5">
                        @foreach($this->suggestions as $s)
                            <button
                                wire:click="useSuggestion('{{ addslashes($s) }}')"
                                class="w-full text-left text-xs px-3 py-2 bg-white border border-gray-200 rounded-xl
                                       hover:border-violet-400 hover:bg-violet-50 transition text-gray-700"
                            >
                                {{ $s }}
                            </button>
                        @endforeach
                    </div>
                </div>

            @else
                {{-- Lista de mensagens --}}
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} items-end gap-2">

                        {{-- Avatar IA --}}
                        @if($msg['role'] !== 'user')
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                                </svg>
                            </div>
                        @endif

                        <div class="max-w-[82%]">
                            <div class="px-3 py-2 rounded-2xl text-sm leading-relaxed
                                {{ $msg['role'] === 'user'
                                    ? 'bg-gradient-to-br from-violet-600 to-indigo-600 text-white rounded-br-none'
                                    : ($msg['role'] === 'error'
                                        ? 'bg-red-50 text-red-700 border border-red-200 rounded-bl-none'
                                        : 'bg-white text-gray-800 border border-gray-200 shadow-sm rounded-bl-none')
                                }}"
                            >
                                <p class="whitespace-pre-wrap break-words">{{ $msg['content'] }}</p>
                            </div>
                            @if(isset($msg['timestamp']))
                                <p class="text-[10px] text-gray-400 mt-0.5 {{ $msg['role'] === 'user' ? 'text-right' : 'text-left' }}">
                                    {{ $msg['timestamp'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Loading --}}
                @if($isLoading)
                    <div class="flex items-end gap-2 justify-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-1 bg-white border border-gray-200 shadow-sm rounded-2xl rounded-bl-none px-4 py-3">
                            <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Input --}}
        <div class="px-3 py-3 bg-white border-t border-gray-100 flex-shrink-0">
            <form wire:submit.prevent="sendMessage" class="flex items-end gap-2">
                <div class="flex-1">
                    <textarea
                        wire:model.defer="userInput"
                        x-ref="input"
                        rows="1"
                        maxlength="1000"
                        placeholder="Pergunte sobre {{ $pageName ?: $this->moduleName }}…"
                        class="w-full resize-none text-sm border border-gray-200 rounded-xl px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent
                               placeholder-gray-400 transition"
                        @keydown.enter.prevent="if(!$event.shiftKey) $wire.sendMessage()"
                        @input="$el.style.height='auto'; $el.style.height=Math.min($el.scrollHeight,120)+'px'"
                        :disabled="$wire.isLoading"
                    ></textarea>
                    @error('userInput')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="submit"
                    :disabled="$wire.isLoading"
                    class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-xl
                           bg-gradient-to-br from-violet-600 to-indigo-600 text-white
                           hover:from-violet-500 hover:to-indigo-500 active:scale-95
                           disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                    </svg>
                </button>
            </form>
            <p class="text-[10px] text-gray-400 text-center mt-1.5">
                Powered by <span class="font-medium text-violet-500">Gemini AI</span> · <kbd class="px-1 bg-gray-100 border border-gray-300 rounded text-[10px]">Enter</kbd> para enviar
            </p>
        </div>
    </div>
</div>
