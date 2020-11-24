<template>
    <div class="ui">
        <nav class="navbar board">
            Project Board
            <button class="btn" @click.prevent="dumpDb">export db</button>
        </nav>

        <div class="columns">
            <div class="column"
                 v-for="column in columns"
                 :key="column.slug">
                <header> {{ column.title }} </header>
                    <AddCard
                        v-if="newCardForColumn === column.id"
                        :column-id="column.id"
                        v-on:card-added="handleCardAdded"
                        v-on:card-canceled="closeAddCardForm"
                    />
                <ul>
                    <draggable
                        class="flex-1 overflow-hidden"
                        v-model="column.cards"
                        v-bind="cardDragOptions"
                        @end="handleCardMoved"
                    >
                        <transition-group
                            class="flex-1 flex flex-col h-full overflow-x-hidden overflow-y-auto rounded shadow-xs"
                            tag="div"
                        >
                            <li
                                v-for="card in column.cards"
                                :key="card.id"
                            >{{ card.title }}</li>
                        </transition-group>
                    </draggable>
                    <li v-show="!column.cards.length">
                        No cards yet
                        <button
                            class="btn"
                            @click="openAddCardForm(column.id)"
                        >Add one</button>
                    </li>
                </ul>
                <footer @click="openAddCardForm(column.id)">Add a card...</footer>
            </div>
        </div>
    </div>
</template>

<script>
import AddCard from "./AddCard";
import draggable from "vuedraggable";

export default {
    name: "Column",
    components: { draggable, AddCard },
    props: {
        initialData: Array
    },
    data() {
        return {
            columns: [],
            newCardForColumn: 0
        };
    },
    computed: {
        cardDragOptions() {
            return {
                animation: 200,
                group: "card-list",
                dragClass: "card-drag"
            };
        }
    },
    methods: {
        handleCardMoved() {
            // Send the entire list of statuses to the server
            axios.put("/cards/sync", {columns: this.columns}).catch(err => {
                console.log(err.response);
            });
        },
        openAddCardForm(columnId) {
            this.newCardForColumn = columnId;
            this.show()
        },
        closeAddCardForm() {
            this.newCardForColumn = 0;
        },
        show () {
            this.$modal.show('my-first-modal');
        },
        hide () {
            this.$modal.hide('my-first-modal');
        },
        handleCardAdded(newCard) {
            const idx = this.columns.findIndex(
                column => column.id === newCard.column_id
            );

            // Add newly created task to our column
            this.columns[idx].cards.push(newCard);

            // Reset and close the AddTaskForm
            this.closeAddCardForm();
        },
        dumpDb() {
            axios.get('/database-dump')
                .then(function(response) {
                let blob = new Blob([response.data], { type: 'application/sql' })
                let link = document.createElement('a')
                link.href = window.URL.createObjectURL(blob)
                link.download = 'sql-dump.sql'
                link.click()
            });
        }
    },
    mounted() {
        this.columns = JSON.parse(JSON.stringify(this.initialData));
    }
}
</script>

<style scoped>
.card-drag {
    transition: transform 0.5s;
    transition-property: all;
}
</style>
