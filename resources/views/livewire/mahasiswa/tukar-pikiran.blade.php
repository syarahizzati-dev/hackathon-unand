<div class="space-y-4" wire:key="tukar-pikiran">
    {{-- Header Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">Tukar Pikiran</h2>
                <p class="text-sm text-slate-500 mt-0.5">Berbagi dan saling mendukung secara anonim</p>
            </div>
            <button wire:click="toggleForm"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-blue-800 text-white font-medium text-sm px-4 py-2.5 rounded-lg hover:bg-blue-900 transition-colors min-h-[44px] shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Posting Baru
            </button>
        </div>
    </div>

    {{-- Form Posting Baru --}}
    @if($showForm)
    <div class="bg-white rounded-xl border border-blue-200 shadow-sm p-5" x-data x-transition>
        <div class="flex items-center gap-2 mb-3 text-sm text-slate-500">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
            Kamu akan muncul sebagai: <span class="font-medium text-blue-700">{{ Auth::user()->username_anonim }}</span>
        </div>
        <form wire:submit="posting">
            <textarea wire:model="newPost" rows="3" placeholder="Bagikan pikiran atau perasaan kamu..."
                      class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none min-h-[44px]"></textarea>
            @error('newPost')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            <div class="flex items-center justify-end gap-3 mt-3">
                <button type="button" wire:click="toggleForm" class="text-sm text-slate-500 hover:text-slate-700 px-4 py-2 min-h-[44px]">Batal</button>
                <button type="submit" class="bg-blue-800 text-white font-medium text-sm px-5 py-2.5 rounded-lg hover:bg-blue-900 transition-colors min-h-[44px]">Posting</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Daftar Postingan --}}
    @forelse($posts as $post)
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5" wire:key="post-{{ $post->id }}">
        {{-- Post Content --}}
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-medium text-blue-700">{{ mb_substr($post->user->username_anonim ?? 'A', 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-medium text-slate-800">{{ $post->user->username_anonim ?? 'Anonim' }}</span>
                    <span class="text-xs text-slate-400">{{ $post->created_at->translatedFormat('d M') }}, {{ $post->created_at->format('H.i') }}</span>
                </div>
                <p class="mt-1 text-sm text-slate-700 leading-relaxed">{{ $post->konten }}</p>
            </div>
        </div>

        {{-- Actions: Like + Reply --}}
        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-slate-100">
            <button wire:click="toggleLike({{ $post->id }})" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-red-500 transition-colors min-h-[36px]">
                @if($post->likes->where('user_id', Auth::id())->count() > 0)
                <svg class="w-4 h-4 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                @else
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                @endif
                <span>{{ $post->likes_count }}</span>
            </button>
            <button wire:click="startReply({{ $post->id }})" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-600 transition-colors min-h-[36px]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z"/></svg>
                <span>{{ $post->replies_count }} balasan</span>
            </button>
        </div>

        {{-- Replies --}}
        @if($post->replies->count() > 0)
        <div class="mt-3 ml-2 pl-3 sm:ml-4 sm:pl-4 border-l-2 border-slate-200 space-y-3">
            @foreach($post->replies as $reply)
            <div wire:key="reply-{{ $reply->id }}" class="space-y-2">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-medium text-slate-500">{{ mb_substr($reply->user->username_anonim ?? 'A', 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-medium text-slate-800">{{ $reply->user->username_anonim ?? 'Anonim' }}</span>
                            <span class="text-xs text-slate-400">{{ $reply->created_at->translatedFormat('d M') }}, {{ $reply->created_at->format('H.i') }}</span>
                        </div>
                        <p class="mt-0.5 text-sm text-slate-600">{{ $reply->konten }}</p>
                        <button wire:click="startReplyToReply({{ $post->id }}, {{ $reply->id }})" class="mt-1 text-xs font-medium text-blue-600 hover:text-blue-800 min-h-[28px]">Balas</button>
                    </div>
                </div>

                @if($replyingToReply === $reply->id)
                <div class="ml-2 sm:ml-11" x-data x-transition>
                    <form wire:submit="kirimBalasan({{ $post->id }}, {{ $reply->id }})" class="flex flex-col sm:flex-row sm:items-end gap-2">
                        <input type="text" wire:model="replyText" placeholder="Balas {{ $reply->user->username_anonim ?? 'Anonim' }}..." autofocus
                               class="flex-1 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent min-h-[44px]">
                        <button type="submit" class="bg-blue-700 text-white p-2.5 rounded-lg hover:bg-blue-800 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                        </button>
                    </form>
                    @error('replyText')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                @endif

                @if($reply->children->count() > 0)
                <div class="ml-2 pl-3 sm:ml-11 border-l border-blue-100 space-y-2">
                    @foreach($reply->children as $child)
                    <div class="flex items-start gap-2" wire:key="reply-child-{{ $child->id }}">
                        <div class="w-7 h-7 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-[11px] font-medium text-blue-600">{{ mb_substr($child->user->username_anonim ?? 'A', 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-medium text-slate-800">{{ $child->user->username_anonim ?? 'Anonim' }}</span>
                                <span class="text-xs text-slate-400">{{ $child->created_at->translatedFormat('d M') }}, {{ $child->created_at->format('H.i') }}</span>
                            </div>
                            <p class="mt-0.5 text-sm text-slate-600">{{ $child->konten }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Reply Input --}}
        @if($replyingToPost === $post->id && $replyingToReply === null)
        <div class="mt-3 ml-2 pl-3 sm:ml-4 sm:pl-4 border-l-2 border-blue-200" x-data x-transition>
            <form wire:submit="kirimBalasan({{ $post->id }})" class="flex flex-col sm:flex-row sm:items-end gap-2">
                <input type="text" wire:model="replyText" placeholder="Tulis balasan..." autofocus
                        class="flex-1 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent min-h-[44px]"
                        @keydown.enter.exact.prevent="$wire.kirimBalasan({{ $post->id }})">
                <button type="submit" class="bg-blue-700 text-white p-2.5 rounded-lg hover:bg-blue-800 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                </button>
            </form>
            @error('replyText')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
        <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
        <p class="text-slate-500 font-medium">Belum ada postingan.</p>
        <p class="text-slate-400 text-sm mt-1">Jadilah yang pertama berbagi pikiran!</p>
    </div>
    @endforelse
</div>
