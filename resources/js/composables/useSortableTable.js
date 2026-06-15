import { computed, ref } from 'vue'

const collator = new Intl.Collator(undefined, {
    numeric: true,
    sensitivity: 'base',
})

const resolvePath = (row, path) => path
    .split('.')
    .reduce((value, segment) => value?.[segment], row)

const normalizeValue = (value) => {
    if (value === null || value === undefined || value === '') {
        return null
    }

    if (typeof value === 'boolean') {
        return value ? 1 : 0
    }

    if (typeof value === 'number') {
        return value
    }

    if (value instanceof Date) {
        return value.getTime()
    }

    if (typeof value === 'string') {
        const trimmed = value.trim()
        const numericValue = Number(trimmed)

        if (trimmed !== '' && !Number.isNaN(numericValue)) {
            return numericValue
        }

        const timestamp = Date.parse(trimmed)
        if (!Number.isNaN(timestamp) && /^\d{4}-\d{2}-\d{2}/.test(trimmed)) {
            return timestamp
        }

        return trimmed
    }

    return String(value)
}

export const useSortableTable = (rows, options = {}) => {
    const {
        initialKey = 'id',
        initialDirection = 'desc',
        accessors = {},
    } = options

    const sortKey = ref(initialKey)
    const sortDirection = ref(initialDirection)

    const getCellValue = (row, key) => {
        const accessor = accessors[key]

        if (typeof accessor === 'function') {
            return accessor(row)
        }

        if (typeof accessor === 'string') {
            return resolvePath(row, accessor)
        }

        return resolvePath(row, key)
    }

    const sortedRows = computed(() => {
        const sourceRows = Array.isArray(rows.value) ? [...rows.value] : []

        return sourceRows.sort((leftRow, rightRow) => {
            const leftValue = normalizeValue(getCellValue(leftRow, sortKey.value))
            const rightValue = normalizeValue(getCellValue(rightRow, sortKey.value))

            if (leftValue === rightValue) {
                return 0
            }

            if (leftValue === null) {
                return 1
            }

            if (rightValue === null) {
                return -1
            }

            let comparison = 0

            if (typeof leftValue === 'number' && typeof rightValue === 'number') {
                comparison = leftValue - rightValue
            } else {
                comparison = collator.compare(String(leftValue), String(rightValue))
            }

            return sortDirection.value === 'asc' ? comparison : -comparison
        })
    })

    const setSort = (key) => {
        if (sortKey.value === key) {
            sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
            return
        }

        sortKey.value = key
        sortDirection.value = 'asc'
    }

    const isSortedBy = (key) => sortKey.value === key

    return {
        sortKey,
        sortDirection,
        sortedRows,
        setSort,
        isSortedBy,
    }
}
