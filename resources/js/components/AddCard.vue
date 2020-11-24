<template>
<div>
    <modal name="my-first-modal">
    <form @submit.prevent="handleAddNewCard" class="p-4">
        <input
            class="block form-input w-100"
            name="title" type="text"
            placeholder="Enter a title"
            v-model.trim="newCard.title"
        >
        <textarea name="description"
                  class="block form-input w-100"
                  placeholder="Add a description (optional)" rows="2"
                  v-model.trim="newCard.description"
        ></textarea>
        <div v-show="errorMessage">
        <span class="text-xs text-red-500">
          {{ errorMessage }}
        </span>
        </div>
        <button type="submit" class="btn-primary block form-input w-100" @click.prevent="handleAddNewCard">Add</button>
    </form>
    </modal>
</div>
</template>

<script>
export default {
name: "AddCard",
    props: {
        columnId: Number
    },
    data() {
        return {
            newCard: {
                title: "",
                description: "",
                column_id: null
            },
            errorMessage: ""
        };
    },
    mounted() {
    console.log(this.columnId)
        this.show()
        this.newCard.column_id = this.columnId;
    },
    methods: {
        show () {
            this.$modal.show('my-first-modal');
        },
        hide () {
            this.$modal.hide('my-first-modal');
        },
        handleAddNewCard() {
            // Basic validation so we don't send an empty task to the server
            if (!this.newCard.title) {
                this.errorMessage = "The title field is required";
                return;
            }

            // Send new task to server
            axios
                .post("/cards", this.newCard)
                .then(res => {
                    // Tell the parent component we've added a new task and include it
                    this.$emit("card-added", res.data);
                    this.hide()
                    this.newCard.title = "";
                    this.newCard.description = "";
                })
                .catch(err => {
                    // Handle the error returned from our request
                    this.handleErrors(err);
                });
        },
        handleErrors(err) {
            if (err.response && err.response.status === 422) {
                // We have a validation error
                const errorBag = err.response.data.errors;
                if (errorBag.title) {
                    this.errorMessage = errorBag.title[0];
                } else if (errorBag.description) {
                    this.errorMessage = errorBag.description[0];
                } else {
                    this.errorMessage = err.response.message;
                }
            } else {
                // We have bigger problems
                console.log(err.response);
            }
        }
    }
}
</script>

<style scoped>

</style>
