<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login');
}
</script>

<template>
    <main class="flex min-h-screen items-center justify-center bg-stone-100 px-4 py-12 text-stone-900">
        <section class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl shadow-stone-900/5 sm:p-10">
            <a href="/" class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Raffle Desk</a>
            <h1 class="mt-5 text-3xl font-bold tracking-tight">Acceso del propietario</h1>
            <p class="mt-2 text-sm leading-6 text-stone-600">Inicia sesión para administrar las ventas de la rifa.</p>

            <form class="mt-8 space-y-5" @submit.prevent="submit">
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium">Correo electrónico</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="username"
                        required
                        autofocus
                        class="w-full rounded-xl border border-stone-300 px-4 py-3 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10"
                    >
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-700">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium">Contraseña</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full rounded-xl border border-stone-300 px-4 py-3 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10"
                    >
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-700">{{ form.errors.password }}</p>
                </div>

                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input v-model="form.remember" type="checkbox" class="rounded border-stone-300 text-emerald-700 focus:ring-emerald-600">
                    Mantener la sesión iniciada
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl bg-emerald-700 px-4 py-3 font-semibold text-white transition hover:bg-emerald-800 disabled:cursor-wait disabled:opacity-60"
                >
                    {{ form.processing ? 'Ingresando…' : 'Iniciar sesión' }}
                </button>
            </form>
        </section>
    </main>
</template>
