import axios from "axios";
import { router } from "@inertiajs/vue3";

export const registerUser = async (registerData, errors, showRegister) => {
    errors.value = {};
        if (!registerData.value.name) {
        errors.value.name = "Name is required.";
    }
    if (!registerData.value.email) {
        errors.value.email = "Email is required.";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(registerData.value.email)) {
        errors.value.email = "Invalid email format.";
    }
    if (!registerData.value.password) {
        errors.value.password = "Password is required.";
    } else if (registerData.value.password.length < 6) {
        errors.value.password = "Password must be at least 6 characters.";
    }

    if (Object.keys(errors.value).length > 0) return; 

    try {
        await router.post("/register", registerData.value, {
            onSuccess: () => {
                alert("Registration successful!");
                showRegister.value = false; 
                router.visit("/"); 
            },
        });
    } catch (error) {
        errors.value.general = error.response?.data?.message || "Registration failed.";
    }
};

export const loginUser = async (loginData, user, isLoggedIn, loginError, showLogin) => {
    loginError.value = "";

    try {
        await router.post("/login", loginData.value, {
            onSuccess: () => {
                isLoggedIn.value = true;
                showLogin.value = false;
                router.visit(route("dashboard")); 
            },
        });
    } catch (error) {
        loginError.value = error.response?.data?.message || "Invalid credentials.";
    }
};


export const logoutUser = async (user, isLoggedIn) => {
    try {
        await axios.post("/logout");
        user.value = null;
        isLoggedIn.value = false;
        router.visit("/"); 
    } catch (error) {
        console.error("Logout failed:", error);
    }
};
