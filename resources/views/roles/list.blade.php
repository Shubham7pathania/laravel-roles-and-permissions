<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Roles') }}
            </h2>
            <a href="{{ route('roles.create') }}" class="bg-slate-700 text-sm rounded-md px-3 py-2 text-white">Create</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">#</th>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Permissions</th>
                        <th class="px-6 py-3 text-left" width="180">Created</th>
                        <th class="px-6 py-3 text-center" width="180">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($roles->isNotEmpty())
                        @foreach ($roles as $role)
                        <tr class="border-b">
                            <td class="px-6 py-3 text-left">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-3 text-left">
                                {{ $role->name }}
                            </td>
                            <td class="px-6 py-3 text-left">
                                {{ $role->permissions->pluck('name')->implode(', ') }}
                            </td>
                            <td class="px-6 py-3 text-left">
                                {{ \Carbon\Carbon::parse($role->created_at)->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                @can('edit roles')
                                <a href="{{ route('roles.edit', [ 'id' => $role->id]) }}" class="bg-slate-700 hover:bg-slate-600 text-sm rounded-md px-3 py-2 text-white">Edit</a>
                                @endcan

                                @can('delete roles')
                                <a href="{{ route('roles.destroy',$role->id) }}" class="bg-red-700 hover:bg-red-600 text-sm rounded-md px-3 py-2 text-white">Delete</a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="my-3">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
