<script setup>
import { ref, onMounted } from "vue";
import Modal from "../Components/Modal.vue";
import $ from "jquery";
import { registerUser, loginUser, logoutUser } from "./utils/auth"; 


const showRegister = ref(false);
const showLogin = ref(false);

const registerData = ref({ name: "", email: "", password: "" });
const loginData = ref({ email: "", password: "" });

const errors = ref({});
const user = ref(null);
const isLoggedIn = ref(false);
const loginError = ref("");

const handleRegister = () => {
    registerUser(registerData, errors, showRegister);
};

const handleLogin = () => {
    loginUser(loginData, user, isLoggedIn, loginError, showLogin);
};

const handleLogout = () => {
    logoutUser(user, isLoggedIn);
};

onMounted(() => {
    $(document).on("input", ".form-control", function () {
        $(this).removeClass("is-invalid");
    });
});
</script>

<template>
    <div class="container text-center mt-5">
        <h1>Welcome to Income-Expense Tracker</h1>
        
        <div v-if="isLoggedIn">
            <p>Welcome, {{ user?.name }}</p>
            <button class="btn btn-danger" @click="handleLogout">Logout</button>
        </div>
        <div v-else>
            <button class="btn btn-primary me-2" @click="showRegister = true">
                Register
            </button>
            <button class="btn btn-secondary" @click="showLogin = true">
                Login
            </button>
        </div>

        <!-- Register Modal -->
        <Modal :show="showRegister" @close="showRegister = false">
            <template #header>
                <h5 class="modal-title">Register</h5>
            </template>
            <template #body>
                <form @submit.prevent="handleRegister">
                    <div class="form-group">
                        <input v-model="registerData.name" class="form-control" placeholder="Name" />
                        <small class="text-danger" v-if="errors.name">{{ errors.name }}</small>
                    </div>
                    <div class="form-group">
                        <input v-model="registerData.email" type="email" class="form-control" placeholder="Email" />
                        <small class="text-danger" v-if="errors.email">{{ errors.email }}</small>
                    </div>
                    <div class="form-group">
                        <input v-model="registerData.password" type="password" class="form-control" placeholder="Password" />
                        <small class="text-danger" v-if="errors.password">{{ errors.password }}</small>
                    </div>
                    <button type="submit" class="btn btn-success">Register</button>
                </form>
            </template>
        </Modal>

        <!-- Login Modal -->
        <Modal :show="showLogin" @close="showLogin = false">
            <template #header>
                <h5 class="modal-title">Login</h5>
            </template>
            <template #body>
                <form @submit.prevent="handleLogin">
                    <div class="form-group">
                        <input v-model="loginData.email" type="email" class="form-control" placeholder="Email" />
                    </div>
                    <div class="form-group">
                        <input v-model="loginData.password" type="password" class="form-control" placeholder="Password" />
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                    <br>
                    <small class="text-danger" v-if="loginError">{{ loginError }}</small>
                </form>
            </template>
        </Modal>
    </div>
</template>
