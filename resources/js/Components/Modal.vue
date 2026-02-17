<template>
    <div class="modal" @click.self="$emit('close')">
        <div class="modal__content">
            <header class="modal__header">
                <slot name="header"></slot>
                <button class="modal__close" @click="$emit('close')">&times;</button>
            </header>

            <main class="modal__body">
                <slot name="body"></slot>
            </main>

            <footer class="modal__footer">
                <slot name="footer"></slot>
            </footer>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 50;

    &__content {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 400px;
        width: 90%;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    &__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    &__close {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
        cursor: pointer;
        color: #555;
        transition: color 0.2s ease;

        &:hover {
            color: #000;
        }
    }

    &__body {
        flex-grow: 1;
        margin-bottom: 1rem;
        font-size: 1rem;
        color: #333;
    }

    &__footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;

        button {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: background-color 0.2s ease;

            &:first-child {
                background: #f0f0f0;
                border-color: #ccc;
                color: #333;

                &:hover {
                    background: #e0e0e0;
                }
            }

            &:last-child {
                background: #007bff;
                color: white;

                &:hover {
                    background: #0056b3;
                }
            }
        }
    }
}
</style>
