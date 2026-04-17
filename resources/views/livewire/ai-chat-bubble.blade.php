<div
    x-data="{
        open: false,
        init() {
            const observer = new MutationObserver(() => {
                const path = window.location.pathname.replace(/^\//, '');
                $wire.updatePath(path);
            });
            observer.observe(document.querySelector('title') ?? document.body, {
                subtree: true, childList: true, characterData: true
            });
            this.$watch('open', val => {
                if (val) this.$nextTick(() => this.scrollBottom());
            });
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
    {{-- ── Botão Flutuante ──────────────────────────────────────────────── --}}
    <button
        @click="open = !open"
        title="Assistente IA"
        class="relative flex items-center justify-center w-14 h-14 rounded-full shadow-2xl
               bg-gradient-to-br from-violet-600 to-indigo-600
               hover:from-violet-500 hover:to-indigo-500
               active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-violet-300"
    >
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
        </svg>
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18 18 6M6 6l12 12"/>
        </svg>

        @if(count($messages) > 0)
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                {{ min(count($messages), 99) }}
            </span>
        @else
            <span x-show="!open" class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
        @endif
    </button>

    {{-- ── Janela do Chat (fixed, sempre visível no canto) ─────────────── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed flex flex-col rounded-2xl shadow-2xl border border-gray-200 bg-white overflow-hidden"
        style="display:none; position:fixed; bottom:5.5rem; right:1.5rem; width:420px; height:580px; transform-origin:bottom right; z-index:9998;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-violet-600 to-indigo-600 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-full bg-white/20 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-semibold leading-tight" style="color:#ffffff;">Assistente Nexora</p>
                    <p class="text-xs leading-tight mt-0.5" style="color:rgba(255,255,255,0.85);">{{ $pageName ?: $this->moduleName }}</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                @if(count($messages) > 0)
                    <button wire:click="clearChat" title="Limpar conversa" class="p-2 rounded-lg hover:bg-white/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                    </button>
                @endif
                <button @click="open = false" class="p-2 rounded-lg hover:bg-white/20 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mensagens --}}
        <div
            x-ref="messages"
            class="flex-1 overflow-y-auto bg-gray-50 min-h-0"
            style="padding:20px 16px; display:flex; flex-direction:column; gap:16px;"
        >
            @if(count($messages) === 0)
                {{-- Tela inicial --}}
                <div class="flex flex-col items-center justify-center h-full text-center gap-5 py-4">
                    <div class="flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-violet-100 to-indigo-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-base">Olá! Sou o Assistente Nexora</p>
                        <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">
                            Especializado em <span class="font-medium text-violet-600">{{ $pageName ?: $this->moduleName }}</span>.<br>
                            Como posso ajudar?
                        </p>
                    </div>
                    <div class="w-full flex flex-col gap-2">
                        @foreach($this->suggestions as $s)
                            <button
                                wire:click="useSuggestion('{{ addslashes($s) }}')"
                                class="w-full text-left text-sm px-4 py-2.5 bg-white border border-gray-200 rounded-xl
                                       hover:border-violet-400 hover:bg-violet-50 transition text-gray-700 leading-snug"
                            >{{ $s }}</button>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Lista de mensagens --}}
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} items-end gap-2.5">

                        {{-- Avatar IA --}}
                        @if($msg['role'] !== 'user')
                            <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Balão --}}
                        <div class="max-w-[78%] flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">
                            <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed"
                                style="
                                    @if($msg['role'] === 'user')
                                        background: linear-gradient(135deg, #7c3aed, #4338ca); border-radius: 1rem 1rem 0.25rem 1rem;
                                    @elseif($msg['role'] === 'error')
                                        background:#fef2f2; border:1px solid #fecaca; border-radius: 1rem 1rem 1rem 0.25rem;
                                    @else
                                        background:#ffffff; border:1px solid #e5e7eb; box-shadow:0 1px 2px rgba(0,0,0,0.05); border-radius: 1rem 1rem 1rem 0.25rem;
                                    @endif
                                "
                            >
                                <p class="whitespace-pre-wrap break-words"
                                   style="color: @if($msg['role'] === 'user') #ffffff @elseif($msg['role'] === 'error') #b91c1c @else #1f2937 @endif;">{{ $msg['content'] }}</p>
                            </div>
                            @if(isset($msg['timestamp']))
                                <p class="text-[11px] text-gray-400 mt-1 px-1">{{ $msg['timestamp'] }}</p>
                            @endif
                        </div>

                        {{-- Avatar Usuário --}}
                        @if($msg['role'] === 'user')
                            <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-300 flex items-center justify-center shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- Loading --}}
                @if($isLoading)
                    <div class="flex items-end gap-2.5 justify-start">
                        <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-1.5 bg-white border border-gray-200 shadow-sm rounded-2xl rounded-bl-sm px-5 py-3.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-violet-400 animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-violet-400 animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-violet-400 animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Input --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex-shrink-0">
            <form wire:submit.prevent="sendMessage" class="flex items-end gap-2">
                <div class="flex-1">
                    <textarea
                        wire:model.defer="userInput"
                        x-ref="input"
                        rows="2"
                        maxlength="1000"
                        placeholder="Pergunte sobre {{ $pageName ?: $this->moduleName }}…"
                        class="w-full resize-none text-sm border border-gray-300 rounded-xl px-4 py-2.5
                               focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent
                               placeholder-gray-400 transition leading-relaxed bg-gray-50 focus:bg-white"
                        @keydown.enter.prevent="if(!$event.shiftKey) $wire.sendMessage()"
                        @input="$el.style.height='auto'; $el.style.height=Math.min($el.scrollHeight,140)+'px'"
                        :disabled="$wire.isLoading"
                    ></textarea>
                    @error('userInput')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="submit"
                    :disabled="$wire.isLoading"
                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl
                           bg-gradient-to-br from-violet-600 to-indigo-600 text-white
                           hover:from-violet-500 hover:to-indigo-500 active:scale-95
                           disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-md"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                    </svg>
                </button>
            </form>
            <p class="text-[11px] text-gray-400 text-center mt-2">
                Powered by <span class="font-semibold text-violet-500">Gemini AI</span>
                &nbsp;·&nbsp;
                <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-300 rounded text-[10px] font-mono">Enter</kbd> envia
                &nbsp;·&nbsp;
                <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-300 rounded text-[10px] font-mono">Shift+Enter</kbd> quebra linha
            </p>
        </div>
    </div>
</div>
