<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

defineProps({
    changeRequests: Array,
});

const status = usePage().props.flash?.status;

function approveChangeRequest(id) {
    if (confirm('Approve this change request?')) {
        router.post(route('change-requests.approve', id));
    }
}
</script>

<template>
    <Head title="Pending Approval" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('change-requests.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ← Back
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Pending my approval
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    v-if="status"
                    class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700"
                >
                    {{ status }}
                </div>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div
                            v-if="changeRequests.length === 0"
                            class="text-center text-gray-500"
                        >
                            No change requests pending your approval.
                        </div>

                        <table
                            v-else
                            class="min-w-full divide-y divide-gray-200"
                        >
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Title
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Description
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Requested by
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Submitted
                                    </th>
                                    <th
                                        scope="col"
                                        class="relative px-6 py-3"
                                    >
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="request in changeRequests"
                                    :key="request.id"
                                >
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ request.title }}
                                    </td>
                                    <td class="max-w-xs truncate px-6 py-4 text-sm text-gray-500">
                                        {{ request.description || '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ request.user?.name ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ new Date(request.updated_at).toLocaleDateString() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <button
                                            type="button"
                                            class="text-green-600 hover:text-green-900"
                                            @click="approveChangeRequest(request.id)"
                                        >
                                            Approve
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
