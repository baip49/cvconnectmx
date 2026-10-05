<div wire:poll.5s class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#0b141a]">
    <div class="flex items-center gap-3 border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ $ticket->title }}
            </p>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                {{ $ticket->creator?->name ?? 'Sistema' }} · Ticket #{{ $ticket->id }} · {{ $ticket->created_at?->format('d/m/Y H:i') }} · {{ \App\Support\TicketLabels::statuses()[$ticket->status] ?? $ticket->status }} · {{ \App\Support\TicketLabels::levels()[$ticket->level] ?? $ticket->level }}
            </p>
        </div>

        @if ($canEdit)
            <div x-data="{ open: false }" class="relative shrink-0">
                <button
                    type="button"
                    x-on:click="open = ! open"
                    aria-label="Opciones del ticket"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-white/10"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                        <path fill-rule="evenodd" d="M10.5 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm0 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm0 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-on:click.away="open = false"
                    class="absolute right-0 z-20 mt-1 w-48 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg dark:border-white/10 dark:bg-[#1f2c34]"
                >
                    <button
                        type="button"
                        wire:click="startEdit"
                        x-on:click="open = false"
                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/10"
                    >
                        Editar ticket
                    </button>
                </div>
            </div>
        @endif
    </div>

    @if ($editing)
        <form wire:submit="saveEdit" class="space-y-4 border-b border-gray-200 bg-gray-50 px-4 py-4 dark:border-white/10 dark:bg-white/5">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Asunto</label>
                <input type="text" wire:model="editTitle" maxlength="255" class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-white/10 dark:bg-white/10 dark:text-gray-100 dark:placeholder-gray-400" />
                @error('editTitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipo</label>
                    <div class="relative">
                        <select wire:model="editType" class="block w-full appearance-none rounded-xl border border-gray-300 bg-white py-2 pl-3 pr-9 text-sm text-gray-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-white/10 dark:bg-white/10 dark:text-gray-100 dark:[color-scheme:dark] dark:[&>option]:bg-[#1f2c34]">
                            @foreach ($typeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    @error('editType') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Urgencia</label>
                    <div class="relative">
                        <select wire:model="editLevel" class="block w-full appearance-none rounded-xl border border-gray-300 bg-white py-2 pl-3 pr-9 text-sm text-gray-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-white/10 dark:bg-white/10 dark:text-gray-100 dark:[color-scheme:dark] dark:[&>option]:bg-[#1f2c34]">
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    @error('editLevel') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" wire:click="$set('editing', false)" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-white/10">
                    Cancelar
                </button>
                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                    Guardar
                </button>
            </div>
        </form>
    @endif

    <div
        x-data
        x-init="const box = $el; new MutationObserver(() => { box.scrollTop = box.scrollHeight; }).observe(box, { childList: true }); box.scrollTop = box.scrollHeight;"
        class="flex h-[calc(100dvh-25rem)] min-h-80 flex-col gap-2 overflow-y-auto overscroll-contain bg-[#ece5dd] px-4 py-4 dark:bg-[#0b141a]"
    >
        @php($descriptionIsMine = $ticket->created_by === auth()->id())
        <div class="flex justify-center">
            <div class="rounded-full bg-black/10 px-4 py-1 text-center text-[11px] font-medium text-gray-600 dark:bg-white/10 dark:text-gray-300">
                Ticket creado el {{ $ticket->created_at?->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="flex items-end gap-2 {{ $descriptionIsMine ? 'justify-end' : 'justify-start' }}">
            @unless ($descriptionIsMine)
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white" style="background-color: {{ $ticket->creator?->avatarColorHex() ?? '#6b7280' }}" title="{{ $ticket->creator?->name ?? 'Sistema' }}">
                    {{ $ticket->creator?->initials() ?? 'S' }}
                </div>
            @endunless
            <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm shadow-sm {{ $descriptionIsMine ? 'rounded-br-sm bg-[#d9fdd3] text-gray-900 dark:bg-[#005c4b] dark:text-gray-100' : 'rounded-bl-sm bg-white text-gray-800 dark:bg-[#1f2c34] dark:text-gray-100' }}">
                <p class="mb-1 text-[11px] font-semibold {{ $descriptionIsMine ? 'text-emerald-800 dark:text-emerald-300' : 'text-emerald-700 dark:text-emerald-400' }}">
                    {{ $ticket->creator?->name ?? 'Sistema' }} · {{ $ticket->created_at?->format('d/m H:i') }}
                </p>
                <p class="whitespace-pre-line">{{ $ticket->description }}</p>
            </div>
        </div>

        @foreach ($replies as $reply)
            @if ($reply->is_internal && ! $isAttendant)
                @continue
            @endif

            @if ($reply->performed_by === null)
                <div class="flex justify-center">
                    <div class="inline-flex max-w-[85%] items-center gap-2 rounded-full bg-black/10 px-4 py-1 text-center text-[11px] font-medium text-gray-600 dark:bg-white/10 dark:text-gray-300">
                        <img src="{{ asset('favicon.svg') }}" alt="Sistema" class="h-3.5 w-3.5 shrink-0" />
                        <span>{{ $reply->message }} · {{ $reply->created_at?->format('d/m H:i') }}</span>
                    </div>
                </div>
                @continue
            @endif

            @php($isMine = $reply->performed_by === auth()->id())
            <div class="flex items-end gap-2 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                @unless ($isMine)
                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white" style="background-color: {{ $reply->performedBy->avatarColorHex() }}" title="{{ $reply->performedBy->name }}">
                        {{ $reply->performedBy->initials() }}
                    </div>
                @endunless
                <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm shadow-sm {{ $isMine ? 'rounded-br-sm bg-[#d9fdd3] text-gray-900 dark:bg-[#005c4b] dark:text-gray-100' : 'rounded-bl-sm bg-white text-gray-800 dark:bg-[#1f2c34] dark:text-gray-100' }}">
                    <p class="mb-1 text-[11px] font-semibold {{ $isMine ? 'text-emerald-800 dark:text-emerald-300' : 'text-emerald-700 dark:text-emerald-400' }}">
                        {{ $reply->performedBy?->name ?? 'Sistema' }} · {{ $reply->created_at?->format('d/m H:i') }}
                    </p>
                    <p class="whitespace-pre-line">{{ $reply->message }}</p>
                </div>
            </div>
        @endforeach
    </div>

    @if ($canReply)
        <form wire:submit="send" class="flex items-center gap-2 bg-gray-100 px-4 py-3 dark:bg-white/5">
            <input
                type="text"
                wire:model="message"
                maxlength="2000"
                placeholder="Escribe tu mensaje..."
                autocomplete="off"
                class="block w-full rounded-full border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-white/10 dark:bg-white/10 dark:text-gray-100 dark:placeholder-gray-400"
            />
            <button
                type="submit"
                aria-label="Enviar mensaje"
                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white shadow-sm transition hover:bg-emerald-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                    <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.594.75.75 0 000-1.212A60.517 60.517 0 003.478 2.405z" />
                </svg>
            </button>
        </form>
        @error('message')
            <p class="bg-gray-100 px-4 pb-2 text-xs text-red-600 dark:bg-white/5">{{ $message }}</p>
        @enderror
    @else
        <p class="bg-gray-100 px-4 py-3 text-center text-sm text-gray-500 dark:bg-white/5 dark:text-gray-400">
            Este ticket está cerrado. Ya no se pueden enviar mensajes.
        </p>
    @endif
</div>
