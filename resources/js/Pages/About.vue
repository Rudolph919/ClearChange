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
    <Head title="About – ClearChange" />
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-3xl px-6 py-16 sm:py-24">
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

            <main class="mt-16">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                    About ClearChange
                </h1>
                <p class="mt-4 text-lg text-slate-600">
                    ClearChange demonstrates a structured, auditable workflow for capturing,
                    reviewing, approving, and processing sensitive data changes. It mirrors
                    real-world business systems found in payroll, HR, finance, and regulated
                    environments.
                </p>

                <section class="mt-12">
                    <h2 class="text-xl font-semibold text-slate-900">
                        How it works
                    </h2>
                    <ol class="mt-6 space-y-6">
                        <li class="flex gap-4">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
                            >
                                1
                            </span>
                            <div>
                                <strong class="text-slate-900">Create</strong>
                                <p class="mt-1 text-slate-600">
                                    Create a draft with title and description. Changes are stored as
                                    immutable items (field, old value, new value). Edit uses Current →
                                    Proposed for revisions.
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
                            >
                                2
                            </span>
                            <div>
                                <strong class="text-slate-900">Submit</strong>
                                <p class="mt-1 text-slate-600">
                                    The owner submits the draft for approval. Once submitted, it
                                    cannot be edited until approved or rejected.
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
                            >
                                3
                            </span>
                            <div>
                                <strong class="text-slate-900">Approve</strong>
                                <p class="mt-1 text-slate-600">
                                    Users with the admin role see pending requests under "Pending my
                                    approval" and can approve or reject them.
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
                            >
                                4
                            </span>
                            <div>
                                <strong class="text-slate-900">Process</strong>
                                <p class="mt-1 text-slate-600">
                                    Approved requests are queued for background processing. The job
                                    iterates over change items and applies them. Status moves from
                                    approved → processing → completed (or failed).
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
                            >
                                5
                            </span>
                            <div>
                                <strong class="text-slate-900">Retry</strong>
                                <p class="mt-1 text-slate-600">
                                    If processing fails, the owner sees a Retry button. One click
                                    re-queues the request. No partial updates—transactions keep data
                                    consistent.
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
                            >
                                6
                            </span>
                            <div>
                                <strong class="text-slate-900">Audit</strong>
                                <p class="mt-1 text-slate-600">
                                    Every status change and field update is logged. Users with
                                    permission can view the full audit trail for any change request.
                                    Job-run transitions are attributed to "System".
                                </p>
                            </div>
                        </li>
                    </ol>
                </section>

                <section class="mt-12">
                    <h2 class="text-xl font-semibold text-slate-900">
                        Roles & permissions
                    </h2>
                    <ul class="mt-4 space-y-2 text-slate-600">
                        <li>
                            <strong class="text-slate-800">user</strong> — Create, edit, submit, and
                            retry change requests. View audit logs.
                        </li>
                        <li>
                            <strong class="text-slate-800">admin</strong> — All user permissions, plus
                            approve submitted change requests.
                        </li>
                    </ul>
                </section>

                <section class="mt-12">
                    <h2 class="text-xl font-semibold text-slate-900">
                        Tech stack
                    </h2>
                    <p class="mt-4 text-slate-600">
                        Laravel 12, Vue 3 (Composition API), Inertia.js, Tailwind CSS, Spatie
                        Laravel Permission, MySQL, Laravel Queues & Jobs.
                    </p>
                </section>

                <div class="mt-12">
                    <Link
                        :href="route('welcome')"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        ← Back to home
                    </Link>
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
