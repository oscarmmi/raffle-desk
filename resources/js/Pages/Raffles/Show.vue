<script setup>
import { computed, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    raffle: { type: Object, required: true },
    is_owner: { type: Boolean, required: true },
});

const selectedNumber = ref(null);
const saleForm = useForm({ buyer_name: '', buyer_phone: '' });

const counts = computed(() => ({
    available: props.raffle.numbers.filter((number) => number.status === 'available').length,
    reserved: props.raffle.numbers.filter((number) => number.status === 'reserved').length,
    sold: props.raffle.numbers.filter((number) => number.status === 'sold').length,
}));

const soldNumbers = computed(() => props.raffle.numbers.filter((number) => number.status === 'sold'));

function chooseNumber(number) {
    if (!props.is_owner || number.status !== 'available') return;

    selectedNumber.value = number;
    saleForm.clearErrors();
}

function sellNumber() {
    if (!selectedNumber.value) return;

    saleForm.patch(`/owner/numbers/${selectedNumber.value.id}/sell`, {
        onSuccess: () => {
            selectedNumber.value = null;
            saleForm.reset();
        },
    });
}

function logout() {
    router.post('/logout');
}

function drawWinner() {
    router.post('/owner/draw');
}
</script>

<template>
    <main class="min-h-screen bg-stone-100 text-stone-900">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-5 sm:px-6 lg:px-8">
                <a href="/" class="text-sm font-bold uppercase tracking-[0.2em] text-emerald-700">Raffle Desk</a>
                <div class="flex items-center gap-3">
                    <template v-if="is_owner">
                        <span class="hidden text-sm font-medium text-stone-600 sm:inline">Vista del propietario</span>
                        <button class="rounded-lg border border-stone-300 px-3 py-2 text-sm font-semibold hover:bg-stone-50" type="button" @click="logout">Cerrar sesión</button>
                    </template>
                    <a v-else href="/login" class="rounded-lg border border-stone-300 px-3 py-2 text-sm font-semibold hover:bg-stone-50">Acceso del propietario</a>
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <section class="mb-8 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700">Rifa activa</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ raffle.name }}</h1>
                    <p class="mt-3 max-w-xl text-stone-600">Elige tu número favorito y consulta su disponibilidad. Los números vendidos se muestran en dorado.</p>
                </div>
                <div class="grid grid-cols-3 gap-2 sm:min-w-[330px]">
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium text-stone-500">Disponibles</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-700">{{ counts.available }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium text-stone-500">Apartados</p>
                        <p class="mt-1 text-2xl font-bold text-amber-700">{{ counts.reserved }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium text-stone-500">Vendidos</p>
                        <p class="mt-1 text-2xl font-bold text-stone-800">{{ counts.sold }}</p>
                    </div>
                </div>
            </section>

            <p v-if="$page.props.flash?.success" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ $page.props.flash.success }}
            </p>

            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
                <section class="rounded-3xl bg-white p-4 shadow-sm sm:p-6" aria-labelledby="number-grid-heading">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 id="number-grid-heading" class="text-lg font-bold">Números del 00 al 99</h2>
                            <p class="mt-1 text-sm text-stone-500">{{ is_owner ? 'Selecciona un número disponible para registrarlo como vendido.' : 'Consulta los números disponibles antes de participar.' }}</p>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs font-medium text-stone-600">
                            <span class="inline-flex items-center gap-2"><i class="h-3 w-3 rounded bg-emerald-100 ring-1 ring-emerald-200"></i>Disponible</span>
                            <span class="inline-flex items-center gap-2"><i class="h-3 w-3 rounded bg-amber-100 ring-1 ring-amber-200"></i>Apartado</span>
                            <span class="inline-flex items-center gap-2"><i class="h-3 w-3 rounded bg-stone-800"></i>Vendido</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-2 sm:grid-cols-10 sm:gap-2.5">
                        <button
                            v-for="number in raffle.numbers"
                            :key="number.id"
                            type="button"
                            :disabled="!is_owner || number.status !== 'available'"
                            :aria-label="`Número ${number.display_number}, ${number.status === 'available' ? 'disponible' : number.status === 'sold' ? 'vendido' : 'apartado'}`"
                            :aria-pressed="selectedNumber?.id === number.id"
                            @click="chooseNumber(number)"
                            class="aspect-square rounded-xl text-sm font-bold transition sm:text-base"
                            :class="{
                                'bg-emerald-50 text-emerald-900 ring-1 ring-emerald-200 hover:bg-emerald-100': number.status === 'available' && selectedNumber?.id !== number.id,
                                'bg-emerald-700 text-white ring-2 ring-emerald-900 ring-offset-2': number.status === 'available' && selectedNumber?.id === number.id,
                                'bg-amber-100 text-amber-900 ring-1 ring-amber-200': number.status === 'reserved',
                                'bg-stone-800 text-white': number.status === 'sold',
                                'cursor-pointer': is_owner && number.status === 'available',
                                'cursor-default': !is_owner || number.status !== 'available',
                            }"
                        >{{ number.display_number }}</button>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-3xl bg-stone-900 p-5 text-white shadow-sm sm:p-6" aria-labelledby="draw-heading">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-300">Resultado</p>
                        <h2 id="draw-heading" class="mt-2 text-lg font-bold">Sorteo de la rifa</h2>
                        <div v-if="raffle.draw.winner_number" class="mt-4 rounded-2xl bg-white/10 p-4">
                            <p class="text-sm text-stone-300">Número ganador</p>
                            <p class="mt-1 text-4xl font-black tracking-tight text-emerald-300">{{ raffle.draw.winner_number }}</p>
                        </div>
                        <template v-else>
                            <p class="mt-2 text-sm leading-6 text-stone-300">El ganador se elige al azar entre los números vendidos.</p>
                            <button v-if="is_owner" type="button" :disabled="soldNumbers.length === 0" class="mt-5 w-full rounded-xl bg-emerald-400 px-4 py-3 font-bold text-stone-950 transition hover:bg-emerald-300 disabled:cursor-not-allowed disabled:opacity-50" @click="drawWinner">
                                Realizar sorteo
                            </button>
                            <p v-else class="mt-4 text-sm text-stone-400">El sorteo todavía no se ha realizado.</p>
                        </template>
                    </section>

                    <section v-if="is_owner" class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" aria-labelledby="sale-form-heading">
                        <h2 id="sale-form-heading" class="text-lg font-bold">Registrar venta</h2>
                        <p v-if="selectedNumber" class="mt-1 text-sm text-stone-600">Número <strong>{{ selectedNumber.display_number }}</strong></p>
                        <p v-else class="mt-1 text-sm text-stone-600">Selecciona un número disponible en la cuadrícula.</p>

                        <form class="mt-5 space-y-4" @submit.prevent="sellNumber">
                            <div>
                                <label for="buyer-name" class="mb-1.5 block text-sm font-medium">Nombre del comprador</label>
                                <input id="buyer-name" v-model="saleForm.buyer_name" required maxlength="255" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10" :disabled="!selectedNumber">
                                <p v-if="saleForm.errors.buyer_name" class="mt-1.5 text-sm text-red-700">{{ saleForm.errors.buyer_name }}</p>
                            </div>
                            <div>
                                <label for="buyer-phone" class="mb-1.5 block text-sm font-medium">Teléfono</label>
                                <input id="buyer-phone" v-model="saleForm.buyer_phone" required maxlength="40" inputmode="tel" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10" :disabled="!selectedNumber">
                                <p v-if="saleForm.errors.buyer_phone" class="mt-1.5 text-sm text-red-700">{{ saleForm.errors.buyer_phone }}</p>
                            </div>
                            <button type="submit" :disabled="!selectedNumber || saleForm.processing" class="w-full rounded-xl bg-emerald-700 px-4 py-3 font-semibold text-white transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-50">
                                {{ saleForm.processing ? 'Guardando…' : 'Marcar como vendido' }}
                            </button>
                        </form>
                    </section>

                    <section v-if="is_owner" class="rounded-3xl bg-white p-5 shadow-sm sm:p-6" aria-labelledby="buyer-list-heading">
                        <div class="flex items-center justify-between gap-3">
                            <h2 id="buyer-list-heading" class="text-lg font-bold">Compradores</h2>
                            <span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600">{{ soldNumbers.length }}</span>
                        </div>
                        <ul v-if="soldNumbers.length" class="mt-4 divide-y divide-stone-100">
                            <li v-for="number in soldNumbers" :key="number.id" class="flex items-start justify-between gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold">{{ number.buyer_name || 'Sin nombre' }}</p>
                                    <p class="mt-0.5 text-sm text-stone-500">{{ number.buyer_phone || 'Sin teléfono' }}</p>
                                </div>
                                <span class="shrink-0 rounded-lg bg-stone-100 px-2 py-1 text-xs font-bold tabular-nums">{{ number.display_number }}</span>
                            </li>
                        </ul>
                        <p v-else class="mt-4 text-sm text-stone-500">Todavía no hay números vendidos.</p>
                    </section>

                    <p v-else class="rounded-2xl border border-stone-200 bg-white p-5 text-sm leading-6 text-stone-600">
                        La información de compradores solo está disponible para el propietario.
                    </p>
                </aside>
            </div>
        </div>
    </main>
</template>
