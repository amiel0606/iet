import axios from "axios";

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

    if (Object.keys(errors.value).length > 0) {
        return;
    }

    try {
        await axios.post("/register", registerData.value);
        alert("Registration successful!");
        showRegister.value = false;
    } catch (error) {
        alert(error.response?.data?.message || "Error registering user");
    }
};

export const loginUser = async (
    loginData,
    user,
    isLoggedIn,
    loginError,
    showLogin
) => {
    loginError.value = "";
    try {
        const response = await axios.post("/login", loginData.value);
        user.value = response.data.user;
        isLoggedIn.value = true;
        showLogin.value = false;
    } catch (error) {
        loginError.value =
            error.response?.data?.message || "Invalid credentials";
    }
};

export const logoutUser = (user, isLoggedIn) => {
    user.value = null;
    isLoggedIn.value = false;
};
