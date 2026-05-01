<template>
    <AppLayout>
        <div class="ui-page">
            <PageHeader :title="t('trainings.title')" :description="monthLabel">
                <template #actions>
                    <AppButton type="button" variant="secondary" @click="prevMonth">‹</AppButton>
                    <AppButton type="button" variant="secondary" @click="nextMonth">›</AppButton>
                </template>
            </PageHeader>

            <AppCard soft>
                <div class="calendar">
                    <div class="calendar__weekdays">
                        <div v-for="day in weekdays" :key="day" class="calendar__weekday">{{ day }}</div>
                    </div>

                    <div class="calendar__grid">
                        <div
                            v-for="cell in calendarCells"
                            :key="cell.dateKey"
                            class="calendar__cell"
                            :class="{ 'calendar__cell--today': cell.isToday, 'calendar__cell--empty': !cell.day }"
                        >
                            <div class="calendar__date">{{ cell.day }}</div>

                            <button
                                v-for="training in cell.trainings"
                                :key="training.id"
                                type="button"
                                class="calendar__event"
                                @click="openTraining(training)"
                            >
                                <strong>{{ $formatTime(training.time) }}</strong>
                                <span>{{ training.sport.name }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </AppCard>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    trainings: { type: Array, required: true },
})

const today = new Date()
const currentMonth = ref(today.getMonth())
const currentYear = ref(today.getFullYear())
const { t, currentLang } = useI18n()

const weekdays = computed(() => t('trainings.weekdays'))

const localeMap = {
    ru: 'ru-RU',
    uk: 'uk-UA',
    en: 'en-US',
}

const monthLabel = computed(() => new Date(currentYear.value, currentMonth.value).toLocaleString(
    localeMap[currentLang.value] || 'en-US',
    { month: 'long', year: 'numeric' }
))

function openTraining(training) {
    router.visit(route('trainings.show', training.id))
}

function prevMonth() {
    if (currentMonth.value === 0) {
        currentMonth.value = 11
        currentYear.value -= 1
        return
    }

    currentMonth.value -= 1
}

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0
        currentYear.value += 1
        return
    }

    currentMonth.value += 1
}

const calendarCells = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value, 1)
    const startDay = (firstDay.getDay() + 6) % 7
    const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate()
    const cells = []

    for (let index = 0; index < startDay; index += 1) {
        cells.push({ day: '', trainings: [], dateKey: `empty-${index}`, isToday: false })
    }

    for (let day = 1; day <= daysInMonth; day += 1) {
        const dateStr = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`

        cells.push({
            day,
            dateKey: dateStr,
            isToday: day === today.getDate() && currentMonth.value === today.getMonth() && currentYear.value === today.getFullYear(),
            trainings: props.trainings.filter((training) => training.date === dateStr),
        })
    }

    return cells
})
</script>

<style scoped>
.calendar {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.calendar__weekdays,
.calendar__grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 8px;
}

.calendar__weekday {
    text-align: center;
    font-weight: 800;
    color: #587089;
}

.calendar__cell {
    min-height: 128px;
    padding: 10px;
    border: 1px solid rgba(191, 204, 220, 0.9);
    border-radius: 14px;
    background: #fff;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.calendar__cell--today {
    border-color: rgba(37, 99, 235, 0.65);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.calendar__cell--empty {
    background: #f8fafc;
}

.calendar__date {
    font-weight: 800;
}

.calendar__event {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
    width: 100%;
    padding: 8px 10px;
    border: 0;
    border-radius: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    cursor: pointer;
    text-align: left;
}

@media (max-width: 860px) {
    .calendar__weekdays,
    .calendar__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
