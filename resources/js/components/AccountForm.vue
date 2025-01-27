<template>
    <div class="form-container">
        <form @submit.prevent="handleSubmit">
            <div>
                <label for="accountName">Account name:</label>
                <input
                    type="text"
                    id="accountName"
                    v-model="form.accountName"
                    required
                />
            </div>

            <div>
                <label for="accountWebsite">Account website:</label>
                <input
                    type="url"
                    id="accountWebsite"
                    v-model="form.accountWebsite"
                    required
                />
            </div>

            <div>
                <label for="accountPhone">Account phone:</label>
                <input
                    type="text"
                    id="accountPhone"
                    v-model="form.accountPhone"
                    @input="validatePhone"
                    required
                />
            </div>

            <div>
                <label for="dealName">Deal name:</label>
                <input
                    type="text"
                    id="dealName"
                    v-model="form.dealName"
                    required
                />
            </div>

            <div>
                <label for="dealStage">Deal stage:</label>
                <input
                    type="text"
                    id="dealStage"
                    v-model="form.dealStage"
                    required
                />
            </div>

            <button type="submit" :disabled="formSubmitted">
                <span v-if="formSubmitted">Loading...</span>
                <span v-else>Create</span>
            </button>
        </form>

        <div v-if="successMessage" class="success-message">
            {{ successMessage }}
        </div>

        <div v-else-if="errorBlock.message" class="error-message">
            {{ errorBlock.message }}
            <div v-if="Object.keys(errorBlock.list).length">
                <ul>
                    <li v-for="(error) in errorBlock.list">
                        <span v-for="e in error">{{ e }}</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            form: {
                accountName: '',
                accountWebsite: '',
                accountPhone: '',

                dealName: '',
                dealStage: '',
            },
            formSubmitted: false,
            successMessage: undefined,
            errorBlock: {
                message: undefined,
                list: [],
            }
        };
    },
    methods: {
        validatePhone() {
            const phonePattern = /^\+380\d{9}$/;
            if (this.form.accountPhone && !phonePattern.test(this.form.accountPhone)) {
                this.errorBlock.message = 'Enter the phone in the given format: +380XXXXXXXXX';
            } else {
                this.errorBlock.message = undefined;
            }
        },
        async handleSubmit() {
            let response;
            try {
                this.formSubmitted = true;

                this.errorBlock = {
                    message: undefined,
                    list: [],
                };
                this.successMessage = undefined;

                response = await axios.post('/api/createRecords', this.form);

                this.form.accountName = '';
                this.form.accountWebsite = '';
                this.form.accountPhone = '';

                this.form.dealStage = '';
                this.form.dealName = '';

                if (response.data.result === 'success') {
                    this.successMessage = response.data.message;
                }
            } catch (error) {
                this.errorBlock.message = error.response.data.message || 'No error message.';

                this.errorBlock.list = error.response.data.errors || []; //Laravel validation errors
            } finally {
                this.formSubmitted = false;
            }
        }
    }
};
</script>

<style scoped>
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-container {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 8px;
    font-weight: bold;
}

input {
    margin-bottom: 16px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button {
    padding: 10px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}

.success-message {
    margin-top: 20px;
    padding: 10px;
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
}
.error-message {
    margin-top: 20px;
    padding: 10px;
    background-color: #edd4d4;
    color: #571515;
    border: 1px solid #e6c3c3;
    border-radius: 4px;
}
</style>
