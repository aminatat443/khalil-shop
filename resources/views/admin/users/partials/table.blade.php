@php
    $roleLabels = ['client' => 'Client', 'gestionnaire' => 'Gestionnaire', 'admin' => 'Administrateur', 'super_admin' => 'Super admin'];
@endphp

<div class="space-y-3 lg:hidden">
    @forelse($users as $user)
        <div class="bg-white p-4 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate font-medium text-secondary-shade dark:text-white">{{ $user->name }}</p>
                    <p class="truncate text-xs text-grey dark:text-white/50">{{ $user->email }}</p>
                </div>
                @if($user->isOnline())
                    <span class="inline-flex shrink-0 items-center gap-1.5 text-xs font-semibold text-green-600 dark:text-green-400">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>En ligne
                    </span>
                @else
                    <span class="inline-flex shrink-0 items-center gap-1.5 text-xs text-grey dark:text-white/40">
                        <span class="h-2 w-2 rounded-full bg-grey/40 dark:bg-white/20"></span>Hors ligne
                    </span>
                @endif
            </div>
            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2.5 border-t border-secondary-shade/10 pt-3 text-xs dark:border-white/10">
                <div>
                    <dt class="text-grey/60 dark:text-white/30">Rôle</dt>
                    <dd class="mt-0.5 text-secondary-shade dark:text-white">{{ $roleLabels[$user->role->value] ?? $user->role->value }}</dd>
                </div>
                <div>
                    <dt class="text-grey/60 dark:text-white/30">Compte créé</dt>
                    <dd class="mt-0.5 text-secondary-shade dark:text-white">{{ $user->created_at->format('d/m/Y') }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-grey/60 dark:text-white/30">Dernière connexion</dt>
                    <dd class="mt-0.5 text-secondary-shade dark:text-white">
                        {{ $user->last_login_at?->diffForHumans() ?? '—' }}
                        @if(! $user->isOnline() && $user->last_activity)
                            <span class="block text-grey/70 dark:text-white/30">Hors ligne depuis {{ \Illuminate\Support\Carbon::createFromTimestamp($user->last_activity)->diffForHumans() }}</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    @empty
        <div class="bg-white px-6 py-10 text-center text-sm text-grey shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:text-white/40 dark:ring-white/5">Aucun compte ne correspond à ces critères.</div>
    @endforelse
</div>

<div class="hidden overflow-x-auto bg-white shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5 lg:block">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey dark:border-white/10 dark:text-white/40">
                <th class="px-6 py-4 font-medium">Nom</th>
                <th class="px-6 py-4 font-medium">Email</th>
                <th class="px-6 py-4 font-medium">Rôle</th>
                <th class="px-6 py-4 font-medium">Statut</th>
                <th class="px-6 py-4 font-medium">Dernière connexion</th>
                <th class="px-6 py-4 font-medium">Compte créé</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10 dark:divide-white/10">
            @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 font-medium text-secondary-shade dark:text-white">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-grey dark:text-white/50">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-secondary-shade dark:text-white">{{ $roleLabels[$user->role->value] ?? $user->role->value }}</td>
                    <td class="px-6 py-4">
                        @if($user->isOnline())
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-600 dark:text-green-400">
                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                En ligne
                            </span>
                        @elseif($user->last_activity)
                            <span class="inline-flex items-center gap-1.5 text-xs text-grey dark:text-white/40">
                                <span class="h-2 w-2 rounded-full bg-grey/40 dark:bg-white/20"></span>
                                Hors ligne depuis {{ \Illuminate\Support\Carbon::createFromTimestamp($user->last_activity)->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-xs text-grey/60 dark:text-white/30">Jamais connecté</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-grey dark:text-white/40">
                        {{ $user->last_login_at?->diffForHumans() ?? '—' }}
                    </td>
                    <td class="px-6 py-4 text-xs text-grey dark:text-white/40">{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-grey dark:text-white/40">Aucun compte ne correspond à ces critères.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6" data-pagination>{{ $users->links() }}</div>
