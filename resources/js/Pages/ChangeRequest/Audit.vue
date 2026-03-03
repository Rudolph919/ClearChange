<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const AUDIT_FIELD_ORDER = ['status', 'title', 'description'];

function orderedKeys(values) {
    if (!values || typeof values !== 'object') return [];
    return AUDIT_FIELD_ORDER.filter((k) => values && Object.prototype.hasOwnProperty.call(values, k));
}

defineProps({
    changeRequest: Object,
    auditLogs: Array,
});
</script>

<template>
    <Head title="Audit Log" />

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
                    Audit trail: {{ changeRequest?.title }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div
                            v-if="auditLogs.length === 0"
                            class="text-center text-gray-500"
                        >
                            No audit entries yet.
                        </div>

                        <div
                            v-else
                            class="space-y-6"
                        >
                            <div
                                v-for="(log, index) in auditLogs"
                                :key="log.id"
                                class="relative flex gap-4"
                            >
                                <div
                                    v-if="index < auditLogs.length - 1"
                                    class="absolute left-[11px] top-6 h-full w-0.5 bg-gray-200"
                                    aria-hidden="true"
                                />
                                <div class="relative flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-100">
                                    <span
                                        v-if="log.action === 'created'"
                                        class="text-xs font-medium text-green-600"
                                    >
                                        +
                                    </span>
                                    <span
                                        v-else
                                        class="text-xs font-medium text-blue-600"
                                    >
                                        ~
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        <span v-if="log.action === 'created'">Change request created</span>
                                        <span v-else>Change request updated</span>
                                    </p>
                                    <div class="mt-2 rounded-md border border-gray-200 bg-white p-3 text-sm shadow-sm">
                                        <div class="space-y-1.5">
                                            <div>
                                                <span class="font-medium text-gray-600">User: </span>
                                                <span class="text-gray-900">{{ log.user?.name ?? 'System' }}</span>
                                                <span class="text-xs text-gray-500"> @ {{ new Date(log.created_at).toLocaleString() }}</span>
                                            </div>
                                            <template v-if="log.action === 'updated' && log.new_values">
                                                <div
                                                    v-for="key in orderedKeys(log.new_values)"
                                                    :key="key"
                                                    class="flex flex-wrap gap-2 pt-1"
                                                >
                                                    <span class="font-medium text-gray-600 capitalize">{{ key }}:</span>
                                                    <span
                                                        v-if="log.old_values && log.old_values[key] !== log.new_values[key]"
                                                        class="text-red-600 line-through"
                                                    >
                                                        {{ log.old_values[key] ?? '—' }}
                                                    </span>
                                                    <span
                                                        v-if="log.old_values && log.old_values[key] !== log.new_values[key]"
                                                        class="text-green-600"
                                                    >
                                                        → {{ log.new_values[key] }}
                                                    </span>
                                                    <span
                                                        v-else
                                                        class="text-gray-700"
                                                    >
                                                        {{ log.new_values[key] }}
                                                    </span>
                                                </div>
                                            </template>
                                            <template v-else-if="log.action === 'created' && log.new_values">
                                                <div
                                                    v-for="key in orderedKeys(log.new_values)"
                                                    :key="key"
                                                    class="flex flex-wrap gap-2 pt-1"
                                                >
                                                    <span class="font-medium text-gray-600 capitalize">{{ key }}:</span>
                                                    <span class="text-gray-700">{{ log.new_values[key] ?? '—' }}</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
