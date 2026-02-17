<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

defineProps({
    changeRequests: Array,
});

const status = usePage().props.flash?.status;
const canViewAudit = usePage().props.auth?.can?.viewAuditLogs ?? false;

function deleteChangeRequest(id) {
    if (confirm('Are you sure you want to delete this change request?')) {
        router.delete(route('change-requests.destroy', id));
    }
}

function submitChangeRequest(id) {
    if (confirm('Submit this change request for approval? You will not be able to edit it after submitting.')) {
        router.post(route('change-requests.submit', id));
    }
}
</script>

<template>
    <Head title="Change Requests" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Change Requests
                    </h2>
                    <Link
                        :href="route('change-requests.pending-approval')"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Pending my approval
                    </Link>
                </div>
                <Link
                    :href="route('change-requests.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                >
                    New Change Request
                </Link>
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
                            No change requests yet.
                            <Link
                                :href="route('change-requests.create')"
                                class="ml-1 text-indigo-600 hover:text-indigo-500"
                            >
                                Create one
                            </Link>
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
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        Created
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
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span
                                            :class="{
                                                'rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800': request.status === 'draft',
                                                'rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800': request.status === 'submitted',
                                                'rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800': request.status === 'approved',
                                                'rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800': request.status === 'processing',
                                                'rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800': request.status === 'completed',
                                            }"
                                        >
                                            {{ request.status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ new Date(request.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <Link
                                            v-if="canViewAudit"
                                            :href="route('change-requests.audit', request.id)"
                                            class="text-gray-600 hover:text-gray-900"
                                        >
                                            Audit
                                        </Link>
                                        <template v-if="request.status === 'draft'">
                                            <Link
                                                :href="route('change-requests.edit', request.id)"
                                                :class="canViewAudit ? 'ml-4' : ''"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                class="ml-4 text-indigo-600 hover:text-indigo-900"
                                                @click="submitChangeRequest(request.id)"
                                            >
                                                Submit
                                            </button>
                                            <button
                                                type="button"
                                                class="ml-4 text-red-600 hover:text-red-900"
                                                @click="deleteChangeRequest(request.id)"
                                            >
                                                Delete
                                            </button>
                                        </template>
                                        <template v-else-if="request.status === 'submitted'">
                                            <span
                                                :class="canViewAudit ? 'ml-4' : ''"
                                                class="text-gray-400"
                                            >
                                                Awaiting approval
                                            </span>
                                        </template>
                                        <template v-else-if="request.status === 'approved' || request.status === 'processing'">
                                            <span
                                                :class="canViewAudit ? 'ml-4' : ''"
                                                class="text-gray-400"
                                            >
                                                In progress
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span
                                                :class="canViewAudit ? 'ml-4' : ''"
                                                class="text-gray-400"
                                            >
                                                Completed
                                            </span>
                                        </template>
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
