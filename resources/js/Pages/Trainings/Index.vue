<template>
    <AppLayout>
        <div class="calendar-page">
            <h2 class="calendar-page__title">Розклад тренувань</h2>

            <a v-if="isAdmin" href="/trainings/create" class="calendar-page__create-link">
                Додати тренування
            </a>

            <div class="calendar">
                <div class="calendar__header">
                    <button @click="prevMonth">‹</button>
                    <h3>{{ monthName }} {{ currentYear }}</h3>
                    <button @click="nextMonth">›</button>
                </div>

                <div class="calendar__grid">
                    <div class="calendar__weekday" v-for="d in weekdays" :key="d">
                        {{ d }}
                    </div>

                    <div
                        v-for="cell in calendarCells"
                        :key="cell.dateKey"
                        class="calendar__cell"
                        :class="{ 'calendar__cell--today': cell.isToday }"
                    >
                        <div class="calendar__date">{{ cell.day }}</div>

                        <div
                            v-for="training in cell.trainings"
                            :key="training.id"
                            class="calendar__event"
                            @click="openTraining(training)"
                        >
                            <b>{{ training.time }}</b>
                            — {{ training.sport.name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, computed} from 'vue'
import {usePage, router} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

/********************************
 *     PROPS (Важная часть!)
 ********************************/
const props = defineProps({
    trainings: {type: Array, required: true},
})

console.log(props.now)

/********************************
 *         AUTH
 ********************************/
const page = usePage()
const authUser = computed(() => page.props.auth?.user ?? null)
const isAdmin = computed(() => authUser.value?.role === 'admin')

function openTraining(training) {
    router.visit(route('trainings.show', training.id))
}

/********************************
 *         CALENDAR
 ********************************/
const today = new Date()
const currentMonth = ref(today.getMonth())
const currentYear = ref(today.getFullYear())

const weekdays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Нд']

const monthName = computed(() =>
    new Date(currentYear.value, currentMonth.value).toLocaleString('uk-UA', {
        month: 'long'
    })
)

function prevMonth() {
    if (currentMonth.value === 0) {
        currentMonth.value = 11
        currentYear.value--
    } else currentMonth.value--
}

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0
        currentYear.value++
    } else currentMonth.value++
}

/********************************
 *        FIXED CALENDAR CELLS
 ********************************/
const calendarCells = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value, 1)
    const startDay = (firstDay.getDay() + 6) % 7 // Понеділок = 0

    const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate()
    const cells = []

    // Пустые клетки перед 1 днем месяца
    for (let i = 0; i < startDay; i++) {
        cells.push({
            day: '',
            trainings: [],
            dateKey: 'empty-' + i
        })
    }

    // Дни месяца
    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${currentYear.value}-${(currentMonth.value + 1)
            .toString()
            .padStart(2, '0')}-${d.toString().padStart(2, '0')}`

        cells.push({
            day: d,
            dateKey: dateStr,
            isToday:
                d === today.getDate() &&
                currentMonth.value === today.getMonth() &&
                currentYear.value === today.getFullYear(),

            // ✔️ ПРАВИЛЬНОЕ ОБРАЩЕНИЕ К ТРЕНИРОВКАМ
            trainings: props.trainings.filter(t => t.date === dateStr)
        })
    }

    return cells
})
</script>

<style scoped>
.calendar-page {
    max-width: 900px;
    margin: auto;
}

.calendar-page__title {
    font-size: 28px;
    margin-bottom: 20px;
}

.calendar-page__create-link {
    display: inline-block;
    margin-bottom: 20px;
    background: #4CAF50;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
}

.calendar {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.calendar__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.calendar__grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}

.calendar__weekday {
    text-align: center;
    font-weight: bold;
    padding: 6px;
}

.calendar__cell {
    min-height: 95px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 6px;
    background: #fafafa;
    font-size: 14px;
}

.calendar__cell--today {
    border-color: #3b82f6;
    box-shadow: 0 0 3px #3b82f6;
}

.calendar__date {
    font-weight: bold;
    margin-bottom: 4px;
}

.calendar__event {
    background: #cfe3ff;
    padding: 4px 6px;
    border-radius: 4px;
    margin-bottom: 3px;
    cursor: pointer;
    transition: 0.2s;
}

.calendar__event:hover {
    background: #a5c8ff;
}

/* Buttons */
.btn {
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid #777;
}

.btn--primary {
    background: #2563eb;
    color: white;
}
</style>
