<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="ClearChange – Change Request & Approval Platform" />
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-4xl px-6 py-16 sm:py-24">
            <header class="flex items-center justify-between">
                <Link
                    :href="route('welcome')"
                    class="text-xl font-semibold tracking-tight text-slate-800 hover:text-slate-600"
                >
                    ClearChange
                </Link>
                <nav v-if="canLogin" class="flex items-center gap-4">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('change-requests.index')"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                        >
                            Change Requests
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-slate-600 hover:text-slate-900"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="mt-24 text-center">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">
                    Change Request & Approval Platform
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-600">
                    Structured, auditable workflows for capturing, reviewing, and processing
                    sensitive data changes. Built for payroll, HR, finance, and regulated
                    environments.
                </p>

                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <template v-if="!$page.props.auth?.user">
                        <Link
                            :href="route('register')"
                            class="rounded-md bg-indigo-600 px-6 py-3 text-base font-medium text-white hover:bg-indigo-500"
                        >
                            Get started
                        </Link>
                        <Link
                            :href="route('login')"
                            class="rounded-md border border-slate-300 bg-white px-6 py-3 text-base font-medium text-slate-700 hover:bg-slate-50"
                        >
                            Log in
                        </Link>
                    </template>
                    <Link
                        v-else
                        :href="route('change-requests.index')"
                        class="rounded-md bg-indigo-600 px-6 py-3 text-base font-medium text-white hover:bg-indigo-500"
                    >
                        View Change Requests
                    </Link>
                </div>

                <div class="mt-20 grid gap-8 sm:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="text-sm font-medium uppercase tracking-wider text-indigo-600">
                            Workflow
                        </div>
                        <p class="mt-2 text-slate-600">
                            Draft → Submit → Approve → Process. Clear state transitions with
                            validation at every step.
                        </p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="text-sm font-medium uppercase tracking-wider text-indigo-600">
                            Audit Trail
                        </div>
                        <p class="mt-2 text-slate-600">
                            Immutable log of who changed what and when. Role-based visibility.
                        </p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="text-sm font-medium uppercase tracking-wider text-indigo-600">
                            Safe Retry
                        </div>
                        <p class="mt-2 text-slate-600">
                            Failed processing? Retry with one click. No partial updates.
                        </p>
                    </div>
                </div>
            </main>

            <footer class="mt-24 border-t border-slate-200 pt-8 text-center text-sm text-slate-500">
                <Link :href="route('about')" class="text-indigo-600 hover:text-indigo-500">
                    About
                </Link>
                <span class="mx-2">·</span>
                ClearChange – Laravel 12, Vue 3, Inertia, Tailwind
            </footer>
        </div>
    </div>
</template>
