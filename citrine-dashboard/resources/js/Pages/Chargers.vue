<template>
  <div class="p-6">
    <header class="mb-6">
      <h1 class="text-2xl font-bold text-black dark:text-white">Chargers</h1>
      <p class="text-sm text-gray-500">List of chargers and their status</p>
    </header>

    <div class="grid gap-4">
      <div v-if="chargersList.length" class="space-y-3">
        <div
          v-for="charger in chargersList"
          :key="charger.id || charger.name || charger.serial || chargerIndex"
          class="flex items-center justify-between rounded-md border border-gray-100 p-3 bg-white shadow dark:bg-zinc-900"
        >
          <div class="flex items-center gap-3">
            <div :class="['h-9 w-9 rounded-md flex items-center justify-center', charger.online ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700']">⚡</div>
            <div>
              <p class="font-medium">{{ charger.name ?? `Charger ${charger.id ?? '?'}` }}</p>
              <p class="text-sm text-gray-500">Location: {{ charger.location ?? 'Unknown' }}</p>
            </div>
          </div>
          <div :class="charger.online ? 'text-green-600 text-sm' : 'text-red-600 text-sm'">{{ charger.online ? 'Online' : 'Offline' }}</div>
        </div>
      </div>

      <div v-else class="text-gray-600">No chargers available.</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  chargers: {
    type: [Array, Object],
    default: () => [],
  },
})

const chargersList = computed(() => {
  if (Array.isArray(props.chargers)) return props.chargers
  if (props.chargers && typeof props.chargers === 'object') return Object.values(props.chargers)
  return []
})
</script>
