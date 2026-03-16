<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-md px-6 py-16 sm:py-24">
            <header class="mb-8">
                <Link
                    :href="route('welcome')"
                    class="flex items-center gap-2 text-xl font-semibold tracking-tight text-slate-800 hover:text-slate-600"
                >
                    <img src="/favicon.svg" alt="" class="h-8 w-8" />
                    ClearChange
                </Link>
            </header>

            <div
                class="w-full overflow-hidden rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
            >
                <slot />
            </div>

            <p class="mt-6 text-center text-sm text-slate-500">
                <Link :href="route('welcome')" class="text-indigo-600 hover:text-indigo-500">
                    ← Back to home
                </Link>
            </p>
        </div>
    </div>
</template>
