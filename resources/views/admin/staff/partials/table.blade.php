@php
    $roleLabels = ['super_admin' => 'Super Administrateur', 'admin' => 'Administrateur', 'gestionnaire' => 'Gestionnaire'];
@endphp

<div class="space-y-3 lg:hidden">
    @forelse($staff as $member)
        <div class="bg-white p-4 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate font-medium text-secondary-shade dark:text-white">{{ $member->name }}</p>
                    <p class="truncate text-xs text-grey dark:text-white/50">{{ $member->email }}</p>
                </div>
                <span class="shrink-0 text-xs text-secondary-shade dark:text-white">{{ $roleLabels[$member->role->value] ?? $member->role->value }}</span>
            </div>
            @can('delete', $member)
                <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Supprimer ce compte ?');" class="mt-3 border-t border-secondary-shade/10 pt-3 text-right dark:border-white/10">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-primary hover:underline">Supprimer</button>
                </form>
            @endcan
        </div>
    @empty
        <div class="bg-white px-6 py-10 text-center text-sm text-grey shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:text-white/40 dark:ring-white/5">Aucun membre du staff.</div>
    @endforelse
</div>

<div class="hidden overflow-x-auto bg-white shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5 lg:block">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey dark:border-white/10 dark:text-white/40">
                <th class="px-6 py-4 font-medium">Nom</th>
                <th class="px-6 py-4 font-medium">Email</th>
                <th class="px-6 py-4 font-medium">Rôle</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10 dark:divide-white/10">
            @foreach($staff as $member)
                <tr>
                    <td class="px-6 py-4 font-medium text-secondary-shade dark:text-white">{{ $member->name }}</td>
                    <td class="px-6 py-4 text-grey dark:text-white/50">{{ $member->email }}</td>
                    <td class="px-6 py-4 text-secondary-shade dark:text-white">{{ $roleLabels[$member->role->value] ?? $member->role->value }}</td>
                    <td class="px-6 py-4 text-right">
                        @can('delete', $member)
                            <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Supprimer ce compte ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-primary hover:underline">Supprimer</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
